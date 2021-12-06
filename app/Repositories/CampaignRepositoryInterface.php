<?php

namespace App\Repositories;

use App\Http\DTOs\CampaignDto;
use Illuminate\Support\Collection;

interface CampaignRepositoryInterface
{
    /**
     * Save model in the database.
     * @param array $data campaign data to be saved
     * @param array $banners array of object containing 'image_url' to be saved
     * @return CampaignDto
     */
    public function create($data,  $banners);

    /**
     * Update the model in the database.
     * @param int $id table key column value
     * @param array $data campaign data to be updated
     * @param array $new_banners array of object containing 'image_url' to be inserted
     * @param array $deleted_banners array of integer containing ids of the banners to be deleted
     * @return CampaignDto
     */
    public function update($id, $data, $new_banners, $deleted_banners);

    /**
     * Get all campaigns.
     * @return CampaignDto[]
     */
    public function all();
}
