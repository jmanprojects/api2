<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
        /* Keep styles simple: DomPDF supports a limited CSS subset */
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { margin-bottom: 12px; }
        .title { font-size: 18px; font-weight: bold; margin-bottom: 6px; }
        .meta { margin-bottom: 10px; }
        .meta div { margin-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; vertical-align: top; }
        th { background: #eee; }
        .notes { margin-top: 12px; }
        .footer { margin-top: 18px; font-size: 11px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="title">Medical Prescription</div>
        <div class="meta">
            <div><strong>Date:</strong> {{ $issuedAt }}</div>
            <div><strong>Doctor:</strong> {{ $doctorName }}</div>
            @if($doctorLicense)
                <div><strong>License:</strong> {{ $doctorLicense }}</div>
            @endif
            <div><strong>Patient:</strong> {{ $patientName }}</div>
            @if($patientBirthdate)
                <div><strong>Birthdate:</strong> {{ $patientBirthdate }}</div>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Medicine</th>
                <th style="width: 15%;">Dose</th>
                <th style="width: 20%;">Frequency</th>
                <th style="width: 15%;">Duration</th>
                <th style="width: 20%;">Instructions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['medicine_name'] }}</td>
                    <td>{{ $item['dose'] ?? '-' }}</td>
                    <td>{{ $item['frequency'] ?? '-' }}</td>
                    <td>{{ $item['duration'] ?? '-' }}</td>
                    <td>
                        @if(!empty($item['route']))
                            <div><strong>Route:</strong> {{ $item['route'] }}</div>
                        @endif
                        {{ $item['instructions'] ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($notes))
        <div class="notes">
            <strong>Notes:</strong>
            <div>{{ $notes }}</div>
        </div>
    @endif

    <div class="footer">
        <div><strong>Consultation ID:</strong> {{ $consultationId }}</div>
        <div><strong>Prescription ID:</strong> {{ $prescriptionId }}</div>
    </div>

</body>
</html>
