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
        .notes-box { background: #fafafa; border-left: 4px solid #CC0000; padding: 12px 16px; border-radius: 0 8px 8px 0; margin-top: 12px; }
        .footer { padding: 16px 30px; background: #f8f8f8; font-size: 10px; color: #888; display: flex; justify-content: space-between; }
        .link { color: #CC0000; text-decoration: none; }
    </style>
</head>
<body>

    {{-- En-tête rouge --}}
    <div class="header">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <span class="badge">{{ strtoupper($report->type) }}</span>
                <h1 style="margin-top:8px;">EXECUTIVE REPORT</h1>
                <p>{{ $report->project?->name }}</p>
            </div>
            <div style="text-align:right; font-size:11px; opacity:0.85;">
                <div>e-Business Afrique</div>
                <div>UAT/IAT Manager</div>
            </div>
        </div>
    </div>

    {{-- Informations générales --}}
    <div class="section">
        <div class="section-title">Informations générales</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Nom du projet</div>
                <div class="info-value">{{ $report->project?->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Client</div>
                <div class="info-value">{{ $report->project?->client?->name ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Version testée</div>
                <div class="info-value">{{ $report->tested_version ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Date du test</div>
                <div class="info-value">{{ $report->test_date?->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Responsable</div>
                <div class="info-value">{{ $report->responsible ?? $report->creator?->name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Périmètre</div>
                <div class="info-value">{{ $report->perimeter ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- Statistiques globales --}}
    <div class="section">
        <div class="section-title">Statistiques globales</div>
        @php $stats = $report->stats ?? []; @endphp

        <div class="total-box">
            <span style="font-size:13px; font-weight:600;">Nombre total des cas de test</span>
            <span style="font-size:24px; font-weight:800;">{{ $stats['total'] ?? 0 }} cas</span>
        </div>

        <div class="stats-grid">
            <div class="stat-box success">
                <div class="stat-number">{{ $stats['success'] ?? 0 }}</div>
                <div class="stat-label" style="color:#16a34a;">✅ Succès</div>
            </div>
            <div class="stat-box failure">
                <div class="stat-number">{{ $stats['failure'] ?? 0 }}</div>
                <div class="stat-label" style="color:#CC0000;">💣 Échecs</div>
            </div>
            <div class="stat-box reserve">
                <div class="stat-number">{{ $stats['reserve'] ?? 0 }}</div>
                <div class="stat-label" style="color:#f59e0b;">🤔 Sous réserve</div>
            </div>
            <div class="stat-box optim">
                <div class="stat-number">{{ $stats['optim'] ?? 0 }}</div>
                <div class="stat-label" style="color:#3b82f6;">👷 Optimisation</div>
            </div>
        </div>

        @if($report->notes)
        <div class="notes-box" style="margin-top:16px;">
            <strong style="font-size:11px; color:#CC0000;">NB :</strong>
            <span style="font-size:11px; color:#444; margin-left:4px;">{{ $report->notes }}</span>
        </div>
        @endif
    </div>

    {{-- Pied de page --}}
    <div class="footer">
        <span>Généré le {{ now()->format('d/m/Y à H:i') }} — EbaTestManager by e-Business Afrique</span>
        <span>{{ $report->title }}</span>
    </div>

</body>
</html>
