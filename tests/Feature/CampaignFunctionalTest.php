<?php

namespace Tests\Feature;

use App\Models\Campaign;
use Database\Seeders\CampaignSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CampaignFunctionalTest extends TestCase
{

    public function test_it_should_create_campaign_with_banner_files()
    {
        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-12-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array(UploadedFile::fake()->image('test_image2.png')),
        );

        $response = $this->postJson('/api/campaign/create', $form_data);

        $response->assertStatus(200);
        $content = $response->json();

        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertTrue($content['status']);
        $this->assertIsArray($content['payload']);
        $data = $content['payload'];
        $this->assertEquals($data['name'], $form_data['name']);
        $this->assertEquals($data['total_budget'], $form_data['total_budget']);
        $this->assertEquals($data['daily_budget'], $form_data['daily_budget']);
        $this->assertEquals($data['date_from'], $form_data['date_from']);
        $this->assertEquals($data['date_to'], $form_data['date_to']);
        $this->assertArrayHasKey('banners', $content['payload']);
        $this->assertIsArray($content['payload']['banners']);
        $this->assertCount(1, $content['payload']['banners']);
    }


    public function test_it_should_update_campaign_with_banner_files()
    {

        $this->seed(CampaignSeeder::class);
        $campaign = Campaign::take(1)->first();
        $deleted_banner_ids = $campaign->banners()->pluck('id')->toArray();

        $form_data = array(
            "name" => 'campaign 3',
            "date_from" => '2008-12-23',
            "date_to" => '2020-11-30',
            "total_budget" => 2600,
            "daily_budget" => 100,
            "banner_files" => array(
                UploadedFile::fake()->image('test_image2.png'),
                UploadedFile::fake()->image('test_image3.png')
            ),
            "deleted_files" => $deleted_banner_ids
        );

        $response = $this->putJson('/api/campaign/update/' . $campaign->id, $form_data);

        $response->assertStatus(200);
        $content = $response->json();

        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertTrue($content['status']);
        $this->assertIsArray($content['payload']);
        $data = $content['payload'];
        $this->assertEquals($data['name'], $form_data['name']);
        $this->assertEquals($data['total_budget'], $form_data['total_budget']);
        $this->assertEquals($data['daily_budget'], $form_data['daily_budget']);
        $this->assertEquals($data['date_from'], $form_data['date_from']);
        $this->assertEquals($data['date_to'], $form_data['date_to']);
        $this->assertArrayHasKey('banners', $content['payload']);
        $this->assertIsArray($content['payload']['banners']);
        $this->assertCount(2, $content['payload']['banners']);
    }
}
