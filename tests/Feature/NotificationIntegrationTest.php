<?php

use Transmissor\Test\TestCase;
use Transmissor\Models\Notification;
use Transmissor\Models\Group;
use Transmissor\Models\Comment;
use Transmissor\Test\User;

class NotificationIntegrationTest extends TestCase
{
    public function testNotificationModelCrud()
    {
        $notification = Notification::create([
            'user_id' => 1,
            'flag' => 'info',
            'uuid' => 'test-uuid-1234',
            'title' => 'Testing Notification',
            'details' => 'Your car has been impounded!',
            'is_read' => 0,
        ]);

        $this->assertEquals(1, $notification->id);
        $this->assertEquals('info', $notification->flag);
        $this->assertEquals('Testing Notification', $notification->title);
        $this->assertEquals(0, $notification->is_read);

        $notification->update(['is_read' => 1]);
        $this->assertEquals(1, $notification->fresh()->is_read);
        $this->assertDatabaseHas('notifications', [
            'id' => 1,
            'is_read' => 1,
        ]);

        $notification->delete();
        $this->assertNull(Notification::find(1));
    }

    public function testGroupModel()
    {
        $chat = [
            'id' => 987654,
            'type' => 'supergroup',
            'title' => 'Developers Group',
        ];

        $group = Group::updateOrCreateFromChat($chat, 'pt-BR', 'BRL');
        $this->assertNotNull($group);
        $this->assertEquals(987654, $group->telegram_id);
        $this->assertEquals('Developers Group', $group->title);
        $this->assertEquals('pt-BR', $group->language);
        $this->assertEquals('BRL', $group->currency);

        $updated = Group::updateOrCreateFromChat($chat, 'en', 'USD');
        $this->assertEquals('en', $updated->language);
        $this->assertEquals('USD', $updated->currency);
    }

    public function testCommentModel()
    {
        $comment = new Comment([
            'commentable_id' => 10,
            'commentable_type' => 'App\\Models\\Post',
            'content' => 'Great Post and Discussion',
        ]);

        $this->assertEquals(10, $comment->commentable_id);
        $this->assertEquals('great_post_and_discussion', $comment->content);

        $entity = $comment->toEntity();
        $this->assertNotNull($entity);
        $this->assertEquals('great_post_and_discussion', $entity->getValue());
    }
}
