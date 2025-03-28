<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAktivsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivs', function (Blueprint $table) {
            $table->boolean('is_status_yer_tola')->default(false)->after('faoliyat_xolati');
            $table->boolean('does_exists_yer_tola')->default(false)->after('is_status_yer_tola');
            $table->boolean('does_can_we_use_yer_tola')->default(false)->after('does_exists_yer_tola');
            $table->boolean('does_ijaraga_berilgan_yer_tola')->default(false)->after('does_can_we_use_yer_tola');
            $table->decimal('ijaraga_berilgan_qismi_yer_tola', 10, 2)->nullable()->after('does_ijaraga_berilgan_yer_tola');
            $table->decimal('ijaraga_berilmagan_qismi_yer_tola', 10, 2)->nullable()->after('ijaraga_berilgan_qismi_yer_tola');
            $table->decimal('texnik_qismi_yer_tola', 10, 2)->nullable()->after('ijaraga_berilmagan_qismi_yer_tola');
            $table->decimal('oylik_ijara_narxi_yer_tola', 10, 2)->nullable()->after('texnik_qismi_yer_tola');
            $table->json('faoliyat_turi')->nullable()->after('oylik_ijara_narxi_yer_tola');
            $table->boolean('does_yer_tola_ijaraga_berish_mumkin')->default(false)->after('faoliyat_turi');
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
            $table->dropColumn([
                'is_status_yer_tola',
                'does_exists_yer_tola',
                'does_can_we_use_yer_tola',
                'does_ijaraga_berilgan_yer_tola',
                'ijaraga_berilgan_qismi_yer_tola',
                'ijaraga_berilmagan_qismi_yer_tola',
                'texnik_qismi_yer_tola',
                'oylik_ijara_narxi_yer_tola',
                'faoliyat_turi',
                'does_yer_tola_ijaraga_berish_mumkin',
            ]);
        });
    }
}
