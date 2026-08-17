<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.5; }
        .section { padding: 18px 30px; border-bottom: 1px solid #f0f0f0; }
        .section-title { font-size: 13px; font-weight: 700; color: #CC0000; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 7px 8px; border-bottom: 1px solid #ddd; }
        th { font-size: 9.5px; text-transform: uppercase; color: #888; letter-spacing: 0.5px; }
        td { font-size: 11px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bar-track { width: 100%; background: #f0f0f0; border-radius: 4px; height: 9px; overflow: hidden; }
        .bar-fill { height: 9px; border-radius: 4px; }
        .stacked { width: 100%; background: #f0f0f0; border-radius: 4px; height: 12px; overflow: hidden; white-space: nowrap; }
        .stacked span { display: inline-block; height: 12px; }
        .legend { font-size: 9px; margin-top: 8px; }
        .legend span { margin-right: 14px; }
        .swatch { display: inline-block; width: 9px; height: 9px; border-radius: 2px; margin-right: 3px; }
        .totals { background: #fafafa; font-weight: 700; }
        .footer { padding: 14px 30px; background: #f8f8f8; font-size: 10px; color: #888; }
        .mono { font-family: "Courier New", monospace; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body>

    @include('pdf.partials.header', [
        'category' => 'Statistiques par projet',
        'title' => "Cas de test & répartition des statuts",
        'filtersText' => 'Période : ' . ($filterPeriod === 'all' ? 'Toutes' : ($filterPeriod === 'this_month' ? 'Ce mois-ci' : ($filterPeriod === 'last_month' ? 'Le mois dernier' : 'Cette année'))) . ' | Projet : ' . ($filterProject === 'all' ? 'Tous' : $filterProject) . ' | Testeur : ' . ($filterTester === 'all' ? 'Tous' : $filterTester),
    ])

    <div class="section">
        <div class="section-title">1. Cas de test par projet</div>
        <table>
            <thead>
                <tr>
                    <th>Projet</th>
                    <th class="center">Total</th>
                    <th class="center">Exécutés</th>
                    <th class="center">Non exécutés</th>
                    <th style="width: 28%;">Taux d'exécution</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perProjectStats as $row)
                <tr>
                    <td><strong>{{ $row['name'] }}</strong></td>
                    <td class="center mono">{{ $row['total'] }}</td>
                    <td class="center mono">{{ $row['executed'] }}</td>
                    <td class="center mono">{{ $row['non_executed'] }}</td>
                    <td>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ $row['taux_execution'] }}%; background: #CC0000;"></div>
                        </div>
                        <div class="right" style="font-size:9px; color:#666; margin-top:2px;">{{ $row['taux_execution'] }}%</div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals">
                    <td>Total</td>
                    <td class="center mono">{{ $totals['total'] }}</td>
                    <td class="center mono">{{ $totals['executed'] }}</td>
                    <td class="center mono">{{ $totals['non_executed'] }}</td>
                    <td class="right mono">{{ $totals['total'] > 0 ? round($totals['executed'] / $totals['total'] * 100, 1) : 0 }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">2. Répartition des statuts par projet</div>
        <table>
            <thead>
                <tr>
                    <th>Projet</th>
                    <th class="center">Validé</th>
                    <th class="center">Non validé</th>
                    <th class="center">Sous réserve</th>
                    <th class="center">Optim.</th>
                    <th class="center">Non exéc.</th>
                    <th style="width: 30%;">Répartition</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perProjectStats as $row)
                @php
                    $denom = $row['total'] > 0 ? $row['total'] : 1;
                    $w = fn ($n) => round($n / $denom * 100, 1);
                @endphp
                <tr>
                    <td><strong>{{ $row['name'] }}</strong></td>
                    <td class="center mono">{{ $row['valide'] }}</td>
                    <td class="center mono">{{ $row['non_valide'] }}</td>
                    <td class="center mono">{{ $row['sous_reserve'] }}</td>
                    <td class="center mono">{{ $row['optimisation'] }}</td>
                    <td class="center mono">{{ $row['non_executed'] }}</td>
                    <td>
                        <div class="stacked">
                            <span style="width: {{ $w($row['valide']) }}%; background:#16a34a;"></span>
                            <span style="width: {{ $w($row['non_valide']) }}%; background:#CC0000;"></span>
                            <span style="width: {{ $w($row['sous_reserve']) }}%; background:#a855f7;"></span>
                            <span style="width: {{ $w($row['optimisation']) }}%; background:#f59e0b;"></span>
                            <span style="width: {{ $w($row['non_executed']) }}%; background:#e5e7eb;"></span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="totals">
                    <td>Total</td>
                    <td class="center mono">{{ $totals['valide'] }}</td>
                    <td class="center mono">{{ $totals['non_valide'] }}</td>
                    <td class="center mono">{{ $totals['sous_reserve'] }}</td>
                    <td class="center mono">{{ $totals['optimisation'] }}</td>
                    <td class="center mono">{{ $totals['non_executed'] }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <div class="legend">
            <span><span class="swatch" style="background:#16a34a;"></span>Validé</span>
            <span><span class="swatch" style="background:#CC0000;"></span>Non validé</span>
            <span><span class="swatch" style="background:#a855f7;"></span>Sous réserve</span>
            <span><span class="swatch" style="background:#f59e0b;"></span>Optimisation</span>
            <span><span class="swatch" style="background:#e5e7eb;"></span>Non exécuté</span>
        </div>
    </div>

    <div class="footer">
        <span>Généré le {{ now()->format('d/m/Y à H:i') }} — EbaTestManager | {{ $perProjectStats->count() }} projet(s) analysé(s)</span>
    </div>

</body>
</html>
