<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that let an Admin manage the member form:
     *  - form_sections        : groups of questions (built-in or custom), can be hidden
     *  - form_fields          : questions, their dropdown options, visibility and required flag
     *  - member_field_values  : members' answers to custom questions
     */
    public function up(): void
    {
        Schema::create('form_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('description')->nullable();
            $table->boolean('is_builtin')->default(false);
            $table->boolean('is_locked')->default(false);   // locked sections can't be hidden
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_section_id')->constrained('form_sections')->cascadeOnDelete();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('type');                          // text, textarea, number, date, select, radio, checkboxes, yes_no, file
            $table->json('options')->nullable();             // [{value, label}, ...] for select / radio / checkboxes
            $table->string('help_text')->nullable();
            $table->boolean('is_builtin')->default(false);
            $table->boolean('is_locked')->default(false);    // locked fields can't be hidden (needed for the ID card)
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_required')->default(false);  // applies to custom questions
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('member_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('form_field_id')->constrained('form_fields')->cascadeOnDelete();
            $table->text('value')->nullable();               // checkboxes are stored as JSON
            $table->timestamps();

            $table->unique(['member_id', 'form_field_id']);
        });

        $this->seedDefaults();
    }

    public function down(): void
    {
        Schema::dropIfExists('member_field_values');
        Schema::dropIfExists('form_fields');
        Schema::dropIfExists('form_sections');
    }

    /**
     * The sections and dropdowns that already exist on the member form,
     * so the Admin starts from exactly what members see today.
     */
    private function seedDefaults(): void
    {
        $now = now();
        $opt = fn (array $pairs) => json_encode(array_map(
            fn ($value, $label) => ['value' => $value, 'label' => $label],
            array_keys($pairs),
            $pairs
        ));
        $same = fn (array $values) => $opt(array_combine($values, $values));

        $sections = [
            // key, title, locked, sort
            ['education',      'Educational Background',                false, 10],
            ['farm_profile',   'Farm Profile & Scale of Operation',     false, 20],
            ['specialization', 'Specialization & Crops',                true,  30],
            ['machinery',      'Machinery & Agricultural Skills',       false, 40],
            ['certifications', 'Certifications & Supporting Documents', false, 50],
        ];

        $sectionIds = [];
        foreach ($sections as [$key, $title, $locked, $sort]) {
            $sectionIds[$key] = DB::table('form_sections')->insertGetId([
                'key' => $key, 'title' => $title, 'is_builtin' => true, 'is_locked' => $locked,
                'is_visible' => true, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        $fields = [
            // section, key, label, type, options, locked, sort
            ['education', 'highest_education', 'Highest Attainment', 'select', $opt([
                'High School'           => 'High School Student / Graduate',
                'Vocational'            => 'Vocational / Technical',
                'College Undergraduate' => 'College Undergraduate',
                'College Graduate'      => 'College Graduate',
                'Post-Graduate'         => 'Post-Graduate',
            ]), false, 10],
            ['education', 'degree_course', 'Degree / Course (if applicable)', 'text', null, false, 20],
            ['education', 'school_name',   'School / Institution',            'text', null, false, 30],

            ['farm_profile', 'land_ownership', 'Land Tenure / Ownership', 'select', $opt([
                'Owner'    => 'Owner / Titled',
                'Tenant'   => 'Tenant',
                'Lessee'   => 'Lessee / Renter',
                'Co-Owner' => 'Family Co-Owner',
            ]), false, 10],
            ['farm_profile', 'farm_area',           'Total Farm Area (Hectares / SqM)', 'text',   null, false, 20],
            ['farm_profile', 'is_rsbsa_registered', 'RSBSA Registered?',                'yes_no', null, false, 30],

            ['specialization', 'specialization', 'As a 4-H member, which of the following is your specialization?', 'select', $opt([
                'HVCDP'       => 'High Value Crops (HVCDP)',
                'Livestock'   => 'Livestock',
                'Fisheries'   => 'Fisheries',
                'Rice'        => 'Rice',
                'Corn'        => 'Corn',
                'Poultry'     => 'Poultry',
                'Combination' => 'Combination of Multiple Fields',
            ]), true, 10],
            ['specialization', 'hvcdp_category', 'HVCDP Category', 'select', $opt([
                'Vegetables'         => 'Vegetables',
                'Fruits'             => 'Fruits',
                'Industrial Crops'   => 'Industrial Crops',
                'Alternative Staple' => 'Alternative Staple Food',
                'Cut Across'         => 'Cut Across (Multiple)',
            ]), false, 20],

            ['machinery', 'farm_equipment', 'Farm Equipment / Technologies Managed', 'checkboxes', $same([
                'Tractor / Hand Tractor', 'Drip Irrigation', 'Greenhouse / Hydroponics', 'Drone / Precision Ag',
                'Solar Pump', 'Processing Machinery', 'Livestock Feed Mill',
            ]), false, 10],
            ['machinery', 'agri_skills', 'Specialized Agricultural Skills', 'textarea', null, false, 20],

            ['certifications', 'certifications', 'NC Certifications (e.g., TESDA NC II Organic Agriculture)', 'text', null, false, 10],
            ['certifications', 'member_file',    'Upload Agri-Resume / Curriculum Vitae (PDF/Docx)',          'file', null, false, 20],
        ];

        foreach ($fields as [$section, $key, $label, $type, $options, $locked, $sort]) {
            DB::table('form_fields')->insert([
                'form_section_id' => $sectionIds[$section], 'key' => $key, 'label' => $label, 'type' => $type,
                'options' => $options, 'is_builtin' => true, 'is_locked' => $locked, 'is_visible' => true,
                'is_required' => false, 'sort_order' => $sort, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
};