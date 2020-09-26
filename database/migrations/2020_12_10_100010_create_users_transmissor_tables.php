<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTransmissorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'users',
            function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'message_count')) {
                    $table->integer('message_count')->default(0);
                }
                if (!Schema::hasColumn('users', 'message_new_count')) {
                    $table->integer('message_new_count')->default(0);
                }
                if (!Schema::hasColumn('users', 'notification_count')) {
                    $table->integer('notification_count')->default(0);
                }
                if (!Schema::hasColumn('users', 'notification_new_count')) {
                    $table->integer('notification_new_count')->default(0);
                }
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
    }
}
