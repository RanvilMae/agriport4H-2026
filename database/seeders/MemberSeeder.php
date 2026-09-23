<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch existing valid IDs from referenced tables, or fallback to 1
        $regionId       = DB::table('regions')->value('id') ?? 1;
        $provinceId     = DB::table('provinces')->value('id') ?? 1;
        $organizationId = DB::table('organizations')->value('id') ?? null; // Nullable if migration allows

        $members = [
            [
                // Section I: Identity & Demographics
                'first_name'          => 'Juan',
                'middle_name'         => 'Rios',
                'last_name'           => 'Dela Cruz',
                'suffix'              => 'Jr.',
                'sex'                 => 'Male',
                'civil_status'        => 'Married',
                'dob'                 => '1995-06-15',
                'contact_no'          => '09171234567',
                'email'               => 'juan.delacruz@example.com',
                'member_id'           => 'AGRI-2026-001',
                'uid'                 => '4H-03-2026-000-00001',

                // Section II: Address & Location Details
                'region_id'           => $regionId,
                'province_id'         => $provinceId,
                'city_municipality'   => 'Muñoz',
                'district'            => 'District 2',
                'barangay'            => 'Bantug',
                'zip_code'            => '3119',

                // Section III: Professional & Program Details
                'member_type'         => 'Youth Farmer',
                'occupation'          => 'Rice & Corn Farmer',
                'organization_id'     => $organizationId,
                'specialization'      => 'Crops',
                'hvcdp_category'      => null,
                'crops'               => ['Palay', 'Yellow Corn'],
                'services'            => ['Custom Plowing', 'Land Preparation'],
                'internship'          => 'YAFP 2024 Batch',
                'scholarship'         => 'DA Youth in Agriculture Grant',
                'lsa_level'           => 'Level 1',
                'lsa_type'            => 'School for Practical Agriculture',
                'training_course'     => 'Inbred Rice Seed Production',

                // Section IV: Agri-Resume & Field Experience Details
                'highest_education'   => 'College Degree',
                'degree_course'       => 'BS Agriculture',
                'school_name'         => 'Central Luzon State University',
                'land_ownership'      => 'Owner',
                'farm_area'           => '3.5 Hectares',
                'years_experience'    => 12,
                'is_rsbsa_registered' => true,
                'rsbsa_no'            => '03-49-11-001-000123',
                'farm_equipment'      => ['Hand Tractor', 'Four-Wheel Tractor', 'Rice Transplanter'],
                'agri_skills'         => 'Integrated Pest Management (IPM), Hybrid Seed Production, Drip Irrigation Setup',
                'certifications'      => 'NC II Agricultural Crops Production, GAP Certified',
                'bio_summary'         => 'Dedicated rice and corn producer with solid experience in modern farming techniques and sustainable crop management in Nueva Ecija.',
                'agri_resume_path'    => null,
            ],
            [
                // Section I: Identity & Demographics
                'first_name'          => 'Maria',
                'middle_name'         => 'Santos',
                'last_name'           => 'Gomez',
                'suffix'              => null,
                'sex'                 => 'Female',
                'civil_status'        => 'Single',
                'dob'                 => '1998-03-22',
                'contact_no'          => '09289876543',
                'email'               => 'maria.gomez@example.com',
                'member_id'           => 'AGRI-2026-002',
                'uid'                 => '4H-CAR-2026-000-00002',

                // Section II: Address & Location Details
                'region_id'           => $regionId,
                'province_id'         => $provinceId,
                'city_municipality'   => 'La Trinidad',
                'district'            => 'District 1',
                'barangay'            => 'Puguis',
                'zip_code'            => '2601',

                // Section III: Professional & Program Details
                'member_type'         => '4H Club Member',
                'occupation'          => 'High-Value Vegetable Grower',
                'organization_id'     => $organizationId,
                'specialization'      => 'HVCDP',
                'hvcdp_category'      => 'Highland Vegetables',
                'crops'               => ['Romaine Lettuce', 'Bell Pepper', 'Strawberries'],
                'services'            => ['Fresh Produce Supply', 'Hydroponics Setup Consultation'],
                'internship'          => null,
                'scholarship'         => 'EASAG Scholar',
                'lsa_level'           => 'Level 2',
                'lsa_type'            => 'Agri-Tourism Site',
                'training_course'     => 'Organic Vegetable Production NC II',

                // Section IV: Agri-Resume & Field Experience Details
                'highest_education'   => 'High School',
                'degree_course'       => null,
                'school_name'         => 'La Trinidad National High School',
                'land_ownership'      => 'Tenant / Lease',
                'farm_area'           => '1.2 Hectares',
                'years_experience'    => 8,
                'is_rsbsa_registered' => true,
                'rsbsa_no'            => '14-11-05-002-000456',
                'farm_equipment'      => ['Knapsack Sprayer', 'Power Sprayer', 'Greenhouse System'],
                'agri_skills'         => 'Organic Vegetable Production, Hydroponics, Post-Harvest Handling',
                'certifications'      => 'TESDA Organic Agriculture Production NC II',
                'bio_summary'         => 'Specializes in high-value highland crop cultivation including lettuce, tomatoes, and bell peppers utilizing protected structures.',
                'agri_resume_path'    => null,
            ],
        ];

        foreach ($members as $member) {
            Member::updateOrCreate(
                ['email' => $member['email']],
                $member
            );
        }
    }
}