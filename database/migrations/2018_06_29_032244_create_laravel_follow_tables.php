<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaravelFollowTables extends Migration
{
    /**
     * Run the migrations.
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
        Schema::create(
            \Illuminate\Support\Facades\Config::get('follow.followable_table', 'followables'), function (Blueprint $table) {
                $userForeignKey = \Illuminate\Support\Facades\Config::get('follow.users_table_foreign_key', 'person_code');

                // // Laravel 5.8 session user is unsignedBigInteger
                // // https://github.com/laravel/framework/pull/28206/files
                // if ((float) app()->version() >= 5.8) {
                //     $table->unsignedBigInteger($userForeignKey);
                // } else {
                //     $table->unsignedInteger($userForeignKey);
                // }
                $table->string($userForeignKey);

                $table->string('followable_id');
                $table->string('followable_type')->index();
                $table->string('relation')->default('follow')->comment('follow/like/subscribe/favorite/upvote/downvote');
                $table->softDeletes();
                $table->timestamps();

                // $table->foreign($userForeignKey)
                //     ->references(\Illuminate\Support\Facades\Config::get('follow.users_table_primary_key', 'id'))
                //     ->on(\Illuminate\Support\Facades\Config::get('follow.users_table_name', 'users'))
                //     ->onUpdate('cascade')
                //     ->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
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
        Schema::table(
            \Illuminate\Support\Facades\Config::get('follow.followable_table', 'followables'), function ($table) {
                $table->dropForeign(\Illuminate\Support\Facades\Config::get('follow.followable_table', 'followables').'_user_id_foreign');
            }
        );

        Schema::drop(\Illuminate\Support\Facades\Config::get('follow.followable_table', 'followables'));
    }
}
