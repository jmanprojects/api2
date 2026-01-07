<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\PrescriptionPdfService;
use Illuminate\Http\Request;

class PrescriptionPdfController extends Controller
{
    protected PrescriptionPdfService $pdfService;

    public function __construct(PrescriptionPdfService $pdfService)
    {
        $this->pdfService = $pdfService;

        // auth:sanctum should already wrap routes, and role middleware will protect it.
    }

    /**
     * Stream PDF in the browser (best for "Print" button in the frontend).
     *
     * Route: GET /api/prescriptions/{prescription}/pdf
     */
    public function stream(Request $request, Prescription $prescription)
    {
        $pdf = $this->pdfService->makePdf($prescription);

        // Stream shows the PDF in browser.
        return $pdf->stream($this->pdfService->filename($prescription));
    }

    /**
     * Force download of the PDF.
     *
     * Route: GET /api/prescriptions/{prescription}/pdf/download
     */
    public function download(Request $request, Prescription $prescription)
    {
        $pdf = $this->pdfService->makePdf($prescription);

        return $pdf->download($this->pdfService->filename($prescription));
    }
}
