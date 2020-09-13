<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');

            $table->string('flag');
            $table->string('uuid');
            $table->string('title');
            $table->text('details')->nullable();
            $table->boolean('is_read')->default(false);

            $table->string('notificable_id');
            $table->string('notificable_type');
            $table->softDeletes();
            $table->timestamps();
        });
        // Schema::create('notifications', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('from_user_id')->index();
        //     $table->integer('user_id')->index();
        //     $table->integer('topic_id')->index();
        //     $table->integer('reply_id')->nullable()->index();
        //     $table->text('body')->nullable();
        //     $table->string('type')->index();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
