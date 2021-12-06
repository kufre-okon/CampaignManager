<?php

namespace App\Repositories;

use App\Http\DTOs\CampaignDto;
use Illuminate\Support\Collection;

interface CampaignBannerRepositoryInterface
{
    /**
     * Get banner info by banner ids
     *
     * @param array $banner_ids integer array
     * @param bool $convertPathToUrl indicate whether the banner path should be converted to storage url suitable for the browser
     * @return array array of CampaignBannerDto
     */
    public function getBannersById($banner_ids, $convertPathToUrl);
}
