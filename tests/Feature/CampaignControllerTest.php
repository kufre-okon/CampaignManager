<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\CampaignController;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class CampaignControllerTest extends TestCase
{
    protected $campaign_serv_mock;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign_serv_mock = $this->mock('App\Services\CampaignServiceInterface');

        $data = array(
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

        $this->campaign_serv_mock->campaign_data = $data;
        $this->app->instance('App\Services\CampaignService', $this->campaign_serv_mock);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_it_should_load_all_campaigns()
    {
        $campaigns = array($this->campaign_serv_mock->campaign_data);

        $this->campaign_serv_mock->shouldReceive('getAll')->once()->andReturns($campaigns);
        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));
        $response = $controller->list();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertTrue($content['status']);
        $this->assertIsArray($content['payload']);
        $this->assertCount(1, $content['payload']);
        $this->assertIsArray($content['payload'][0]);
        $this->assertArrayHasKey('banners', $content['payload'][0]);
        $this->assertIsArray($content['payload'][0]['banners']);
        $this->assertCount(2, $content['payload'][0]['banners']);
    }

    public function test_it_return_custom_error_if_request_failed()
    {
        $this->campaign_serv_mock->shouldReceive('getAll')->once()->andThrow(new RuntimeException(''));
        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));
        $response = $controller->list();

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(400,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertNotTrue($content['status']);
        $this->assertNull($content['payload']);
    }

    public function test_it_should_not_create_campaign_with_invalid_fields()
    {
        $form_data = array(
            "name" => '',
            'date_from' => '2021-29-09',
            'date_to' => '2021-12-09',
            'daily_budget' => 60,
            'total_budget' => '600o',
        );

        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new StoreCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);

        $response = $controller->store($form_request);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(422,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertFalse($content['status']);
        $this->assertArrayHasKey('name', $content['payload']);
        $this->assertArrayHasKey('date_from', $content['payload']);
        $this->assertArrayHasKey('total_budget', $content['payload']);
        $this->assertArrayNotHasKey('daily_budget', $content['payload']);
        $this->assertArrayHasKey('banner_files', $content['payload']);
    }

    public function test_it_should_not_create_campaign_with_invalid_banner_file_type()
    {
        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array(UploadedFile::fake()->create('test_image2.pdf', 10)),
        );

        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new StoreCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);
        $response = $controller->store($form_request);
        $content = json_decode($response->getContent(), true);

        $imagefield = 'banner_files.0';

        $this->assertEquals(422,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertFalse($content['status']);
        $this->assertArrayHasKey($imagefield, $content['payload']);
        $this->assertIsString($content['payload'][$imagefield][0]);
        $this->assertIsString($content['payload'][$imagefield][1]);
    }

    public function test_it_should_not_create_campaign_with_invalid_banner_files()
    {
        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array('id' => 1),
        );

        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new StoreCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);
        $response = $controller->store($form_request);
        $content = json_decode($response->getContent(), true);

        $invalid_field = 'banner_files.id';

        $this->assertEquals(422,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertFalse($content['status']);
        $this->assertArrayHasKey($invalid_field, $content['payload']);
        $this->assertIsString($content['payload'][$invalid_field][0]);
    }

    public function test_it_should_create_campaign_with_banner_files()
    {
        $campaigns = $this->campaign_serv_mock->campaign_data;

        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array(UploadedFile::fake()->image('test_image2.png')),
        );

        $this->campaign_serv_mock->shouldReceive('create')->once()->andReturns($campaigns);
        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new StoreCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);

        $response = $controller->store($form_request);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertTrue($content['status']);
        $this->assertIsArray($content['payload']);
        $this->assertArrayHasKey('banners', $content['payload']);
        $this->assertIsArray($content['payload']['banners']);
        $this->assertCount(2, $content['payload']['banners']);
    }

    public function test_it_should_not_update_campaign_with_invalid_fields()
    {
        $form_data = array(
            "name" => '',
            'date_from' => '2021-29-09',
            'date_to' => '2021-12-09',
            'daily_budget' => 60,
            'total_budget' => '600o',
        );

        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new UpdateCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);

        $response = $controller->update(1, $form_request);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(422,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertFalse($content['status']);
        $this->assertArrayHasKey('name', $content['payload']);
        $this->assertArrayHasKey('date_from', $content['payload']);
        $this->assertArrayHasKey('total_budget', $content['payload']);
        $this->assertArrayNotHasKey('daily_budget', $content['payload']);
        $this->assertArrayNotHasKey('banner_files', $content['payload']);
        $this->assertArrayNotHasKey('deleted_files', $content['payload']);
    }

    public function test_it_should_not_update_campaign_with_invalid_banner_file_type()
    {
        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array(UploadedFile::fake()->create('test_image2.pdf', 10)),
        );

        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new UpdateCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);
        $response = $controller->update(1, $form_request);
        $content = json_decode($response->getContent(), true);

        $imagefield = 'banner_files.0';

        $this->assertEquals(422,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertFalse($content['status']);
        $this->assertArrayHasKey($imagefield, $content['payload']);
        $this->assertIsString($content['payload'][$imagefield][0]);
        $this->assertIsString($content['payload'][$imagefield][1]);
    }

    public function test_it_should_not_update_campaign_with_invalid_banner_files()
    {
        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array('id' => 1),
        );

        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new UpdateCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);
        $response = $controller->update(1, $form_request);
        $content = json_decode($response->getContent(), true);

        $invalid_field = 'banner_files.id';

        $this->assertEquals(422,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertFalse($content['status']);
        $this->assertArrayHasKey($invalid_field, $content['payload']);
        $this->assertIsString($content['payload'][$invalid_field][0]);
    }

    public function test_it_should_update_campaign_without_banners_images()
    {
        $campaigns = $this->campaign_serv_mock->campaign_data;

        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90
        );

        $this->campaign_serv_mock->shouldReceive('update')->once()->andReturns($campaigns);
        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new UpdateCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);

        $response = $controller->update(1, $form_request);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertTrue($content['status']);
        $this->assertIsArray($content['payload']);
        $this->assertArrayHasKey('banners', $content['payload']);
        $this->assertIsArray($content['payload']['banners']);
        $this->assertCount(2, $content['payload']['banners']);
    }

    public function test_it_should_update_campaign_with_banners_images()
    {
        $campaigns = $this->campaign_serv_mock->campaign_data;

        $form_data = array(
            "name" => 'campaign 2',
            "date_from" => '2007-05-23',
            "date_to" => '2021-11-30',
            "total_budget" => 1600,
            "daily_budget" => 90,
            "banner_files" => array(UploadedFile::fake()->image('test_image2.png', 10)),
            'deleted_files' => array(1)
        );

        $this->campaign_serv_mock->shouldReceive('update')->once()->andReturns($campaigns);
        $controller = App::make(CampaignController::class, array($this->campaign_serv_mock));

        $form_request = new UpdateCampaignRequest(array(), $form_data);
        $validator = Validator::make($form_data, $form_request->rules(), $form_request->messages());
        $form_request->setValidator($validator);

        $response = $controller->update(1, $form_request);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(200,  $response->getStatusCode());
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('message', $content);
        $this->assertArrayHasKey('payload', $content);
        $this->assertTrue($content['status']);
        $this->assertIsArray($content['payload']);
        $this->assertArrayHasKey('banners', $content['payload']);
        $this->assertIsArray($content['payload']['banners']);
        $this->assertCount(2, $content['payload']['banners']);
    }
}
