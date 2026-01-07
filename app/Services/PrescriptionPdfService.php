<?php

namespace App\Services;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;

class PrescriptionPdfService
{
    /**
     * Build a PDF for a prescription.
     *
     * Why a dedicated service?
     * - Keeps controller lean (SRP).
     * - Reusable for download/stream/email later.
     * - Easy to swap DomPDF for Snappy/WKHTML if needed.
     */
    public function makePdf(Prescription $prescription)
    {
        /**
         * Load required relations in one shot to avoid N+1 and
         * to ensure the PDF always has the data it needs.
         */
        $prescription->load([
            'items',
            'doctor.user',
            'patient.user',
            'consultation',
        ]);

        $doctorUser = optional($prescription->doctor)->user;
        $patientUser = optional($prescription->patient)->user;

        // You can map this to your real fields (license/cedula/etc.) as you add them.
        $doctorLicense = $prescription->doctor->license_number ?? null;

        $data = [
            'issuedAt'       => optional($prescription->issued_at)->format('Y-m-d H:i') ?? '',
            'doctorName'     => $doctorUser?->name ?? 'Doctor',
            'doctorLicense'  => $doctorLicense,
            'patientName'    => $patientUser?->name ?? 'Patient',
            'patientBirthdate' => optional($prescription->patient->birthdate ?? null)?->format('Y-m-d'),
            'items'          => $prescription->items->map(function ($item) {
                return [
                    'medicine_name' => $item->medicine_name,
                    'dose'          => $item->dose,
                    'frequency'     => $item->frequency,
                    'duration'      => $item->duration,
                    'route'         => $item->route,
                    'instructions'  => $item->instructions,
                ];
            })->toArray(),
            'notes'          => $prescription->notes,
            'consultationId' => $prescription->consultation_id,
            'prescriptionId' => $prescription->id,
        ];

        /**
         * Create the PDF using a Blade view.
         * Keep HTML simple for best DomPDF compatibility.
         */
        return Pdf::loadView('pdfs.prescription', $data)
            ->setPaper('letter'); // or 'a4' depending on your target
    }

    /**
     * Generate a safe filename for downloads.
     */
    public function filename(Prescription $prescription): string
    {
        $date = optional($prescription->issued_at)->format('Ymd_His') ?? now()->format('Ymd_His');
        return "prescription_{$prescription->id}_{$date}.pdf";
    }
}
