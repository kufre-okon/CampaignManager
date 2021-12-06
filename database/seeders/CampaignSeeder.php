<?php

namespace Database\Seeders;

use App\Models\CampaignBanner;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Campaign::factory()
            ->has(CampaignBanner::factory()->count(1), 'banners')
            ->count(10)
            ->create();
    }
}
