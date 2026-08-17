{{-- En-tête commun des rapports PDF — logo carré EBA, catégorie/titre, bandeau filtres --}}
@php
    $category    = $category ?? 'Rapport';
    $title       = $title ?? 'Titre du rapport';
    $filtersText = $filtersText ?? 'Aucun filtre';
    $generatedAt = $generatedAt ?? \Carbon\Carbon::now();
@endphp
<div style="border-bottom:3px solid #C4302B;">
    <div style="padding:22px 28px 0; display:flex; justify-content:space-between; align-items:flex-start;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:56px; height:56px; background:#C4302B; border-radius:10px; display:flex; align-items:center; justify-content:center; padding:4px;">
                <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/logo.jpg'))) }}" alt="EBA Logo" style="max-width:100%; max-height:100%; border-radius:6px;">
            </div>
            <div>
                <div style="font-size:9px; font-weight:700; color:#C4302B; text-transform:uppercase; letter-spacing:1.5px;">{{ $category }}</div>
                <div style="font-size:20px; font-weight:800; color:#1a1a1a; letter-spacing:-0.5px; margin-top:2px;">{{ $title }}</div>
            </div>
        </div>
        <div style="text-align:right; font-size:10px; color:#888; line-height:1.4;">
            <div style="font-weight:700; color:#333;">e-Business Afrique</div>
            <div>EbaTestManager</div>
            <div style="margin-top:2px;">{{ $generatedAt->format('d/m/Y à H:i') }}</div>
        </div>
    </div>
    <div style="margin-top:16px; background:#F1EFE8; padding:8px 28px; font-size:10px; color:#555;">
        <strong style="color:#1a1a1a;">Filtres appliqués :</strong> {{ $filtersText }}
    </div>
</div>
