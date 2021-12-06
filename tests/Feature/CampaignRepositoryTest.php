<?php

namespace Tests\Feature;

use App\Http\DTOs\CampaignDto;
use App\Models\Campaign;
use App\Models\CampaignBanner;
use Database\Seeders\CampaignSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_load_all_campaigns()
    {
        // Run a specific seeder...
        $this->seed(CampaignSeeder::class);

        $repository = new \App\Repositories\CampaignRepository(new Campaign());

        $data = $repository->all();

        $this->assertIsArray($data);
        $this->assertCount(10, $data);
    }

    public function test_it_should_create_campaign()
    {
        $repository = new \App\Repositories\CampaignRepository(new Campaign());
        $campaign = Campaign::factory()->make()->toArray();
        $campaign_banners = CampaignBanner::factory()->count(1)->make()->toArray();

        $campaign_dto =  $repository->create($campaign, $campaign_banners);

        $this->assertInstanceOf(CampaignDto::class, $campaign_dto);
        $this->assertEquals($campaign['name'], $campaign_dto->name);
        $this->assertEquals($campaign['total_budget'], $campaign_dto->total_budget);
        $this->assertEquals($campaign['daily_budget'], $campaign_dto->daily_budget);
        $this->assertEquals($campaign['date_from'], $campaign_dto->date_from);
        $this->assertIsArray($campaign_dto->banners);
    }

    public function test_it_should_throw_error_when_any_required_campaign_field_is_missing()
    {
        $this->expectException(QueryException::class);

        $repository = new \App\Repositories\CampaignRepository(new Campaign());
        $campaign = Campaign::factory()->make(['name' => null])->toArray();
        $campaign_banners = CampaignBanner::factory()->count(1)->make()->toArray();

        $repository->create($campaign, $campaign_banners);
    }

    public function test_it_should_update_campaign()
    {
        $repository = new \App\Repositories\CampaignRepository(new Campaign());

        $this->seed(CampaignSeeder::class);
        $campaign = Campaign::take(1)->first();
        $new_campaign = Campaign::factory()->make();
        $repository->update($campaign->id, $new_campaign->toArray(), array(), array());

        $updated_campaign = Campaign::find($campaign->id);

        $this->assertEquals($updated_campaign->name, $new_campaign->name);
        $this->assertEquals($updated_campaign->total_budget, $new_campaign->total_budget);
        $this->assertEquals($updated_campaign->daily_budget, $new_campaign->daily_budget);
        $this->assertEquals($updated_campaign->date_from, $new_campaign->date_from);
        $this->assertEquals($updated_campaign->date_to, $new_campaign->date_to);
    }

    public function test_it_should_update_campaign_with_banners()
    {
        $repository = new \App\Repositories\CampaignRepository(new Campaign());

        $this->seed(CampaignSeeder::class);
        $campaign = Campaign::take(1)->first();

        $deleted_banner_ids = $campaign->banners()->pluck('id')->toArray();
       
        $new_campaign = Campaign::factory()->make();
        $new_campaign_banners = CampaignBanner::factory()->count(3)->make([
            'image_url' => 'https://source.unsplash.com/random/500x800'
        ])->toArray();

        $repository->update($campaign->id, $new_campaign->toArray(), $new_campaign_banners, $deleted_banner_ids);

        $updated_campaign = Campaign::find($campaign->id);
        $updated_campaign_banners = $updated_campaign->banners()->get()->all();

        $this->assertEquals($updated_campaign->name, $new_campaign->name);
        $this->assertEquals($updated_campaign->total_budget, $new_campaign->total_budget);
        $this->assertEquals($updated_campaign->daily_budget, $new_campaign->daily_budget);
        $this->assertEquals($updated_campaign->date_from, $new_campaign->date_from);
        $this->assertEquals($updated_campaign->date_to, $new_campaign->date_to);
        $this->assertIsArray($updated_campaign_banners);
        $this->assertCount(3, $updated_campaign_banners);
    }

    public function test_it_should_throw_error_when_any_required_campaign_field_is_missing_for_update()
    {
        $this->expectException(QueryException::class);

        $repository = new \App\Repositories\CampaignRepository(new Campaign());

        $this->seed(CampaignSeeder::class);
        $campaign = Campaign::take(1)->first();
        $new_campaign = Campaign::factory()->make(['name' => null]);
        $repository->update($campaign->id, $new_campaign->toArray(), array(), array());
    }
}
