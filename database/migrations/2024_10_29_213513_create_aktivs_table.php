<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAktivsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_street_id')->nullable();
            $table->foreign('sub_street_id')->references('id')->on('sub_streets')->onDelete('cascade');

            $table->unsignedBigInteger('street_id')->nullable();
            $table->foreign('street_id')->references('id')->on('streets')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('action', ['created', 'updated', 'deleted'])->nullable();
            $table->timestamp('action_timestamp')->nullable();
            $table->softDeletes();

            $table->string('object_name');
            $table->string('balance_keeper');
            $table->string('location');
            $table->decimal('land_area', 10, 2);
            $table->decimal('building_area', 10, 2)->nullable();
            $table->string('gas');
            $table->string('water');
            $table->string('home_number')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('electricity');
            $table->text('additional_info')->nullable();
            $table->string('geolokatsiya')->nullable(); // Allow NULL values
            $table->decimal('latitude', 10, 7)->nullable(); // Allow NULL values
            $table->decimal('longitude', 10, 7)->nullable(); // Allow NULL values
            $table->string('kadastr_raqami')->nullable();

            $table->string('kadastr_pdf')->nullable();
            $table->string('hokim_qarori_pdf')->nullable();
            $table->string('transfer_basis_pdf')->nullable();

            $table->enum('building_type', ['yer', 'kopQavatliUy', 'AlohidaSavdoDokoni'])->nullable();

            $table->string('document_type')->nullable(); // Бошқа ҳужжатлар (ҳоким қарори, ордер, ижара шартнома)
            $table->string('reason_not_active')->nullable(); // Фаолият юритмаётганлиги сабаби
            $table->string('ready_for_rent')->nullable(); // Ижарага беришга тайёрлиги (справочник)
            $table->string('rental_agreement_status')->nullable(); // Ижара шартномасини туздириш (справочник)
            $table->string('unused_duration')->nullable(); // Қанча вақтдан буён фойдаланилмайди (справочник)
        
            $table->string('provided_assistance')->nullable(); // Берилган амалий ёрдам
            $table->date('start_date')->nullable(); // Фаолият юритишни бошлаган сана
            $table->text('additional_notes')->nullable(); // Изоҳ киритилган маълумотлардаги
            $table->boolean('working_24_7')->default(false); // 24/7 режимда ишлайдими (справочник)
        
            $table->string('owner')->nullable(); // Мулкдор
            $table->string('STIR')->nullable(); // СТИР

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
        Schema::dropIfExists('aktivs');
    }
}
