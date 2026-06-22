<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Executive Report - {{ $report->perimeter }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #8b0000;
            font-size: 24px;
            border-bottom: 2px solid #8b0000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            color: #444;
            font-size: 18px;
            margin-top: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-table, .stats-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td, .stats-table th, .stats-table td {
            text-align: left;
            padding: 8px 12px;
            border: 1px solid #eee;
        }
        .info-table th {
            width: 30%;
            background-color: #f9f9f9;
            color: #555;
        }
        .stats-table th {
            background-color: #f5f5f5;
        }
        .stats-table td {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }
        .success { color: #15803d; }
        .failure { color: #b91c1c; }
        .reserve { color: #b45309; }
        .opti { color: #1d4ed8; }
        .notes-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <h1>EXECUTIVE REPORT</h1>

    <h2>Informations générales</h2>
    <table class="info-table">
        <tr>
            <th>Nom du projet</th>
            <td>{{ $report->project->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Périmètre du test</th>
            <td>{{ $report->perimeter }}</td>
        </tr>
        <tr>
            <th>Version testée</th>
            <td>{{ $report->tested_version ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Responsable du test</th>
            <td>{{ $report->responsible }}</td>
        </tr>
        <tr>
            <th>Date du test</th>
            <td>{{ $report->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Nombre total de cas</th>
            <td>{{ $report->stats['total'] ?? 0 }} cas de test</td>
        </tr>
    </table>

    <h2>Statistiques globales</h2>
    <table class="stats-table">
        <thead>
            <tr>
                <th class="success">✅ Succès</th>
                <th class="failure">💣 Échecs</th>
                <th class="reserve">🤔 Sous réserve</th>
                <th class="opti">👷‍♂️ Optimisation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="success">{{ $report->stats['valide'] ?? 0 }}</td>
                <td class="failure">{{ $report->stats['non_valide'] ?? 0 }}</td>
                <td class="reserve">{{ $report->stats['sous_reserve'] ?? 0 }}</td>
                <td class="opti">{{ $report->stats['optimisation'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <h2>NB / Conclusion & Remarques</h2>
    <div class="notes-box">
        {!! nl2br(e($report->notes)) !!}
    </div>

    @if($report->responses && $report->responses->count() > 0)
    <h2>Réponses des développeurs</h2>
    @foreach($report->responses as $response)
        <div style="margin-bottom:15px; padding:10px; border-left:3px solid #1d4ed8; background:#eff6ff;">
            <strong>{{ $response->user->name ?? 'Développeur' }}</strong> ({{ $response->created_at->format('d/m/Y H:i') }})<br>
            <div style="margin-top:5px;">
                {!! nl2br(e($response->content)) !!}
            </div>
        </div>
    @endforeach
    @endif

    <div class="footer">
        Généré par EbaTestManager le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>
