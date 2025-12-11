<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Prescription;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PrescriptionService
{
    /**
     * Create a new prescription for a given consultation,
     * with its items (medications).
     *
     * @param Consultation $consultation The consultation linked to this prescription.
     * @param array        $data         Validated data, including optional items.
     *
     * Expected $data structure:
     *  - notes: string|null
     *  - items: [
     *      [
     *        'medicine_name' => 'Ibuprofen 400mg',
     *        'dose'          => '1 tablet',
     *        'frequency'     => 'every 8 hours',
     *        'duration'      => '5 days',
     *        'route'         => 'oral',
     *        'instructions'  => 'Take after meals'
     *      ],
     *      ...
     *    ]
     */
    public function createPrescription(Consultation $consultation, array $data): Prescription
    {
        $doctor  = $consultation->doctor;
        $patient = $consultation->patient;

        return DB::transaction(function () use ($consultation, $doctor, $patient, $data) {
            // 1) Create prescription header
            $prescription = Prescription::create([
                'consultation_id' => $consultation->id,
                'doctor_id'       => $doctor->id,
                'patient_id'      => $patient->id,
                'issued_at'       => now(),
                'notes'           => $data['notes'] ?? null,
            ]);

            // 2) Create items if provided
            $items = $data['items'] ?? [];

            foreach ($items as $itemData) {
                $prescription->items()->create(Arr::only($itemData, [
                    'medicine_name',
                    'dose',
                    'frequency',
                    'duration',
                    'route',
                    'instructions',
                ]));
            }

            return $prescription->load('items');
        });
    }

    /**
     * Update an existing prescription.
     * Strategy:
     *  - update notes
     *  - replace all items with the provided list (simpler for UI forms).
     */
    public function updatePrescription(Prescription $prescription, array $data): Prescription
    {
        return DB::transaction(function () use ($prescription, $data) {
            $prescription->notes = $data['notes'] ?? $prescription->notes;
            $prescription->save();

            // Replace all items
            $prescription->items()->delete();

            $items = $data['items'] ?? [];

            foreach ($items as $itemData) {
                $prescription->items()->create(Arr::only($itemData, [
                    'medicine_name',
                    'dose',
                    'frequency',
                    'duration',
                    'route',
                    'instructions',
                ]));
            }

            return $prescription->load('items');
        });
    }
}
