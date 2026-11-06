<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Profiles are created at registration with only the basics.
     * These fields are filled in later by the member, so they may be empty.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('sex')->nullable()->change();
            $table->string('civil_status')->nullable()->change();
            $table->date('dob')->nullable()->change();
            $table->string('contact_no')->nullable()->change();
            $table->unsignedBigInteger('region_id')->nullable()->change();
            $table->unsignedBigInteger('province_id')->nullable()->change();
            $table->string('city_municipality')->nullable()->change();
            $table->string('barangay')->nullable()->change();
            $table->string('member_type')->nullable()->change();
            $table->string('specialization')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('sex')->nullable(false)->change();
            $table->string('civil_status')->nullable(false)->change();
            $table->date('dob')->nullable(false)->change();
            $table->string('contact_no')->nullable(false)->change();
            $table->unsignedBigInteger('region_id')->nullable(false)->change();
            $table->unsignedBigInteger('province_id')->nullable(false)->change();
            $table->string('city_municipality')->nullable(false)->change();
            $table->string('barangay')->nullable(false)->change();
            $table->string('member_type')->nullable(false)->change();
            $table->string('specialization')->nullable(false)->change();
        });
    }
};