<?php


namespace App\Services;

/**
 * Service class Interface for handling all campaign business logic
 *
 * @package App\Services
 */
interface CampaignServiceInterface
{
    /**
     * Handles persisting of campaign
     *
     * @param array $data 
     * @var File[] $banner_files campaign banner image files
     * @param bool $auto_generate_fiile_names indicate if the banner images filename be auto-generated on save. 
     * Note: if set to False, the original filename will be used. 
     * @return \App\Http\DTOs\CampaignDto
     */
    public function create(array $data, array $banner_files, bool $auto_generate_fiile_names = true);

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
    public function update(int $id, array $data, array $new_banner_files, array $deleted_banners, bool $auto_generate_fiile_names = true);

    /**
     * Get all campaigns
     * @return CampaignDto[]
     */
    public function getAll();
}
