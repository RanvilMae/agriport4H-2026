<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agri-Resume - {{ $member->first_name }} {{ $member->last_name }}</title>
    
    <style>
        @page {
            margin: 25pt 30pt;
            size: portrait;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10pt;
            line-height: 1.4;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Utility Classes */
        .uppercase { text-transform: uppercase; }
        .font-bold { font-weight: bold; }
        .text-emerald { color: #047857; }
        .bg-emerald { background-color: #047857; color: #ffffff; }
        .text-gray { color: #64748b; }
        
        /* Layout Structure */
        .header {
            border-bottom: 2px solid #047857;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-left {
            width: 60%;
            vertical-align: top;
        }

        .header-right {
            width: 40%;
            text-align: right;
            vertical-align: top;
            font-size: 8.5pt;
            color: #475569;
        }

        .name {
            font-size: 18pt;
            font-weight: 900;
            color: #0f172a;
            margin: 0;
            line-height: 1.1;
        }

        .title {
            font-size: 9.5pt;
            font-weight: bold;
            color: #047857;
            margin-top: 3px;
        }

        .id-badge {
            font-size: 8.5pt;
            color: #64748b;
            margin-top: 4px;
        }

        .bio-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
            font-style: italic;
            font-size: 8.5pt;
            color: #334155;
        }

        /* Two Column Body Grid */
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .col-left {
            width: 33%;
            vertical-align: top;
            padding-right: 15px;
            border-right: 1px solid #f1f5f9;
        }

        .col-right {
            width: 67%;
            vertical-align: top;
            padding-left: 15px;
        }

        /* Section Styling */
        .section-title {
            font-size: 8pt;
            font-weight: 900;
            color: #065f46;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid #a7f3d0;
            padding-bottom: 3px;
            margin-top: 0;
            margin-bottom: 8px;
        }

        .info-group {
            margin-bottom: 12px;
        }

        .info-group p {
            margin: 2px 0;
            font-size: 8.5pt;
        }

        /* Badges & Tags */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            margin-right: 3px;
            margin-bottom: 3px;
        }

        .badge-green {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-blue {
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .badge-gray {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        /* Grid Cards inside Right Column */
        .grid-2 {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .grid-2 td {
            width: 50%;
            vertical-align: top;
            padding: 2px;
        }

        .stat-card {
            background-color: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 5px;
            padding: 6px 8px;
        }

        .stat-label {
            font-size: 7pt;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            display: block;
        }

        .stat-value {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0f172a;
        }

        .text-block {
            font-size: 8.5pt;
            color: #334155;
            white-space: pre-line;
            line-height: 1.35;
        }

        .footer {
            margin-top: 25px;
            padding-top: 8px;
            border-top: 1px solid #f1f5f9;
            font-size: 7pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    {{-- Resume Header --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <h1 class="name uppercase">
                        {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }} {{ $member->suffix }}
                    </h1>
                    <div class="title uppercase">
                        {{ $member->specialization ?? 'Agricultural Youth Member' }}
                    </div>
                    <div class="id-badge">
                        Member ID: <strong>{{ $member->member_id ?? 'N/A' }}</strong>
                        @if($member->uid) | UID: <strong>{{ $member->uid }}</strong> @endif
                    </div>
                </td>
                <td class="header-right">
                    <p style="margin: 0 0 2px 0;"><strong>Email:</strong> {{ $member->email }}</p>
                    <p style="margin: 0 0 2px 0;"><strong>Phone:</strong> {{ $member->contact_no ?? 'N/A' }}</p>
                    <p style="margin: 0;"><strong>Address:</strong> {{ implode(', ', array_filter([$member->barangay, $member->city_municipality, $member->district, $member->province?->name, $member->zip_code])) ?: 'N/A' }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Professional Summary --}}
    @if($member->bio_summary)
    <div class="bio-box">
        <strong style="color: #065f46; text-transform: uppercase; font-size: 7.5pt; display: block; margin-bottom: 2px;">Professional Summary</strong>
        "{{ $member->bio_summary }}"
    </div>
    @endif

    {{-- Body Grid --}}
    <table class="content-table">
        <tr>
            {{-- Left Column --}}
            <td class="col-left">
                
                {{-- Demographics --}}
                <div class="info-group">
                    <h3 class="section-title">Demographics</h3>
                    <p><strong>Sex:</strong> {{ ucfirst($member->sex ?? 'N/A') }}</p>
                    <p><strong>Civil Status:</strong> {{ ucfirst($member->civil_status ?? 'N/A') }}</p>
                    <p><strong>DOB:</strong> {{ $member->dob ? $member->dob->format('M d, Y') : 'N/A' }}</p>
                    <p><strong>Occupation:</strong> {{ $member->occupation ?? 'N/A' }}</p>
                </div>

                {{-- Affiliation --}}
                <div class="info-group">
                    <h3 class="section-title">Affiliation & RSBSA</h3>
                    <p><strong>Org:</strong> {{ $member->organization?->name ?? 'N/A' }}</p>
                    <p><strong>Region:</strong> {{ $member->region?->name ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ $member->member_type ?? 'N/A' }}</p>
                    <p>
                        <strong>RSBSA:</strong> 
                        @if($member->is_rsbsa_registered)
                            <span style="color: #047857; font-weight: bold;">Yes</span> ({{ $member->rsbsa_no }})
                        @else
                            <span style="color: #94a3b8;">No</span>
                        @endif
                    </p>
                </div>

                {{-- Education --}}
                <div class="info-group">
                    <h3 class="section-title">Education</h3>
                    <p style="font-weight: bold; color: #0f172a;">{{ $member->highest_education ?? 'N/A' }}</p>
                    <p style="color: #475569;">{{ $member->degree_course }}</p>
                    <p style="color: #94a3b8; font-style: italic;">{{ $member->school_name }}</p>
                </div>

                {{-- Equipment --}}
                @if(!empty($member->farm_equipment))
                <div class="info-group">
                    <h3 class="section-title">Farm Equipment</h3>
                    <div>
                        @foreach((array)$member->farm_equipment as $equipment)
                            <span class="badge badge-gray">{{ $equipment }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

            </td>

            {{-- Right Column --}}
            <td class="col-right">
                
                {{-- Farm Profile --}}
                <div class="info-group">
                    <h3 class="section-title">Farm Profile</h3>
                    <table class="grid-2">
                        <tr>
                            <td>
                                <div class="stat-card">
                                    <span class="stat-label">Land Ownership</span>
                                    <span class="stat-value">{{ $member->land_ownership ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="stat-card">
                                    <span class="stat-label">Farm Area</span>
                                    <span class="stat-value">{{ $member->farm_area ? $member->farm_area . ' Hectares' : 'N/A' }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Crops & Services --}}
                <div class="info-group">
                    <h3 class="section-title">Crops & Services Offered</h3>
                    <table class="grid-2">
                        <tr>
                            <td>
                                <strong style="font-size: 7.5pt; color: #64748b; display: block; margin-bottom: 3px;">Crops Cultivated:</strong>
                                @if(!empty($member->crops))
                                    @foreach((array)$member->crops as $crop)
                                        <span class="badge badge-green">{{ $crop }}</span>
                                    @endforeach
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 8pt;">None specified</span>
                                @endif
                            </td>
                            <td>
                                <strong style="font-size: 7.5pt; color: #64748b; display: block; margin-bottom: 3px;">Services Offered:</strong>
                                @if(!empty($member->services))
                                    @foreach((array)$member->services as $service)
                                        <span class="badge badge-blue">{{ $service }}</span>
                                    @endforeach
                                @else
                                    <span style="color: #94a3b8; font-style: italic; font-size: 8pt;">None specified</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Programs & Classifications --}}
                <div class="info-group">
                    <h3 class="section-title">Programs & Classifications</h3>
                    <table class="grid-2">
                        <tr>
                            <td>
                                <p><strong>HVCDP Category:</strong> {{ $member->hvcdp_category ?? 'N/A' }}</p>
                                <p><strong>LSA Level:</strong> {{ $member->lsa_level ?? 'N/A' }}</p>
                                <p><strong>LSA Type:</strong> {{ $member->lsa_type ?? 'N/A' }}</p>
                            </td>
                            <td>
                                <p><strong>Internship:</strong> {{ $member->internship ?? 'N/A' }}</p>
                                <p><strong>Scholarship:</strong> {{ $member->scholarship ?? 'N/A' }}</p>
                                <p><strong>Training Course:</strong> {{ $member->training_course ?? 'N/A' }}</p>
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- Skills --}}
                <div class="info-group">
                    <h3 class="section-title">Agricultural Skills & Specializations</h3>
                    <div class="text-block">
                        {{ $member->agri_skills ?? 'No specific skills defined.' }}
                    </div>
                </div>

                {{-- Certifications --}}
                <div class="info-group">
                    <h3 class="section-title">Certifications & Trainings</h3>
                    <div class="text-block">
                        {{ $member->certifications ?? 'No certifications added.' }}
                    </div>
                </div>

            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="footer" style="width: 100%;">
        <tr>
            <td style="text-align: left;">Official Document • 4-H Club Philippines</td>
            <td style="text-align: right;">Generated: {{ now()->format('M d, Y') }}</td>
        </tr>
    </table>

</body>
</html>