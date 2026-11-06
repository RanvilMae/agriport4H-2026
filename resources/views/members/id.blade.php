@php
    $firstLine = trim(
        $member->first_name . ' ' .
        ($member->middle_name ? mb_substr($member->middle_name, 0, 1) . '.' : '')
    );
    $lastName  = mb_strtoupper(trim($member->last_name . ' ' . ($member->suffix ?? '')));
    $lastSize  = mb_strlen($lastName) > 18 ? 8.5 : (mb_strlen($lastName) > 13 ? 10 : 12);
    $initials  = mb_strtoupper(mb_substr($member->first_name ?? '', 0, 1) . mb_substr($member->last_name ?? '', 0, 1));
    $verified  = (bool) $member->verified_at;
    $issued    = optional($member->verified_at ?? $member->created_at)->format('M d, Y');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $member->member_id }} — 4-H Member ID</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; }

        .card { position: relative; width: 242.65pt; height: 153.07pt; overflow: hidden; background: #ffffff; }
        .break { page-break-after: always; }
        .abs { position: absolute; }

        /* ---------- FRONT ---------- */
        .hdr { top: 0; left: 0; width: 242.65pt; height: 36pt; background: #065f46; }
        .orb-a { top: -34pt; right: -18pt; width: 92pt; height: 92pt; border-radius: 46pt; background: #10b981; opacity: 0.35; }
        .orb-b { top: 8pt; right: 46pt; width: 26pt; height: 26pt; border-radius: 13pt; background: #34d399; opacity: 0.25; }
        .accent { top: 36pt; left: 0; width: 242.65pt; height: 2.5pt; background: #a3e635; }

        .logo-box { top: 7pt; left: 10pt; width: 22pt; height: 22pt; background: #ffffff; border-radius: 6pt; }
        .logo-box img { width: 18pt; height: 18pt; margin: 2pt; }
        .brand { top: 8.5pt; left: 38pt; }
        .brand-1 { font-size: 9pt; font-weight: bold; color: #ffffff; letter-spacing: 1pt; line-height: 1; }
        .brand-2 { font-size: 4.6pt; font-weight: bold; color: #a7f3d0; letter-spacing: 2pt; margin-top: 2.5pt; }

        .tag { top: 12pt; right: 10pt; font-size: 4.8pt; font-weight: bold; color: #ffffff; letter-spacing: 1.2pt;
               border: 0.6pt solid #6ee7b7; border-radius: 7pt; padding: 2pt 6pt; }

        .photo { top: 46pt; left: 12pt; width: 60pt; height: 72pt; border-radius: 6pt; border: 1.5pt solid #d1fae5; }
        .photo-ph { top: 46pt; left: 12pt; width: 60pt; height: 72pt; border-radius: 6pt; background: #ecfdf5;
                    border: 1.5pt solid #d1fae5; text-align: center; }
        .photo-ph span { font-size: 20pt; font-weight: bold; color: #059669; line-height: 72pt; }

        .badge { top: 122.5pt; left: 12pt; width: 63pt; text-align: center; font-size: 4.8pt; font-weight: bold;
                 letter-spacing: 1pt; color: #ffffff; border-radius: 4pt; padding: 1.6pt 0; }
        .badge-ok { background: #059669; }
        .badge-wait { background: #d97706; }

        .info { top: 45pt; left: 84pt; width: 148pt; }
        .lbl { font-size: 4.3pt; font-weight: bold; color: #64748b; letter-spacing: 1.2pt; }
        .last { font-weight: bold; color: #0f172a; line-height: 1.05; margin-top: 1.5pt; }
        .first { font-size: 7pt; color: #334155; margin-top: 1pt; }
        .chip { display: inline-block; margin-top: 5pt; background: #0f172a; color: #ffffff; font-family: 'DejaVu Sans Mono', monospace;
                font-size: 6.4pt; font-weight: bold; letter-spacing: 0.3pt; border-radius: 3pt; padding: 2.2pt 5pt; }

        table.grid { margin-top: 6pt; border-collapse: collapse; width: 148pt; }
        table.grid td { vertical-align: top; padding: 0 4pt 3.5pt 0; width: 50%; }
        .val { font-size: 5.8pt; font-weight: bold; color: #0f172a; margin-top: 0.8pt; }

        .foot { bottom: 0; left: 0; width: 242.65pt; height: 13pt; background: #ecfdf5; border-top: 0.6pt solid #a7f3d0;
                text-align: center; font-size: 4.8pt; font-weight: bold; color: #047857; letter-spacing: 1.6pt; line-height: 13pt; }

        /* ---------- BACK ---------- */
        .side { top: 0; left: 0; width: 92pt; height: 153.07pt; background: #065f46; }
        .orb-c { bottom: -30pt; left: -26pt; width: 84pt; height: 84pt; border-radius: 42pt; background: #10b981; opacity: 0.3; }
        .qr-box { top: 20pt; left: 14pt; width: 64pt; height: 64pt; background: #ffffff; border-radius: 7pt; }
        .qr-box img { width: 56pt; height: 56pt; margin: 4pt; }
        .qr-none { font-size: 4.5pt; color: #64748b; text-align: center; padding-top: 26pt; }
        .scan { top: 90pt; left: 0; width: 92pt; text-align: center; font-size: 4.6pt; font-weight: bold; color: #ffffff; letter-spacing: 1.5pt; }
        .side-id { top: 100pt; left: 0; width: 92pt; text-align: center; font-family: 'DejaVu Sans Mono', monospace;
                   font-size: 5pt; color: #a7f3d0; }

        .back { top: 14pt; left: 104pt; width: 128pt; }
        .back-h { font-size: 7pt; font-weight: bold; color: #065f46; }
        .back-p { font-size: 4.8pt; color: #475569; line-height: 1.4; margin-top: 3pt; }
        table.meta { margin-top: 6pt; border-collapse: collapse; width: 128pt; }
        table.meta td { font-size: 5pt; padding: 1.4pt 0; border-bottom: 0.4pt solid #e2e8f0; }
        table.meta td.k { color: #64748b; width: 42pt; }
        table.meta td.v { color: #0f172a; font-weight: bold; }

        .sign { top: 112pt; left: 104pt; width: 100pt; border-top: 0.6pt solid #94a3b8; padding-top: 1.5pt;
                font-size: 4.4pt; color: #64748b; letter-spacing: 0.8pt; }
        .found { top: 127pt; left: 104pt; width: 128pt; font-size: 3.9pt; color: #94a3b8; line-height: 1.35; }
        .bar { bottom: 0; left: 0; width: 242.65pt; height: 2.5pt; background: #a3e635; }
    </style>
</head>
<body>

    {{-- ===================== FRONT ===================== --}}
    <div class="card break">
        <div class="abs hdr"></div>
        <div class="abs orb-a"></div>
        <div class="abs orb-b"></div>
        <div class="abs accent"></div>

        <div class="abs logo-box">
            @if($logo)<img src="{{ $logo }}" alt="4-H">@endif
        </div>
        <div class="abs brand">
            <div class="brand-1">4-H CLUB</div>
            <div class="brand-2">PHILIPPINES</div>
        </div>
        <div class="abs tag">MEMBER ID</div>

        @if($photo)
            <img class="abs photo" src="{{ $photo }}" alt="Photo">
        @else
            <div class="abs photo-ph"><span>{{ $initials ?: '4H' }}</span></div>
        @endif

        <div class="abs badge {{ $verified ? 'badge-ok' : 'badge-wait' }}">
            {{ $verified ? '✓ VERIFIED' : 'PENDING' }}
        </div>

        <div class="abs info">
            <div class="lbl">MEMBER NAME</div>
            <div class="last" style="font-size: {{ $lastSize }}pt;">{{ $lastName }}</div>
            <div class="first">{{ $firstLine }}</div>

            <div class="chip">{{ $member->member_id ?? 'UNASSIGNED' }}</div>

            <table class="grid">
                <tr>
                    <td>
                        <div class="lbl">REGION</div>
                        <div class="val">{{ $member->region->name ?? '—' }}</div>
                    </td>
                    <td>
                        <div class="lbl">PROVINCE</div>
                        <div class="val">{{ $member->province->name ?? '—' }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lbl">SPECIALIZATION</div>
                        <div class="val">{{ $member->specialization ?? '—' }}</div>
                    </td>
                    <td>
                        <div class="lbl">LSA LEVEL</div>
                        <div class="val">{{ $member->lsa_level ?? 'Standard' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="abs foot">HEAD • HEART • HANDS • HEALTH</div>
    </div>

    {{-- ===================== BACK ===================== --}}
    <div class="card">
        <div class="abs side"></div>
        <div class="abs orb-c"></div>

        <div class="abs qr-box">
            @if($qr)
                <img src="{{ $qr }}" alt="QR code">
            @else
                <div class="qr-none">QR unavailable</div>
            @endif
        </div>
        <div class="abs scan">SCAN TO VERIFY</div>
        <div class="abs side-id">{{ $member->member_id }}</div>

        <div class="abs back">
            <div class="back-h">4-H Club of the Philippines</div>
            <div class="back-p">
                This card certifies that the bearer is a registered member of the 4-H Club.
                It is non-transferable and must be presented upon request.
            </div>

            <table class="meta">
                <tr><td class="k">Organization</td><td class="v">{{ $member->organization->name ?? 'Independent' }}</td></tr>
                <tr><td class="k">Issued</td><td class="v">{{ $issued ?? '—' }}</td></tr>
                <tr><td class="k">Status</td><td class="v">{{ $verified ? 'Verified Member' : 'Pending Verification' }}</td></tr>
                <tr><td class="k">Contact</td><td class="v">{{ $member->contact_no ?: '—' }}</td></tr>
            </table>
        </div>

        <div class="abs sign">AUTHORIZED SIGNATURE</div>
        <div class="abs found">If found, please return to the nearest 4-H Club office or scan the QR code to contact the member's region.</div>
        <div class="abs bar"></div>
    </div>

</body>
</html>