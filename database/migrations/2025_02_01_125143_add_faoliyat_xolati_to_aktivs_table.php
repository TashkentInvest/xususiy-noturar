<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFaoliyatXolatiToAktivsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivs', function (Blueprint $table) {
            $table->enum('faoliyat_xolati', ['work', 'notwork'])->nullable();
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
            $table->dropColumn('faoliyat_xolati');
        });
    }
}
