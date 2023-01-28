<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('receiver_id')->nullable()->after('id');
            $table->integer('sender_id')->nullable()->after('receiver_id');
            $table->string('title')->nullable()->after('sender_id');
            $table->text('data')->nullable()->after('id');
            $table->dateTime("read_at")->nullable()->after('data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn([
                'receiver_id', 'sender_id', 'title', 'data', 'read_at'
            ]);
        });
    }
};
