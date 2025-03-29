<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_managements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();           // e.g. Бектемир тумани
            $table->string('district')->nullable();           // e.g. Бектемир тумани
            $table->string('address')->nullable();            // e.g. Сувсоз 26а уй
            $table->string('inn')->nullable();                // e.g. 306390310
            $table->string('organization')->nullable();       // e.g. Бектемир Сифатли Хизмат Унвирсал
            $table->string('representative')->nullable();     // e.g. К.Сахарова
            $table->string('phone')->nullable();              // e.g. 97-736-37-86
            $table->string('service_phone')->nullable();      // e.g. 71-295-71-66
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
        Schema::dropIfExists('company_managements');
    }
}
