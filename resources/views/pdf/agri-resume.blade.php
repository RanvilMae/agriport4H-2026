<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agri-Resume - {{ $member->first_name }} {{ $member->last_name }}</title>
    <style>
        @page {
            margin: 25pt 30pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11pt;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #059669;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header-title {
            font-size: 20pt;
            font-weight: bold;
            color: #065f46;
            margin: 0;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }
        .member-id-badge {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 4px 10px;
            font-size: 9pt;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #065f46;
            background-color: #f0fdf4;
            padding: 5px 8px;
            border-left: 4px solid #059669;
            margin-top: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 5px 4px;
            vertical-align: top;
        }
        .label {
            font-size: 8.5pt;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            display: block;
        }
        .value {
            font-size: 10pt;
            font-weight: bold;
            color: #1e293b;
        }
        .bio-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 6px;
            font-style: italic;
            font-size: 9.5pt;
            color: #334155;
            margin-top: 5px;
        }
        .badge {
            display: inline-block;
            background-color: #e2e8f0;
            color: #334155;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 3px;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td>
                <h1 class="header-title">{{ $member->first_name }} {{ $member->middle_name ? substr($member->middle_name, 0, 1) . '.' : '' }} {{ $member->last_name }} {{ $member->suffix }}</h1>
                <div class="header-subtitle">Official 4-H Philippines Agricultural Profile CV</div>
            </td>
            <td style="text-align: right;">
                <span class="member-id-badge">ID: {{ $member->member_id ?? 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <!-- Personal & Contact Information -->
    <div class="section-title">Personal & Contact Details</div>
    <table class="info-table">
        <tr>
            <td width="33%">
                <span class="label">Email Address</span>
                <span class="value">{{ $member->email }}</span>
            </td>
            <td width="33%">
                <span class="label">Contact Number</span>
                <span class="value">{{ $member->contact_no ?? 'N/A' }}</span>
            </td>
            <td width="34%">
                <span class="label">Sex / Civil Status</span>
                <span class="value">{{ $member->sex ?? 'N/A' }} / {{ $member->civil_status ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Region</span>
                <span class="value">{{ $member->region->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="label">Province</span>
                <span class="value">{{ $member->province->name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="label">City / Barangay</span>
                <span class="value">{{ $member->city_municipality }}, {{ $member->barangay }}</span>
            </td>
        </tr>
    </table>

    <!-- Professional & Agricultural Credentials -->
    <div class="section-title">Agricultural Background & Operations</div>
    <table class="info-table">
        <tr>
            <td width="33%">
                <span class="label">Specialization</span>
                <span class="value">{{ $member->specialization ?? 'General Agriculture' }}</span>
            </td>
            <td width="33%">
                <span class="label">Current Occupation</span>
                <span class="value">{{ $member->occupation ?? 'Farmer / Agri-practitioner' }}</span>
            </td>
            <td width="34%">
                <span class="label">Educational Attainment</span>
                <span class="value">{{ $member->highest_education ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="label">Farm Tenure / Ownership</span>
                <span class="value">{{ $member->land_ownership ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="label">Farm Area Size</span>
                <span class="value">{{ $member->farm_area ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="label">RSBSA Registered</span>
                <span class="value">{{ $member->is_rsbsa_registered ? 'Yes (' . $member->rsbsa_no . ')' : 'No' }}</span>
            </td>
        </tr>
    </table>

    <!-- Equipment & Services -->
    @if(!empty($member->farm_equipment) && is_array($member->farm_equipment))
    <div class="section-title">Farm Machinery & Equipment</div>
    <div style="margin-bottom: 10px;">
        @foreach($member->farm_equipment as $equipment)
            <span class="badge">{{ $equipment }}</span>
        @endforeach
    </div>
    @endif

    <!-- Bio / Summary -->
    @if($member->bio_summary)
    <div class="section-title">Professional Summary & Expertise</div>
    <div class="bio-box">
        "{{ $member->bio_summary }}"
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        Generated on {{ now()->format('F d, Y') }} | 4-H Club Agricultural Member Registry System
    </div>

</body>
</html>