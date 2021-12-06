<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CampaignBannerRepositoryInterface;
use App\Repositories\CampaignRepositoryInterface;
use App\Services\CampaignServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Service class that handles all campaign business logic
 */
class CampaignService implements CampaignServiceInterface
{
    protected $campain_repo;
    protected $campain_banner_repo;


    public function __construct(CampaignRepositoryInterface $campaignRepository, CampaignBannerRepositoryInterface $campaignBannerRepository)
    {
        $this->campain_repo = $campaignRepository;
        $this->campain_banner_repo = $campaignBannerRepository;
    }

    /**
     * Get all campaigns
     * @return CampaignDto[]
     */
    public function getAll()
    {
        return $this->campain_repo->all();
    }

    /**
     * Handles persisting of campaign
     *
     * @param array $data 
     * @var File[] $banner_files campaign banner image files
     * @param bool $auto_generate_fiile_names indicate if the banner images filename be auto-generated on save. 
     * Note: if set to False, the original filename will be used. 
     * @return \App\Http\DTOs\CampaignDto
     */
    public function create(array $data, array $banner_files, bool $auto_generate_fiile_names = true)
    {
        // begin transaction to enable rolling back all changes if something goes wrong
        // when the repository method is invoked.
        DB::beginTransaction();
        $banners = array();

        try {
            // save the images first to generate paths that will be stored in the database
            $banners = $this->upload_banner_images($banner_files, $auto_generate_fiile_names);

            $campaign = $this->campain_repo->create($data, $banners);

            DB::commit();
            return $campaign;
        } catch (Exception $e) {
            DB::rollBack();
            // we need to delete those images uploaded already if any
            $this->delete_banner_images($banners);
            // rethrow error to be handled at the controller level
            throw $e;
        }
    }

    /**
     * Update campaign entry
     * @param int $id the id of the campaign to be updated
     * @param array $data campaign to be updated
     * @param array $new_banner_files array of posted files to be added to the campaign
     * @param array $deleted_banners array of integer containing ids of the banners to be deleted
     * @param bool $auto_generate_fiile_names indicate if the banner images filename be auto-generated on save. 
     * Note: if set to False, the original filename will be used. 
     * @return \App\Http\DTOs\CampaignDto
     */
    public function update(int $id, array $data, array $new_banner_files, array $deleted_banners, bool $auto_generate_fiile_names = true)
    {
        // begin transaction to enable rolling back all changes if something goes wrong.
        DB::beginTransaction();
        $new_banners = array();

        try {
            // save the images first to generate paths that will be stored in the database
            $new_banners = $this->upload_banner_images($new_banner_files, $auto_generate_fiile_names);
            // get full banner info for the banners to be deleted
            $deleted_banners_info = $this->campain_banner_repo->getBannersById($deleted_banners, false);
            $campaign =  $this->campain_repo->update($id, $data, $new_banners, $deleted_banners);
            // remove the deleted banner images from the file system
            $this->delete_banner_images($deleted_banners_info);

            DB::commit();
            return $campaign;
        } catch (Exception $e) {
            DB::rollBack();
            // we need to delete those images uploaded already if any
            $this->delete_banner_images($new_banners);
            // rethrow error to be handled at the controller level
            throw $e;
        }
    }

    /**
     * Upload posted files to the file system
     *
     * @var File[] $posted_files campaign banner image files
     * @param bool $auto_generate_fiile_names indicate if the banner images filename be auto-generated on save
     * @return array array of objects containing 'image_url'=> value
     */
    private function upload_banner_images($posted_files, $auto_generate_fiile_names)
    {

        $banners_files = array();
        foreach ($posted_files as $file) {
            if ($auto_generate_fiile_names) {
                $path = $file->store('banners', 'public');
            } else {
                $path = $file->storeAs('banners', $file->getClientOriginalName(), 'public');
            }
            $banners_files[] = array('image_url' => $path);
        }

        return $banners_files;
    }

    /**
     * Delete images from the file system
     *
     * @param array $banner_files array containing image paths to be deleted
     * @return void
     */
    private function delete_banner_images($banner_files)
    {
        if (count($banner_files) > 0) {
            $paths = array_map(
                function ($obj) {
                    return $obj['image_url'];
                },
                $banner_files
            );
            Storage::disk('public')->delete($paths);
        }
    }
}
