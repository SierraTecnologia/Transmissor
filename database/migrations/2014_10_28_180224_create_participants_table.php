<?php

use Transmissor\Models\Messenger\Models;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            Models::table('participants'), function (Blueprint $table) {
                $table->increments('id');
                // threads
                $table->string('messageable_id');
                $table->string('messageable_type');
    
                // actor
                $table->string('actorable_id');
                $table->string('actorable_type');
                $table->timestamp('last_read')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists(Models::table('participants'));
    }
}
