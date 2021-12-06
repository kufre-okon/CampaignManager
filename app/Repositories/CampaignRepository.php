<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Http\DTOs\CampaignDto;
use App\Repositories\CampaignRepositoryInterface;
use App\Models\Campaign;

/**
 * Repository class that abstract direct data access
 */
class CampaignRepository implements CampaignRepositoryInterface
{

    /**      
     * @var Campaign      
     */
    protected $model;

    public function __construct(Campaign $model)
    {
        $this->model = $model;
    }

    /**
     * Get all campaigns.
     * @return CampaignDto[]
     */
    public function all(): array
    {
        $campaigns = $this->model->all()->reverse();

        $data = array();

        foreach ($campaigns as $campaign) {
            $data[] = new CampaignDto($campaign, true);
        }
        return $data;
    }

    /**
     * Save model in the database.
     * @param array $data campaign data to be saved
     * @param array $banners array of object containing 'image_url' to be saved
     * @return CampaignDto
     */
    public function create($data, $banners): CampaignDto
    {
        // store the campaign info first before storing the banners
        $campaign = $this->model->create($data);
        $this->create_banners($campaign, $banners);
        $campaign->refresh();

        return new CampaignDto($campaign, true);
    }

    /**
     * Update the model in the database.
     * @param int $id table key column value
     * @param array $data campaign data to be updated
     * @param array $new_banners array of object containing 'image_url' to be inserted
     * @param array $deleted_banners array of integer containing ids of the banners to be deleted
     * @return CampaignDto
     */
    public function update($id, $data, $new_banners, $deleted_banners)
    {
        $campaign = $this->model->find($id);
        $campaign->update($data);
        // delete the banners deleted by the user
        $this->delete_banners($campaign, $deleted_banners);
        // store new banners sent by the user
        $this->create_banners($campaign, $new_banners);

        $campaign->refresh();

        return new CampaignDto($campaign, true);
    }

    private function create_banners($campaign, $banners)
    {
        if (count($banners) > 0) {
            $campaign->banners()->createMany($banners);
        }
    }

    private function delete_banners($campaign, $banners)
    {
        if (count($banners) > 0) {
            $campaign->banners()->whereIn('id', $banners)->delete();
        }
    }
}
