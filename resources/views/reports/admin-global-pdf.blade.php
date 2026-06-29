<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }
        .header {
            background-color: #CC0000;
            color: #ffffff;
            padding: 5px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .header h1 { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { font-size: 11px; opacity: 0.85; margin-top: 2px; }
        .badge { display: inline-block; background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .section { padding: 20px 30px; border-bottom: 1px solid #f0f0f0; }
        .section-title { font-size: 13px; font-weight: 700; color: #CC0000; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 12px; }
        .stat-box { border: 2px solid; border-radius: 8px; padding: 12px; text-align: center; }
        .stat-box.success { border-color: #16a34a; background: #f0fdf4; }
        .stat-box.failure { border-color: #CC0000; background: #fff5f5; }
        .stat-box.reserve { border-color: #f59e0b; background: #fffbeb; }
        .stat-box.optim   { border-color: #3b82f6; background: #eff6ff; }
        .stat-number { font-size: 24px; font-weight: 800; }
        .stat-label  { font-size: 10px; font-weight: 600; text-transform: uppercase; margin-top: 2px; }
        .stat-box.success .stat-number { color: #16a34a; }
        .stat-box.failure .stat-number { color: #CC0000; }
        .stat-box.reserve .stat-number { color: #f59e0b; }
        .stat-box.optim   .stat-number { color: #3b82f6; }
        .footer { padding: 16px 30px; background: #f8f8f8; font-size: 10px; color: #888; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
        td { font-size: 11px; }
        .filters { font-size: 10px; color: #666; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" alt="EBA Logo" style="height: 24px; margin-bottom: 8px; border-radius: 4px;">
                <br>
                <span class="badge">RAPPORT GLOBAL ADMINISTRATEUR</span>
                <h1 style="margin-top:4px;">STATISTIQUES & CAPACITÉ D'ÉQUIPE</h1>
            </div>
            <div style="text-align:right; font-size:11px; opacity:0.85;">
                <div>e-Business Afrique</div>
                <div>EbaTestManager</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="filters">
            <strong>Filtres appliqués :</strong>
            Période : {{ $filterPeriod === 'all' ? 'Toutes' : ($filterPeriod === 'this_month' ? 'Ce mois-ci' : ($filterPeriod === 'last_month' ? 'Le mois dernier' : 'Cette année')) }} |
            Projet ID : {{ $filterProject === 'all' ? 'Tous' : $filterProject }} |
            Testeur ID : {{ $filterTester === 'all' ? 'Tous' : $filterTester }}
        </div>
        
        <div class="section-title">Indicateurs Clés</div>
        <div class="stats-grid">
            <div class="stat-box success">
                <div class="stat-number">{{ $activeProjectsCount }}</div>
                <div class="stat-label">Projets Actifs</div>
            </div>
            <div class="stat-box reserve">
                <div class="stat-number">{{ $uatProjectsCount }}</div>
                <div class="stat-label">En Phase Test (UAT)</div>
            </div>
            <div class="stat-box optim">
                <div class="stat-number">{{ $totalTemplates }}</div>
                <div class="stat-label">Tests Assignés</div>
            </div>
            <div class="stat-box success">
                <div class="stat-number">{{ $validationRate }}%</div>
                <div class="stat-label">Taux de Validation</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Capacité de l'Équipe (Testeurs)</div>
        <table>
            <thead>
                <tr>
                    <th>Nom du Testeur</th>
                    <th style="text-align: center;">Tests Assignés</th>
                    <th style="text-align: center;">Tests Exécutés</th>
                    <th>Progression</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($testersCapacity as $tester)
                <tr>
                    <td><strong>{{ $tester['name'] }}</strong></td>
                    <td style="text-align: center;">{{ $tester['total'] }}</td>
                    <td style="text-align: center;">{{ $tester['done'] }}</td>
                    <td>
                        <div style="width:100%; background:#f0f0f0; border-radius:4px; height:8px; margin-top:4px;">
                            @php
                                $color = $tester['color'] === 'green' ? '#16a34a' : ($tester['color'] === 'yellow' ? '#f59e0b' : '#CC0000');
                            @endphp
                            <div style="width: {{ $tester['percent'] }}%; background:{{ $color }}; height:8px; border-radius:4px;"></div>
                        </div>
                        <div style="font-size:9px; color:#666; margin-top:2px; text-align:right;">{{ $tester['percent'] }}%</div>
                    </td>
                    <td>
                        <span style="font-size: 10px; font-weight: bold; color: {{ $color }};">{{ strtoupper($tester['status']) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <span>Généré le {{ now()->format('d/m/Y à H:i') }} — EbaTestManager</span>
        <span>Rapport Administrateur</span>
    </div>

</body>
</html>
