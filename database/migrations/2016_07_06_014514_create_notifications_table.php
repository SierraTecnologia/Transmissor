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
        if (!\Muleta\Modules\Features\Resources\FeatureHelper::hasActiveFeature(
            [
                'transmissor',
            ]
        )){
            \Log::debug('Migration Ignorada por causa de Feature transmissor');
            return ;
        }
        Schema::hasTable('notifications') || Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->nullable()->index();
            $table->string('flag')->nullable();
            $table->string('uuid')->nullable();
            $table->string('title')->nullable();
            $table->text('details')->nullable();
            $table->boolean('is_read')->default(false);

            $table->string('notificable_id')->nullable();
            $table->string('notificable_type')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        // Schema::hasTable('notifications') || Schema::create('notifications', function (Blueprint $table) {
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
        if (!\Muleta\Modules\Features\Resources\FeatureHelper::hasActiveFeature(
            [
                'transmissor',
            ]
        )){
            \Log::debug('Migration Ignorada por causa de Feature transmissor');
            return ;
        }
        Schema::dropIfExists('notifications');
    }
}
