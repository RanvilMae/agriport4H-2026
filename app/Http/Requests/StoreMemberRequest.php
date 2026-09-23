<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled via Controller Policy
    }

    protected function prepareForValidation(): void
    {
        $user = auth()->user();

        // Merge dynamic "Others" inputs
        if ($this->member_type_select === 'Others') {
            $this->merge(['member_type' => $this->other_member_type]);
        } elseif ($this->has('member_type_select')) {
            $this->merge(['member_type' => $this->member_type_select]);
        }

        if ($this->training_type_select === 'Others') {
            $this->merge(['training_course' => $this->other_training]);
        } elseif ($this->has('training_type_select')) {
            $this->merge(['training_course' => $this->training_type_select]);
        }

        // Force region_id for Presidents / Coordinators
        if (in_array($user->role, ['President', 'Coordinator'])) {
            $this->merge(['region_id' => $user->region_id]);
        }
    }

    public function rules(): array
    {
        $maxAgeDate = now()->subYears(30)->format('Y-m-d');
        $minAgeDate = now()->subYears(10)->format('Y-m-d');

        return [
            'last_name'           => 'required|string|max:255',
            'first_name'          => 'required|string|max:255',
            'middle_name'         => 'nullable|string|max:255',
            'suffix'              => 'nullable|string',
            'sex'                 => 'required|in:Male,Female',
            'civil_status'        => 'required|string',
            'dob'                 => ['required', 'date', "after_or_equal:$maxAgeDate", "before_or_equal:$minAgeDate"],
            'contact_no'          => 'required|string|max:20',
            'email'               => 'required|email|unique:members,email',
            'region_id'           => 'required|exists:regions,id',
            'province_id'         => 'required|exists:provinces,id',
            'city_municipality'   => 'required|string',
            'district'            => 'nullable|string',
            'barangay'            => 'required|string',
            'zip_code'            => 'nullable|string|max:4',
            'member_type'         => 'required|string',
            'occupation'          => 'nullable|string|max:255',
            'organization_id'     => 'nullable|exists:organizations,id',
            'specialization'      => 'required|string',
            'hvcdp_category'      => 'nullable|required_if:specialization,HVCDP,Combination|string',
            'crops'               => 'nullable|array',
            'services'            => 'required|array',
            'internship'          => 'nullable|string',
            'scholarship'         => 'nullable|string',
            'lsa_level'           => 'nullable|string',
            'lsa_type'            => 'nullable|string',
            'training_course'     => 'nullable|string',
            'highest_education'   => 'nullable|string|max:100',
            'degree_course'       => 'nullable|string|max:150',
            'school_name'         => 'nullable|string|max:200',
            'land_ownership'      => 'nullable|string',
            'farm_area'           => 'nullable|string|max:50',
            'is_rsbsa_registered' => 'nullable|boolean',
            'rsbsa_no'            => 'nullable|string|max:50',
            'farm_equipment'      => 'nullable|array',
            'farm_equipment.*'    => 'string',
            'agri_skills'         => 'nullable|string|max:1000',
            'certifications'      => 'nullable|string|max:255',
            'agri_resume_file'    => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'bio_summary'         => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'dob.after_or_equal'  => 'The member must be 30 years old or younger.',
            'dob.before_or_equal' => 'The member must be at least 10 years old.',
        ];
    }
}