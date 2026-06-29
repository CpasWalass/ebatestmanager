<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }
        .header { background: #CC0000; color: white; padding: 24px 30px; }
        .header h1 { font-size: 20px; font-weight: 800; letter-spacing: -0.5px; }
        .header p { font-size: 11px; opacity: 0.85; margin-top: 2px; }
        .badge { display: inline-block; background: rgba(255,255,255,0.2); padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
        .section { padding: 20px 30px; border-bottom: 1px solid #f0f0f0; }
        .section-title { font-size: 13px; font-weight: 700; color: #CC0000; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .info-item { }
        .info-label { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 12px; font-weight: 600; color: #1a1a1a; margin-top: 2px; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 12px; }
        .stat-box { border: 2px solid; border-radius: 8px; padding: 12px; text-align: center; }
        .stat-box.success { border-color: #16a34a; background: #f0fdf4; }
        .stat-box.failure { border-color: #CC0000; background: #fff5f5; }
        .stat-box.reserve { border-color: #f59e0b; background: #fffbeb; }
        .stat-box.optim   { border-color: #3b82f6; background: #eff6ff; }
        .stat-number { font-size: 28px; font-weight: 800; }
        .stat-label  { font-size: 10px; font-weight: 600; text-transform: uppercase; margin-top: 2px; }
        .stat-box.success .stat-number { color: #16a34a; }
        .stat-box.failure .stat-number { color: #CC0000; }
        .stat-box.reserve .stat-number { color: #f59e0b; }
        .stat-box.optim   .stat-number { color: #3b82f6; }
        .total-box { background: #1a1a1a; color: white; border-radius: 8px; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .footer { padding: 16px 30px; background: #f8f8f8; font-size: 10px; color: #888; display: flex; justify-content: space-between; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
        td { font-size: 11px; }
        .status-badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .bg-green { background: #16a34a; color: white; }
        .bg-red { background: #CC0000; color: white; }
        .bg-yellow { background: #f59e0b; color: white; }
        .bg-blue { background: #3b82f6; color: white; }
    </style>
</head>
<body>

    <div class="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <span class="badge">RAPPORT GLOBAL</span>
                <h1 style="margin-top:8px;">STATISTIQUES TESTEUR</h1>
                <p>{{ $user->name }} - {{ $user->email }}</p>
            </div>
            <div style="text-align:right; font-size:11px; opacity:0.85;">
                <div>e-Business Afrique</div>
                <div>EbaTestManager</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Informations Générales</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Testeur</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Date du Rapport</div>
                <div class="info-value">{{ now()->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Cas de tests assignés</div>
                <div class="info-value">{{ $totalAssigned }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Taux de validation globale</div>
                <div class="info-value">{{ $successRate }}%</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Statistiques d'Exécution</div>
        
        <div class="total-box">
            <span style="font-size:13px; font-weight:600;">Cas de tests exécutés</span>
            <span style="font-size:24px; font-weight:800;">{{ $totalExecuted }} cas</span>
        </div>

        <div class="stats-grid">
            <div class="stat-box success">
                <div class="stat-number">{{ $successCount }}</div>
                <div class="stat-label">Validés</div>
            </div>
            <div class="stat-box failure">
                <div class="stat-number">{{ $failureCount }}</div>
                <div class="stat-label">Échecs</div>
            </div>
            <div class="stat-box reserve">
                <div class="stat-number">{{ $reserveCount }}</div>
                <div class="stat-label">Sous réserve</div>
            </div>
            <div class="stat-box optim">
                <div class="stat-number">{{ $optimCount }}</div>
                <div class="stat-label">Optimisation</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Derniers Tests Exécutés (Résumé)</div>
        @if($recentTests->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Projet</th>
                    <th>Date d'exécution</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentTests as $test)
                <tr>
                    <td>{{ $test->testCase->project->name ?? 'Projet Inconnu' }}</td>
                    <td>{{ $test->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @php
                            $badgeClass = match($test->status) {
                                'valide' => 'bg-green',
                                'non_valide' => 'bg-red',
                                'sous_reserve' => 'bg-yellow',
                                'optimisation' => 'bg-blue',
                                default => 'bg-gray-500',
                            };
                            $label = str_replace('_', ' ', $test->status);
                        @endphp
                        <span class="status-badge {{ $badgeClass }}">{{ $label }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="font-size: 11px; color: #888;">Aucun test exécuté récemment.</p>
        @endif
    </div>

    <div class="footer">
        <span>Généré le {{ now()->format('d/m/Y à H:i') }} — EbaTestManager by e-Business Afrique</span>
        <span>Rapport Global - {{ $user->name }}</span>
    </div>

</body>
</html>
