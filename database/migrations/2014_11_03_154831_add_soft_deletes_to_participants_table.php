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
        $tableName = Models::table('participants');
        if (Schema::hasTable($tableName) && ! Schema::hasColumn($tableName, 'deleted_at')) {
            Schema::table(
                $tableName, function (Blueprint $table) {
                    $table->softDeletes();
                }
            );
        }
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
        $tableName = Models::table('participants');
        if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'deleted_at')) {
            Schema::table(
                $tableName, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                }
            );
        }
    }
}
