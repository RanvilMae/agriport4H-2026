@php
    /*
     | 4-H Member ID card: ATM / national ID size (CR80: 85.6 x 54 mm).
     | $sheet = true  -> A4 page with front + back at exact size and cut lines (regular printers)
     | $sheet = false -> two card-size pages, front then back (PVC card printers)
     */
    $sheet = $sheet ?? true;

    // Full name on ONE line: FIRST M. LAST SUFFIX, smaller text for longer names
    $fullName = mb_strtoupper(trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([
        $member->first_name,
        $member->middle_name ? mb_substr($member->middle_name, 0, 1) . '.' : null,
        $member->last_name,
        $member->suffix,
    ])))));
    // Shrink the name so it always fits on one line (about 0.72 x font size per letter, 152pt wide)
    $len      = max(1, mb_strlen($fullName));
    $nameSize = round(max(3.6, min(10.5, 152 / ($len * 0.72))), 1);

    $initials = mb_strtoupper(mb_substr($member->first_name ?? '', 0, 1) . mb_substr($member->last_name ?? '', 0, 1));
    $club     = $member->organization->name ?? '';

    // Club: one line when it fits, two smaller lines for very long names
    $clubLen  = max(1, mb_strlen($club));
    $clubWrap = $clubLen > 40;
    $clubSize = $clubWrap ? 5.2 : round(max(4.4, min(6.6, 140 / ($clubLen * 0.72))), 1);

    $issuedRaw = $member->verified_at ?: $member->created_at;
    $issued    = $issuedRaw ? \Illuminate\Support\Carbon::parse($issuedRaw)->format('M d, Y') : '—';

    // Work even if the controller doesn't pass these
    $photoFile = $member->photo_path ? storage_path('app/public/' . $member->photo_path) : null;
    $photo = $photo ?? ($photoFile && is_file($photoFile)
        ? 'data:' . mime_content_type($photoFile) . ';base64,' . base64_encode(file_get_contents($photoFile))
        : null);
    $logo  = $logo ?? (is_file(public_path('images/logo.png'))
        ? 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')))
        : null);
    $qr    = $qr ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $member->member_id }} — 4-H Member ID</title>
    <style>
        @page { margin: 0; }
        * { margin: 0; padding: 0; line-height: 1.15; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; }

        /* Card = 85.6 x 54 mm */
        .card { position: relative; width: 85.6mm; height: 54mm; overflow: hidden; background: #ffffff; }
        .break { page-break-after: always; }
        .abs { position: absolute; }

        /* ---------- A4 sheet (regular printers) ---------- */
        .sheet-title { position: absolute; top: 14mm; left: 14.4mm; font-size: 10pt; font-weight: bold; color: #065f46; }
        .sheet-note  { position: absolute; top: 20mm; left: 14.4mm; width: 181mm; font-size: 7pt; color: #64748b; line-height: 1.4; }
        .side-label  { position: absolute; top: 31mm; font-size: 6.5pt; font-weight: bold; color: #94a3b8; letter-spacing: 1.5pt; }
        .on-sheet    { position: absolute; top: 35mm; }
        .cut         { position: absolute; top: 35mm; width: 85.6mm; height: 54mm; border: 0.5pt dashed #94a3b8; }
        .front-pos   { left: 14.4mm; }
        .back-pos    { left: 110mm; }

        /* ---------- FRONT ---------- */
        .hdr { top: 0; left: 0; width: 85.6mm; height: 36pt; background: #065f46; }
        .orb-a { top: -38pt; right: -14pt; width: 68pt; height: 68pt; border-radius: 34pt; background: #10b981; opacity: 0.35; }
        .orb-b { top: 8pt; right: 46pt; width: 26pt; height: 26pt; border-radius: 13pt; background: #34d399; opacity: 0.25; }
        .accent { top: 36pt; left: 0; width: 85.6mm; height: 2.5pt; background: #a3e635; }

        .logo-box { top: 7pt; left: 10pt; width: 22pt; height: 22pt; background: #ffffff; border-radius: 6pt; }
        .logo-box img { width: 18pt; height: 18pt; margin: 2pt; }
        .brand { top: 8.5pt; left: 38pt; }
        .brand-1 { font-size: 9pt; font-weight: bold; color: #ffffff; letter-spacing: 1pt; line-height: 1; }
        .brand-2 { font-size: 4.6pt; font-weight: bold; color: #a7f3d0; letter-spacing: 2pt; margin-top: 2.5pt; }
        .tag { top: 12pt; right: 10pt; font-size: 4.8pt; font-weight: bold; color: #ffffff; letter-spacing: 1.2pt;
               border: 0.6pt solid #6ee7b7; border-radius: 7pt; padding: 2pt 6pt; }

        .photo { top: 46pt; left: 11pt; width: 62pt; height: 78pt; border-radius: 6pt; border: 1.5pt solid #d1fae5; }
        .photo-ph { top: 46pt; left: 11pt; width: 62pt; height: 78pt; border-radius: 6pt; background: #ecfdf5;
                    border: 1.5pt solid #d1fae5; text-align: center; }
        .photo-ph span { font-size: 20pt; font-weight: bold; color: #059669; line-height: 78pt; }

        .info { top: 44pt; left: 82pt; width: 152pt; height: 96pt; overflow: hidden; }
        .lbl { font-size: 4.3pt; font-weight: bold; color: #64748b; letter-spacing: 1.2pt; line-height: 1.1; }
        .name { font-weight: bold; color: #0f172a; white-space: nowrap; overflow: hidden; width: 152pt; line-height: 1.1; margin-top: 1pt; }
        .chip { display: inline-block; margin-top: 2.5pt; background: #0f172a; color: #ffffff; font-family: 'DejaVu Sans Mono', monospace;
                font-size: 6pt; font-weight: bold; letter-spacing: 0.3pt; border-radius: 3pt; padding: 1.6pt 5pt; line-height: 1.1; }

        .club { margin-top: 3.5pt; width: 142pt; background: #ecfdf5; border-left: 2pt solid #059669; border-radius: 2pt; padding: 2pt 4pt; }
        .club-name { font-weight: bold; color: #065f46; white-space: nowrap; overflow: hidden; margin-top: 0.6pt; line-height: 1.1; }

        table.grid { margin-top: 3pt; border-collapse: collapse; width: 152pt; }
        table.grid td { vertical-align: top; padding: 0 4pt 2pt 0; width: 50%; }
        .val { font-size: 5.6pt; font-weight: bold; color: #0f172a; margin-top: 0.5pt; white-space: nowrap; overflow: hidden; line-height: 1.1; }

        .foot { bottom: 0; left: 0; width: 85.6mm; height: 11pt; background: #ecfdf5; border-top: 0.6pt solid #a7f3d0;
                text-align: center; font-size: 4.8pt; font-weight: bold; color: #047857; letter-spacing: 1.6pt; line-height: 11pt; }

        /* ---------- BACK ---------- */
        .side { top: 0; left: 0; width: 92pt; height: 54mm; background: #065f46; }
        .orb-c { bottom: -30pt; left: -26pt; width: 84pt; height: 84pt; border-radius: 42pt; background: #10b981; opacity: 0.3; }
        .qr-box { top: 20pt; left: 14pt; width: 64pt; height: 64pt; background: #ffffff; border-radius: 7pt; }
        .qr-box img { width: 56pt; height: 56pt; margin: 4pt; }
        .qr-none { font-size: 4.5pt; color: #64748b; text-align: center; padding-top: 26pt; }
        .scan { top: 90pt; left: 0; width: 92pt; text-align: center; font-size: 4.6pt; font-weight: bold; color: #ffffff; letter-spacing: 1.5pt; }
        .side-id { top: 100pt; left: 0; width: 92pt; text-align: center; font-family: 'DejaVu Sans Mono', monospace; font-size: 5pt; color: #a7f3d0; }

        .back { top: 14pt; left: 104pt; width: 128pt; }
        .back-h { font-size: 7pt; font-weight: bold; color: #065f46; }
        .back-p { font-size: 4.8pt; color: #475569; line-height: 1.4; margin-top: 3pt; }
        table.meta { margin-top: 6pt; border-collapse: collapse; width: 128pt; }
        table.meta td { font-size: 5pt; padding: 1.4pt 0; border-bottom: 0.4pt solid #e2e8f0; vertical-align: top; }
        table.meta td.k { color: #64748b; width: 40pt; }
        table.meta td.v { color: #0f172a; font-weight: bold; }

        .sign { top: 112pt; left: 104pt; width: 100pt; border-top: 0.6pt solid #94a3b8; padding-top: 1.5pt;
                font-size: 4.4pt; color: #64748b; letter-spacing: 0.8pt; }
        .found { top: 127pt; left: 104pt; width: 128pt; font-size: 3.9pt; color: #94a3b8; line-height: 1.35; }
        .bar { bottom: 0; left: 0; width: 85.6mm; height: 2.5pt; background: #a3e635; }
    </style>
</head>
<body>

@if($sheet)
    <div class="sheet-title">4-H Member ID: {{ $fullName }}</div>
    <div class="sheet-note">
        Print at <b>Actual size / 100%</b> (turn off "Fit to page"). Each card is 85.6 × 54 mm, the same as an ATM card or national ID.
        Cut along the dashed lines, fold or glue back-to-back, then laminate.
    </div>
    <div class="side-label front-pos">FRONT</div>
    <div class="side-label back-pos">BACK</div>
    <div class="cut front-pos"></div>
    <div class="cut back-pos"></div>
@endif

{{-- ===================== FRONT ===================== --}}
<div class="card {{ $sheet ? 'on-sheet front-pos' : 'break' }}">
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

    <div class="abs info">
        <div class="lbl">MEMBER NAME</div>
        <div class="name" style="font-size: {{ $nameSize }}pt;">{{ $fullName }}</div>

        <div class="chip">{{ $member->member_id ?? 'UNASSIGNED' }}</div>

        <div class="club">
            <div class="lbl">ORGANIZATION / CLUB</div>
            <div class="club-name" style="font-size: {{ $clubSize }}pt;{{ $clubWrap ? ' white-space: normal; line-height: 1.2;' : '' }}">{{ $club ?: '—' }}</div>
        </div>

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
                <td colspan="2">
                    <div class="lbl">SPECIALIZATION</div>
                    <div class="val">{{ $member->specialization ?? '—' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="abs foot">HEAD • HEART • HANDS • HEALTH</div>
</div>

{{-- ===================== BACK ===================== --}}
<div class="card {{ $sheet ? 'on-sheet back-pos' : '' }}">
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
            <tr><td class="k">Club</td><td class="v">{{ $club ?: '—' }}</td></tr>
            <tr><td class="k">Region</td><td class="v">{{ $member->region->name ?? '—' }}</td></tr>
            <tr><td class="k">Issued</td><td class="v">{{ $issued }}</td></tr>
            <tr><td class="k">Contact</td><td class="v">{{ $member->contact_no ?: '—' }}</td></tr>
        </table>
    </div>

    <div class="abs sign">AUTHORIZED SIGNATURE</div>
    <div class="abs found">If found, please return to the nearest 4-H Club office or scan the QR code to contact the member's region.</div>
    <div class="abs bar"></div>
</div>

</body>
</html>