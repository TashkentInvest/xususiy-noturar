<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIjaragaBerishgaTayyorligiToAktivsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivs', function (Blueprint $table) {
            $table->enum('ijaraga_berishga_tayyorligi', ['not', 'yeap'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivs', function (Blueprint $table) {
            $table->dropColumn('ijaraga_berishga_tayyorligi');
        });
    }
}
