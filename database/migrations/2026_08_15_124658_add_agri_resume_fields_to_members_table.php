<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Education Details
            $table->string('highest_education')->nullable();
            $table->string('degree_course')->nullable();
            $table->string('school_name')->nullable();

            // Farm Metrics & Ownership
            $table->string('land_ownership')->nullable();
            $table->string('farm_area')->nullable();
            $table->integer('years_experience')->nullable(); // Added experience counter

            // RSBSA Registration
            $table->boolean('is_rsbsa_registered')->default(false);
            $table->string('rsbsa_no')->nullable();

            // Skills, Equipment & Bio
            $table->json('farm_equipment')->nullable();
            $table->text('agri_skills')->nullable();
            $table->string('certifications')->nullable();
            $table->text('bio_summary')->nullable(); // Added Professional Bio / Summary
            
            // Document Output
            $table->string('agri_resume_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'highest_education',
                'degree_course',
                'school_name',
                'land_ownership',
                'farm_area',
                'years_experience',
                'is_rsbsa_registered',
                'rsbsa_no',
                'farm_equipment',
                'agri_skills',
                'certifications',
                'bio_summary',
                'agri_resume_path',
            ]);
        });
    }
};