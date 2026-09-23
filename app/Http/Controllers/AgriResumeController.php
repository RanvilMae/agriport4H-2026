<?php
namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class AgriResumeController extends Controller
{
    /**
     * Download the member's Agri-Resume in PDF format.
     */
    public function downloadPdf(Request $request)
    {
        $member = auth()->user(); // Or fetch the specific member model

        // 1. Load the blade view dedicated to the print/PDF format
        $pdf = Pdf::loadView('pdf.agri-resume', compact('member'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true, // Enables fetching remote image assets/logos
                'defaultFont' => 'sans-serif'
            ]);

        // 2. Generate a clean, standardized filename
        $filename = 'Agri-Resume-' . Str::slug($member->first_name . '-' . $member->last_name) . '.pdf';

        // 3. Stream or download the generated PDF directly
        return $pdf->download($filename);
    }
}
