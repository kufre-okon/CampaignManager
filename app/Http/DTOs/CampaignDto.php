<?php

declare(strict_types=1);

namespace App\Http\DTOs;

use App\Models\Campaign;

final class CampaignDto
{
    public $id;
    public $name;
    public $date_from;
    public $date_to;
    public $total_budget;
    public $daily_budget;
    public $created_at;
    /**
     * Array of campaign banners
     *
     * @var \CampaignBannerDto[]
     */
    public $banners;

    public function __construct(Campaign $campaign, $convertPathToUrl)
    {
        $campaign_arr = $campaign->toArray();

        $this->id = $campaign_arr['id'];
        $this->name = $campaign_arr['name'];
        $this->date_to = $campaign_arr['date_to'];
        $this->date_from = $campaign_arr['date_from'];
        $this->daily_budget = $campaign_arr['daily_budget'];
        $this->created_at = $campaign_arr['created_at'];
        $this->total_budget = $campaign_arr['total_budget'];

        $this->banners = array_map(
            function ($banner) use ($convertPathToUrl) {
                return (array)new CampaignBannerDto($banner, $convertPathToUrl);
            },
            $campaign->banners()->get()->all()
        );
    }
}
