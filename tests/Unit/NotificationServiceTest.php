<?php

use Transmissor\Test\TestCase;
use Transmissor\Services\NotificationService;
use Transmissor\Services\UserService;
use Transmissor\Models\Notification;

class NotificationServiceTest extends TestCase
{
    protected $service;
    protected $originalArray;
    protected $editedArray;
    protected $searchTerm;

    protected function setUp(): void
    {
        parent::setUp();

        $user = new class {
            public $id = 1;
            public $name = 'Test User';
            public function notify($notification) {}
        };

        $userServiceMock = Mockery::mock(UserService::class);
        $userServiceMock->shouldReceive('find')->andReturn($user);
        $userServiceMock->shouldReceive('all')->andReturn(collect([$user]));
        $this->app->instance(UserService::class, $userServiceMock);

        $this->service = new NotificationService(new Notification(), $userServiceMock);

        $this->originalArray = [
            'user_id' => 1,
            'flag' => 'info',
            'uuid' => 'lksjdflaskhdf',
            'title' => 'Testing',
            'details' => 'Your car has been impounded!',
            'is_read' => 0,
        ];
        $this->editedArray = [
            'user_id' => 1,
            'flag' => 'info',
            'uuid' => 'lksjdflaskhdf',
            'title' => 'Testing',
            'details' => 'Your car has been impounded!',
            'is_read' => 1,
        ];
        $this->searchTerm = '';
    }

    public function testAll()
    {
        $response = $this->service->all();
        $this->assertEquals(get_class($response), 'Illuminate\Database\Eloquent\Collection');
        $this->assertTrue(is_array($response->toArray()));
        $this->assertEquals(0, count($response->toArray()));
    }

    public function testPaginated()
    {
        $response = $this->service->paginated(25);
        $this->assertEquals(get_class($response), 'Illuminate\Pagination\LengthAwarePaginator');
        $this->assertEquals(0, $response->total());
    }

    public function testSearch()
    {
        $response = $this->service->search($this->searchTerm, 25);
        $this->assertEquals(get_class($response), 'Illuminate\Pagination\LengthAwarePaginator');
        $this->assertEquals(0, $response->total());
    }

    public function testCreate()
    {
        $response = $this->service->create($this->originalArray);
        $this->assertEquals(get_class($response), 'Transmissor\Models\Notification');
        $this->assertEquals(1, $response->id);
    }

    public function testFind()
    {
        // create the item
        $item = $this->service->create($this->originalArray);

        $response = $this->service->find($item->id);
        $this->assertEquals(1, $response->id);
    }

    public function testUpdate()
    {
        // create the item
        $item = $this->service->create($this->originalArray);

        $response = $this->service->update($item->id, $this->editedArray);

        $this->assertEquals(1, $response->id);
        $this->assertDatabaseHas('notifications', $this->editedArray);
    }

    public function testDestroy()
    {
        // create the item
        $item = $this->service->create($this->originalArray);

        $response = $this->service->destroy($item->id);
        $this->assertTrue($response);
    }
}
