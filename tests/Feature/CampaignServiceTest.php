<?php

namespace Tests\Feature;

use App\Http\DTOs\CampaignDto;
use App\Models\Campaign;
use App\Models\CampaignBanner;
use App\Services\CampaignService;
use Exception;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class CampaignServiceTest extends TestCase
{
    protected $campaign_repo_mock;
    protected $campaign_banner_repo_mock;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign_repo_mock = $this->mock('App\Repositories\CampaignRepositoryInterface');
        $this->campaign_banner_repo_mock = $this->mock('App\Repositories\CampaignBannerRepositoryInterface');
        $data = (object) array(
            "id" => 10,
            "name" => 'Consequatur pariatur et est.',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1619,
            "daily_budget" => 55,
            "created_at" => '2021-11-30',
            "banners" => array(
                "0" => array(
                    "id" => 10,
                    "campaign_id" => 10,
                    "image_url" => ''
                ),
                "1" => array(
                    "id" => 11,
                    "campaign_id" => 10,
                    "image_url" => ''
                )
            )
        );

        $this->campaign_repo_mock->campaign_data = $data;
        $this->app->instance('App\Repositories\CampaignRepository', $this->campaign_repo_mock);
        $this->app->instance('App\Repositories\CampaignBannerRepository', $this->campaign_banner_repo_mock);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_should_load_all_campaigns()
    {
        $campaigns = array($this->campaign_repo_mock->campaign_data);

        $this->campaign_repo_mock->shouldReceive('all')->once()->andReturns($campaigns);
        $campaign_service = $this->create_service();
        $fetched_campaigns = $campaign_service->getAll();

        $this->assertEquals(count($campaigns), count($fetched_campaigns));
        $this->assertCount(2, $fetched_campaigns[0]->banners);
    }

    public function test_it_should_create_campaign()
    {
        $banner_files = array(UploadedFile::fake()->image('test_image.png'));
        $campaign = $this->campaign_repo_mock->campaign_data;

        $this->campaign_repo_mock->shouldReceive('create')->once()->andReturns($campaign);
        $campaign_service = $this->create_service();
        $saved_campaign_dto = $campaign_service->create((array)$campaign, $banner_files, false);

        Storage::disk('public')->assertExists('banners/test_image.png');
        $this->assertEquals($campaign->name, $saved_campaign_dto->name);
        $this->assertEquals($campaign->total_budget, $saved_campaign_dto->total_budget);
        $this->assertEquals($campaign->daily_budget, $saved_campaign_dto->daily_budget);
        $this->assertEquals($campaign->date_from, $saved_campaign_dto->date_from);
        $this->assertIsArray($saved_campaign_dto->banners);
        $this->assertCount(2, $saved_campaign_dto->banners);
    }

    public function test_it_should_delete_all_banner_images_saved_if_campaign_fail_to_create()
    {
        $expected_error_msg = 'Test failure message';
        try {
            $banner_files = array(UploadedFile::fake()->image('test_image_2.png'));
            $this->campaign_repo_mock->shouldReceive('create')->once()->andThrow(new RuntimeException($expected_error_msg));
            $campaign_service = $this->create_service();
            $campaign_service->create(array(), $banner_files, false);
        } catch (\RuntimeException $e) {
            $className = get_class($e);
            $actual_error_msg = $e->getMessage();
        }

        $this->assertSame(\RuntimeException::class, $className);
        $this->assertEquals($actual_error_msg, $expected_error_msg);
        Storage::disk('public')->assertMissing('banners/test_image_2.png');
    }

    public function test_it_should_update_campaign()
    {
        $campaign = $this->campaign_repo_mock->campaign_data;

        $this->campaign_repo_mock->shouldReceive('update')->once()->andReturns($campaign);
        $this->campaign_banner_repo_mock->shouldReceive('getBannersById')->once()->andReturns(array());

        $campaign_service = $this->create_service();

        $updated_campaign = $campaign_service->update($campaign->id, (array)$campaign, array(), array(), false);

        $this->assertEquals($campaign->name, $updated_campaign->name);
        $this->assertEquals($campaign->total_budget, $updated_campaign->total_budget);
        $this->assertEquals($campaign->daily_budget, $updated_campaign->daily_budget);
        $this->assertEquals($campaign->date_from, $updated_campaign->date_from);
        $this->assertIsArray($updated_campaign->banners);
        $this->assertCount(2, $updated_campaign->banners);
    }

    public function test_it_should_update_campaign_with_new_banners()
    {
        $campaign = $this->campaign_repo_mock->campaign_data;
        $campaign_banners[0] = array(
            'id' => 1,
            'image_url' => UploadedFile::fake()->image('test_image_to_be_deleted.png')
                ->storeAs('banners', 'test_image_to_be_deleted.png', 'public')
        );

        $this->campaign_repo_mock->shouldReceive('update')->once()->andReturns($campaign);
        $this->campaign_banner_repo_mock->shouldReceive('getBannersById')->once()->andReturns($campaign_banners);

        $campaign_service = $this->create_service();

        $new_campaign_banners = array(UploadedFile::fake()->image('new_campaign_image.png'));
        $deleted_banners = array_map(function ($banner) {
            return $banner['id'];
        }, $campaign_banners);
        $updated_campaign = $campaign_service->update($campaign->id, (array)$campaign, $new_campaign_banners, $deleted_banners, false);

        Storage::disk('public')->assertExists('banners/new_campaign_image.png');
        Storage::disk('public')->assertMissing('banners/test_image_to_be_deleted.png');
        $this->assertEquals($campaign->name, $updated_campaign->name);
        $this->assertEquals($campaign->total_budget, $updated_campaign->total_budget);
        $this->assertEquals($campaign->daily_budget, $updated_campaign->daily_budget);
        $this->assertEquals($campaign->date_from, $updated_campaign->date_from);
        $this->assertIsArray($updated_campaign->banners);
    }

    public function test_it_should_delete_all_banner_images_saved_if_campaign_fail_to_update()
    {
        $expected_error_msg = 'Test failure message';
        try {
            $new_campaign_banners = array(UploadedFile::fake()->image('new_campaign_image.png'));
            $campaign_banners[0] = array(
                'id' => 1,
                'image_url' => UploadedFile::fake()->image('test_image_to_be_deleted.png')
                    ->storeAs('banners', 'test_image_to_be_deleted.png', 'public')
            );
            $deleted_banners = array_map(function ($banner) {
                return $banner['id'];
            }, $campaign_banners);

            $this->campaign_banner_repo_mock->shouldReceive('getBannersById')->once()->andReturns($campaign_banners);
            $this->campaign_repo_mock->shouldReceive('update')->once()->andThrow(new RuntimeException($expected_error_msg));
            $campaign_service = $this->create_service();
            $campaign_service->update(0, array(), $new_campaign_banners, $deleted_banners, false);
        } catch (\RuntimeException $e) {
            $className = get_class($e);
            $actual_error_msg = $e->getMessage();
        }

        $this->assertSame(\RuntimeException::class, $className);
        $this->assertEquals($actual_error_msg, $expected_error_msg);
        Storage::disk('public')->assertMissing('banners/new_campaign_image.png');
        Storage::disk('public')->assertExists('banners/test_image_to_be_deleted.png');
    }

    private function create_service()
    {
        return App::make(CampaignService::class, array($this->campaign_repo_mock, $this->campaign_banner_repo_mock));
    }
}
