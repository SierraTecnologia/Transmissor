<?php

use Transmissor\Models\Messenger\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadsTable extends Migration
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
        Schema::create(Models::table('threads'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->timestamps();
        });
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
        Schema::dropIfExists(Models::table('threads'));
    }
}
