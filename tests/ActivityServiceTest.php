<?php

use Transmissor\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Transmissor\Services\RoleService;

class ActivityServiceTest extends TestCase
{
    use DatabaseMigrations;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ActivityService::class);
    }

    public function testGetByUser()
    {
        $user = factory(User::class)->create();

        $response = $this->service->getByUser($user);
        $this->assertEquals(get_class($response), 'Illuminate\Database\Eloquent\Collection');
        $this->assertTrue(is_array($response->toArray()));
        $this->assertEquals(0, count($response->toArray()));
    }

    public function testLog()
    {
        $user = factory(User::class)->create();
        $this->be($user);

        $response = $this->service->log('this is a simple test');
        $this->assertEquals(get_class($response), 'Porteiro\Models\Activity');
    }
}
