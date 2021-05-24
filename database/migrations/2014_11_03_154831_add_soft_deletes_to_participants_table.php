<?php

use Transmissor\Models\Messenger\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesToParticipantsTable extends Migration
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
        Schema::table(
            Models::table('participants'), function (Blueprint $table) {
                $table->softDeletes();
            }
        );
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
        Schema::table(
            Models::table('participants'), function (Blueprint $table) {
                $table->dropSoftDeletes();
            }
        );
    }
}
