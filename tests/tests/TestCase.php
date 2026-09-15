<?php

namespace Transmissor\Test;

date_default_timezone_set('America/New_York');

require_once __DIR__ . '/Faktory.php';

use AdamWathan\Faktory\Faktory;
use Transmissor\Models\Messenger\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * @var \AdamWathan\Faktory\Faktory
     */
    protected $faktory;

    /**
     * Set up the database, migrations, and initial data.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->configureDatabase();
        $this->migrateTables();
        $this->faktory = new Faktory;
        $load_factories = function ($faktory) {
            require __DIR__ . '/factories.php';
        };
        $load_factories($this->faktory);

        $userModel = User::class;
        Models::setUserModel($userModel);
        \Cmgmyr\Messenger\Models\Models::setUserModel($userModel);
        \Cmgmyr\Messenger\Models\Models::setMessageModel(\Transmissor\Models\Messenger\Message::class);
        \Cmgmyr\Messenger\Models\Models::setParticipantModel(\Transmissor\Models\Messenger\Participant::class);
        \Cmgmyr\Messenger\Models\Models::setThreadModel(\Transmissor\Models\Messenger\Thread::class);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getPackageProviders($app)
    {
        return [
            \Transmissor\TransmissorProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('messenger.message_model', 'Transmissor\Models\Messenger\Message');
        $app['config']->set('messenger.participant_model', 'Transmissor\Models\Messenger\Participant');
        $app['config']->set('messenger.thread_model', 'Transmissor\Models\Messenger\Thread');
    }

    /**
     * Configure the database.
     */
    private function configureDatabase()
    {
    }

    /**
     * Run the migrations for the database.
     */
    private function migrateTables()
    {
        $this->createUsersTable();
        $this->createThreadsTable();
        $this->createMessagesTable();
        $this->createParticipantsTable();
        $this->createActivitiesTable();
        $this->createNotificationsTable();
        $this->createGroupsTable();

        $this->seedUsersTable();
    }

    /**
     * Create the users table in the database.
     */
    private function createUsersTable()
    {
        Schema::create(
            'users',
            function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->enum('notify', ['y', 'n'])->default('y');
                $table->timestamps();
            }
        );
    }

    /**
     * Create some users for the tests to use.
     */
    private function seedUsersTable()
    {
        DB::insert('INSERT INTO ' . DB::getTablePrefix() . 'users (id, name, email, created_at, updated_at) VALUES (?, ?, ?, datetime(), datetime())', [1, 'Chris Gmyr', 'chris@test.com']);
        DB::insert('INSERT INTO ' . DB::getTablePrefix() . 'users (id, name, email, created_at, updated_at) VALUES (?, ?, ?, datetime(), datetime())', [2, 'Adam Wathan', 'adam@test.com']);
        DB::insert('INSERT INTO ' . DB::getTablePrefix() . 'users (id, name, email, created_at, updated_at) VALUES (?, ?, ?, datetime(), datetime())', [3, 'Taylor Otwell', 'taylor@test.com']);
    }

    /**
     * Create the threads table in the database.
     */
    private function createThreadsTable()
    {
        Schema::create(
            'threads',
            function ($table) {
                $table->increments('id');
                $table->string('subject');
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Create the messages table in the database.
     */
    private function createMessagesTable()
    {
        Schema::create(
            'messages',
            function ($table) {
                $table->increments('id');
                $table->integer('thread_id')->unsigned()->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('messageable_id')->nullable();
                $table->string('messageable_type')->nullable();
                $table->string('actorable_id')->nullable();
                $table->string('actorable_type')->nullable();
                $table->text('body')->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Create the participants table in the database.
     */
    private function createParticipantsTable()
    {
        Schema::create(
            'participants',
            function ($table) {
                $table->increments('id');
                $table->integer('thread_id')->unsigned()->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('messageable_id')->nullable();
                $table->string('messageable_type')->nullable();
                $table->string('actorable_id')->nullable();
                $table->string('actorable_type')->nullable();
                $table->timestamp('last_read')->nullable();
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    private function createActivitiesTable()
    {
        Schema::create(
            'activities',
            function ($table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('causer')->nullable();
                $table->string('type')->nullable();
                $table->string('indentifier')->nullable();
                $table->text('description')->nullable();
                $table->text('request')->nullable();
                $table->text('data')->nullable();
                $table->string('activitable_id')->nullable();
                $table->string('activitable_type')->nullable();
                $table->timestamps();
            }
        );
    }

    private function createNotificationsTable()
    {
        Schema::create(
            'notifications',
            function ($table) {
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('flag')->nullable();
                $table->string('uuid')->nullable();
                $table->string('title')->nullable();
                $table->text('details')->nullable();
                $table->boolean('is_read')->default(false);
                $table->string('notificable_id')->nullable();
                $table->string('notificable_type')->nullable();
                $table->softDeletes();
                $table->timestamps();
            }
        );
    }

    private function createGroupsTable()
    {
        Schema::create(
            'groups',
            function ($table) {
                $table->increments('id');
                $table->string('telegram_id')->nullable();
                $table->string('type')->nullable();
                $table->string('title')->nullable();
                $table->string('language')->nullable();
                $table->string('currency')->nullable();
                $table->timestamp('created_at')->nullable();
            }
        );
    }
}

if (!class_exists('Transmissor\Test\User')) {
    class User extends \Illuminate\Foundation\Auth\User
    {
        use \Transmissor\Traits\Messagable;

        protected $table = 'users';

        protected $fillable = ['name', 'email', 'notify'];
    }
}
