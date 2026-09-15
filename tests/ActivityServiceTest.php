<?php

use Transmissor\Test\User;
use Transmissor\Test\TestCase;
use Transmissor\Services\ActivityService;

class ActivityServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->app->make(ActivityService::class);
    }

    public function testGetByUser()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test_activity@example.com',
        ]);

        $response = $this->service->getByUser($user->id);
        $this->assertEquals(get_class($response), 'Illuminate\Database\Eloquent\Collection');
        $this->assertTrue(is_array($response->toArray()));
        $this->assertEquals(0, count($response->toArray()));
    }

    public function testLog()
    {
        $user = User::create([
            'name' => 'Test User 2',
            'email' => 'test_activity2@example.com',
        ]);
        $this->be($user);

        $response = $this->service->log('this is a simple test');
        $this->assertEquals(get_class($response), 'Porteiro\Models\Activity');
    }
}
