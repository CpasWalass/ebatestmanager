<div>
    <style>
        :root {
            --ink: #14171f; --body: #52596b; --muted: #94a3b8;
            --surface: #ffffff; --surface-alt: #f5f6f8; --border: #e6e8ec;
            --brand: #8b0000; --brand-soft: #fbeceb;
            --success: #157f3c; --success-soft: #eaf7ee;
            --warning: #b45309; --warning-soft: #fdf3e2;
            --danger: #b91c1c; --danger-soft: #fdeceb;
            --track: #e3e6eb; --grid: #eef0f3;
        }
        html.dark {
            --ink: #f3f4f6; --body: #a2a8b6; --muted: #6b7280;
            --surface: #191d25; --surface-alt: #111318; --border: #2a2f3a;
            --brand: #c0433f; --brand-soft: rgba(139,0,0,0.18);
            --success: #3fbd72; --success-soft: rgba(21,127,60,0.16);
            --warning: #f2a93c; --warning-soft: rgba(180,83,9,0.16);
            --danger: #f0685f; --danger-soft: rgba(185,28,28,0.16);
            --track: #2c313c; --grid: #242a34;
        }

        .mono { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }
        .bento-card { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; }
        .bento-eyebrow { font-size: 10.5px; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--muted); }
        .bento-section-title { font-size: 14px; font-weight: 700; color: var(--ink); }
        .bento-section-sub { font-size: 12px; color: var(--muted); }
        .bar-track { height: 6px; border-radius: 4px; background: var(--track); overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 4px; transition: width .5s ease; }
        table.bento-data-table { width: 100%; font-size: 13px; border-collapse: collapse; }
        table.bento-data-table th { text-align: left; font-size: 10.5px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); padding: 0 20px 10px; border-bottom: 1px solid var(--border); }
        table.bento-data-table td { padding: 13px 20px; border-bottom: 1px solid var(--border); color: var(--body); vertical-align: middle; }
        table.bento-data-table tr:last-child td { border-bottom: none; }
        .bento-status-pill { font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: 5px; display: inline-block; }

        /* ===== BENTO GRID ===== */
        .bento {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(6, 1fr);
            grid-template-areas:
                "hero  hero  cA cB cB cC"
                "hero  hero  cD cD cE cE"
                "histA histA histA histB histB histB"
                "table table table team team team";
        }
        @media (max-width: 1024px) {
            .bento { grid-template-columns: repeat(2, 1fr);
                grid-template-areas:
                    "hero hero" "cA cB" "cC cD" "cE cE"
                    "histA histA" "histB histB" "table table" "team team"; }
        }
        @media (max-width: 640px) {
            .bento { grid-template-columns: 1fr;
                grid-template-areas: "hero" "cA" "cB" "cC" "cD" "cE" "histA" "histB" "table" "team"; }
        }
        .area-hero { grid-area: hero; }
        .area-cA { grid-area: cA; } .area-cB { grid-area: cB; } .area-cC { grid-area: cC; }
        .area-cD { grid-area: cD; } .area-cE { grid-area: cE; }
        .area-histA { grid-area: histA; } .area-histB { grid-area: histB; }
        .area-table { grid-area: table; } .area-team { grid-area: team; }

        .hero-ring-wrap { position: relative; width: 172px; height: 172px; margin: 0 auto; }
        .stat-mini { display: flex; flex-direction: column; justify-content: center; gap: 4px; height: 100%; }
        .stat-mini .stat-value { font-size: 26px; font-weight: 700; line-height: 1; color: var(--ink); }
        .stat-mini .stat-caption { font-size: 11px; color: var(--muted); }
    </style>

    <div class="space-y-6">

        {{-- ── Page Header & Filters ── --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <p class="bento-eyebrow mb-1">Vue d'ensemble</p>
                <h1 class="text-2xl font-bold" style="color:var(--ink)">Tableau de bord</h1>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="filterPeriod"
                    class="px-4 py-2 rounded-xl bg-[var(--surface)] border border-[var(--border)] text-sm text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                    <option value="all">Toutes les périodes</option>
                    <option value="this_month">Ce mois-ci</option>
                    <option value="last_month">Le mois dernier</option>
                    <option value="this_year">Cette année</option>
                </select>

                <select wire:model.live="filterProject"
                    class="px-4 py-2 rounded-xl bg-[var(--surface)] border border-[var(--border)] text-sm text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                    <option value="all">Tous les projets</option>
                    @foreach($allProjects as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterTester"
                    class="px-4 py-2 rounded-xl bg-[var(--surface)] border border-[var(--border)] text-sm text-[var(--ink)] focus:outline-none focus:ring-2 focus:ring-[#8b0000]">
                    <option value="all">Tous les testeurs</option>
                    @foreach($allTesters as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <div wire:loading class="text-sm text-gray-500">
                    <svg class="animate-spin h-5 w-5 inline text-red-800" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bento">

            <!-- ===== PIÈCE MAÎTRESSE : anneau complet, Cas de test exécutés ===== -->
            <div class="bento-card area-hero p-6 flex flex-col items-center justify-center text-center" style="cursor:pointer;" title="Voir la répartition des cas de test par projet"
                @click="$dispatch('open-stats', { type: 'cases' })">
                <p class="bento-eyebrow mb-4">Cas de test</p>
                <div class="hero-ring-wrap">
                    <canvas id="heroRing"></canvas>
                    <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <span class="mono" style="font-size:34px; font-weight:700; color:var(--ink); line-height:1;">
                            {{ $totalTemplates > 0 ? round(($adminStats['executed'] / $totalTemplates) * 100) : 0 }}%
                        </span>
                        <span class="bento-section-sub mt-1">exécutés</span>
                    </div>
                </div>
                <p class="mono mt-5" style="font-size:13px; color:var(--body);">{{ $adminStats['executed'] }} / {{ $totalTemplates }} cas de test</p>
                <p class="bento-section-sub mt-1" style="font-size:11px; color:var(--brand);">Cliquer pour voir par projet →</p>
            </div>

            <!-- ===== Compteurs bruts, tailles inégales ===== -->
            <div class="bento-card area-cA p-4 flex items-center" style="border-left:3px solid #8b8f99;">
                <div class="stat-mini">
                    <p class="bento-eyebrow">Projets actifs</p>
                    <p class="stat-value mono">{{ $activeProjectsCount }}</p>
                </div>
            </div>

            <div class="bento-card area-cB p-4 flex items-center" style="border-left:3px solid var(--success);">
                <div class="stat-mini">
                    <p class="bento-eyebrow">Clôturés</p>
                    <p class="stat-value mono">{{ $closedProjectsCount }}</p>
                    <p class="stat-caption">Projets terminés</p>
                </div>
            </div>

            <div class="bento-card area-cC p-4 flex items-center" style="border-left:3px solid var(--warning);">
                <div class="stat-mini">
                    <p class="bento-eyebrow">Phase UAT</p>
                    <p class="stat-value mono">{{ $uatProjectsCount }}</p>
                </div>
            </div>

            <!-- ===== Validation IAT (barre) ===== -->
            <div class="bento-card area-cD p-4">
                <div class="flex items-baseline justify-between mb-2">
                    <p class="bento-eyebrow">Validation IAT</p>
                    <span class="mono" style="font-size:17px; font-weight:700; color:var(--ink);">{{ $validationRate }}%</span>
                </div>
                <div class="bar-track"><div class="bar-fill" style="width:{{ $validationRate }}%; background:var(--brand);"></div></div>
                <p class="bento-section-sub mt-2">{{ $adminStats['valide'] }}/{{ $adminStats['executed'] }} validés</p>
                <p class="bento-section-sub mt-1" style="font-size:11px;">
                    <span title="Verdicts posés par les testeurs">{{ $authorBreakdown['by_tester']['total'] }} par testeurs</span>
                    @if($authorBreakdown['by_chef']['total'] > 0)
                    · <span style="color:var(--warning);" title="Verdicts modifiés par le chef de projet">{{ $authorBreakdown['by_chef']['total'] }} par chef de projet</span>
                    @endif
                </p>
            </div>

            <!-- ===== Approbation UAT (barre) ===== -->
            <div class="bento-card area-cE p-4">
                <div class="flex items-baseline justify-between mb-2">
                    <p class="bento-eyebrow">Approbation UAT</p>
                    <span class="mono" style="font-size:17px; font-weight:700; color:var(--ink);">{{ $clientValidationRate }}%</span>
                </div>
                <div class="bar-track"><div class="bar-fill" style="width:{{ $clientValidationRate }}%; background:#7e22ce;"></div></div>
                <p class="bento-section-sub mt-2">{{ $clientStats['validated'] }}/{{ $clientStats['total'] }} approuvés</p>
            </div>

            <!-- ===== Histogrammes, largeurs inégales ===== -->
            <div class="bento-card area-histA p-5" style="cursor:pointer;" title="Voir la répartition des statuts par projet"
                @click="$dispatch('open-stats', { type: 'status' })">
                <p class="bento-section-title mb-1">Répartition des statuts IAT</p>
                <p class="bento-section-sub mb-3">{{ $adminStats['total'] }} cas de test au total · <span style="color:var(--brand);">Cliquer pour voir par projet →</span></p>
                <div style="height:150px;"><canvas id="iatBarChart"></canvas></div>
            </div>

            <div class="bento-card area-histB p-5">
                <p class="bento-section-title mb-1">Approbation client (UAT)</p>
                <p class="bento-section-sub mb-3">{{ $clientStats['total'] }} scénario(s) évalué(s)</p>
                <div style="height:150px;"><canvas id="uatBarChart"></canvas></div>
            </div>

            <!-- ===== Table ===== -->
            <div class="bento-card area-table overflow-hidden">
                <div class="flex items-center justify-between px-6 py-5 border-b border-[var(--border)]">
                    <p class="bento-section-title">Projets filtrés</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="bento-data-table">
                        <thead>
                            <tr>
                                <th>Nom du projet</th>
                                <th>Client</th>
                                <th>Phase</th>
                                <th class="text-right pr-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mainProjects as $project)
                            <tr>
                                <td>
                                    <span style="color:var(--ink);font-weight:600;">{{ $project->name }}</span><br>
                                    <span class="bento-section-sub">Créé le {{ $project->created_at->format('d/m/Y') }}</span>
                                </td>
                                <td>{{ $project->client->name ?? '—' }}</td>
                                <td>
                                    @php
                                        $badgeBg = 'var(--surface-alt)';
                                        $badgeText = 'var(--body)';
                                        $border = '1px solid var(--border)';
                                        
                                        if ($project->status === 'completed') {
                                            $badgeBg = 'var(--success-soft)';
                                            $badgeText = 'var(--success)';
                                            $border = 'none';
                                        } elseif ($project->status === 'in_progress') {
                                            $badgeBg = 'rgba(37,99,235,0.1)';
                                            $badgeText = '#2563eb';
                                            $border = 'none';
                                        } elseif ($project->status === 'in_review') {
                                            $badgeBg = 'var(--warning-soft)';
                                            $badgeText = 'var(--warning)';
                                            $border = 'none';
                                        }
                                    @endphp
                                    <span class="bento-status-pill" style="background:{{ $badgeBg }}; color:{{ $badgeText }}; border:{{ $border }};">
                                        {{ str_replace('_', ' ', $project->status) }}
                                    </span>
                                </td>
                                <td class="text-right pr-5">
                                    <a href="{{ route('projets.show', $project->id) }}" style="color:var(--brand); font-weight:600; font-size:12.5px;">Voir →</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-8">Aucun projet trouvé</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===== Capacité équipe ===== -->
            <div class="bento-card area-team p-6">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="bento-section-title">Capacité équipe</p>
                        @php
                            $overloaded = collect($testersCapacity)->where('color', 'red')->count();
                        @endphp
                        <p class="bento-section-sub mt-1">{{ count($testersCapacity) }} testeur(s) @if($overloaded > 0)· <span style="color:var(--danger); font-weight:600;">{{ $overloaded }} surchargé(s)</span>@endif</p>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-xs font-semibold px-3.5 py-2 rounded-lg text-white flex-shrink-0" style="background:var(--brand);">Rapport</button>
                        <div x-show="open" @click.outside="open = false" x-cloak class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-[var(--surface)] ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1" role="menu">
                                <a href="{{ route('admin.rapport-pdf', ['period' => $filterPeriod, 'project' => $filterProject, 'tester' => $filterTester, 'format' => 'pdf']) }}" target="_blank" class="block px-4 py-2 text-sm text-[var(--ink)] hover:bg-[var(--surface-alt)]">Exporter en PDF</a>
                                <a href="{{ route('admin.rapport-pdf', ['period' => $filterPeriod, 'project' => $filterProject, 'tester' => $filterTester, 'format' => 'word']) }}" target="_blank" class="block px-4 py-2 text-sm text-[var(--ink)] hover:bg-[var(--surface-alt)]">Exporter en Word</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="height:150px; margin-top:18px;"><canvas id="teamBarChart"></canvas></div>
            </div>

        </div>
    </div>

    {{-- ── Modale : statistiques détaillées par projet ── --}}
    <div x-data="statsModal()"
        @open-stats.window="open($event.detail.type)"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[80] overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="show = false"></div>
        <div class="relative min-h-screen flex items-start sm:items-center justify-center p-4">
            <div class="relative z-10 w-full max-w-3xl bg-[var(--surface)] rounded-2xl shadow-2xl border border-[var(--border)] overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-[var(--border)]">
                    <div>
                        <h3 class="bento-section-title" style="font-size:16px;" x-text="title"></h3>
                        <p class="bento-section-sub mt-0.5">Nombre de projets : <span class="mono">{{ $perProjectStats->count() }}</span></p>
                    </div>
                    <button @click="show = false" class="p-2 rounded-lg hover:bg-[var(--surface-alt)] text-[var(--muted)]" aria-label="Fermer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 pt-4">
                    <a :href="pdfUrl" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold text-white transition hover:brightness-110"
                        style="background:var(--brand);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Télécharger ces statistiques en PDF
                    </a>
                </div>

                <div class="px-6 py-4">
                    <div style="height:{{ $perProjectStats->count() > 6 ? '380px' : '260px' }};">
                        <canvas x-ref="chart"></canvas>
                    </div>
                </div>

                <div class="px-6 pb-6 overflow-x-auto">
                    <table class="bento-data-table w-full">
                        <thead>
                            <tr>
                                <th>Projet</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Exécutés</th>
                                <th class="text-center">Validés</th>
                                <th class="text-center">Non validés</th>
                                <th class="text-center">Sous réserve</th>
                                <th class="text-center">Optim.</th>
                                <th class="text-center">Non exéc.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perProjectStats as $row)
                            <tr>
                                <td style="font-weight:600; color:var(--ink);">{{ $row['name'] }}</td>
                                <td class="text-center mono">{{ $row['total'] }}</td>
                                <td class="text-center mono">{{ $row['executed'] }}</td>
                                <td class="text-center mono" style="color:var(--success);">{{ $row['valide'] }}</td>
                                <td class="text-center mono" style="color:var(--danger);">{{ $row['non_valide'] }}</td>
                                <td class="text-center mono" style="color:#a855f7;">{{ $row['sous_reserve'] }}</td>
                                <td class="text-center mono" style="color:var(--warning);">{{ $row['optimisation'] }}</td>
                                <td class="text-center mono" style="color:var(--muted);">{{ $row['non_executed'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-8">Aucune donnée avec les filtres actuels</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        function statsModal() {
            return {
                show: false,
                type: 'cases',
                chart: null,
                data: {
                    cases: {
                        labels: {!! json_encode($perProjectStats->pluck('name')) !!},
                        executed: {!! json_encode($perProjectStats->pluck('executed')) !!},
                        nonExecuted: {!! json_encode($perProjectStats->pluck('non_executed')) !!},
                    },
                    status: {
                        labels: {!! json_encode($perProjectStats->pluck('name')) !!},
                        valide: {!! json_encode($perProjectStats->pluck('valide')) !!},
                        nonValide: {!! json_encode($perProjectStats->pluck('non_valide')) !!},
                        sousReserve: {!! json_encode($perProjectStats->pluck('sous_reserve')) !!},
                        optimisation: {!! json_encode($perProjectStats->pluck('optimisation')) !!},
                        nonExecuted: {!! json_encode($perProjectStats->pluck('non_executed')) !!},
                    }
                },

                get title() {
                    return this.type === 'cases'
                        ? 'Cas de test par projet'
                        : 'Répartition des statuts par projet';
                },

                get pdfUrl() {
                    const params = new URLSearchParams({
                        type: this.type,
                        period: '{{ $filterPeriod }}',
                        project: '{{ $filterProject }}',
                        tester: '{{ $filterTester }}',
                    });
                    return '{{ route('admin.rapport-stats-pdf') }}' + '?' + params.toString();
                },

                open(type) {
                    this.type = type;
                    this.show = true;
                    this.$nextTick(() => this.renderChart());
                },

                renderChart() {
                    if (this.chart) { this.chart.destroy(); this.chart = null; }
                    const canvas = this.$refs.chart;
                    if (!canvas || typeof Chart === 'undefined') return;

                    function cssVar(name){ return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); }
                    const bodyColor = cssVar('--body'), inkColor = cssVar('--ink');
                    const success = cssVar('--success'), danger = cssVar('--danger'), warning = cssVar('--warning'), track = cssVar('--track');
                    const brand = cssVar('--brand');

                    const labels = this.data.cases.labels;

                    if (this.type === 'cases') {
                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    { label: 'Exécutés', data: this.data.cases.executed, backgroundColor: brand, borderRadius: 3 },
                                    { label: 'Non exécutés', data: this.data.cases.nonExecuted, backgroundColor: track, borderRadius: 3 },
                                ]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { color: bodyColor, font: { family: 'Inter', size: 10.5 }, boxWidth: 9, boxHeight: 9 } }
                                },
                                scales: {
                                    x: { stacked: true, beginAtZero: true, grid: { color: cssVar('--grid') }, border: { display: false }, ticks: { color: bodyColor, font: { family: 'JetBrains Mono', size: 10 }, precision: 0 } },
                                    y: { stacked: true, grid: { display: false }, border: { display: false }, ticks: { color: bodyColor, font: { family: 'Inter', size: 11.5, weight: 600 } } }
                                }
                            }
                        });
                    } else {
                        this.chart = new Chart(canvas, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [
                                    { label: 'Validé', data: this.data.status.valide, backgroundColor: success, borderRadius: 2 },
                                    { label: 'Non validé', data: this.data.status.nonValide, backgroundColor: danger, borderRadius: 2 },
                                    { label: 'Sous réserve', data: this.data.status.sousReserve, backgroundColor: '#a855f7', borderRadius: 2 },
                                    { label: 'Optim.', data: this.data.status.optimisation, backgroundColor: warning, borderRadius: 2 },
                                    { label: 'Non exéc.', data: this.data.status.nonExecuted, backgroundColor: track, borderRadius: 2 },
                                ]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'bottom', labels: { color: bodyColor, font: { family: 'Inter', size: 10.5 }, boxWidth: 9, boxHeight: 9 } }
                                },
                                scales: {
                                    x: { stacked: true, beginAtZero: true, grid: { color: cssVar('--grid') }, border: { display: false }, ticks: { color: bodyColor, font: { family: 'JetBrains Mono', size: 10 }, precision: 0 } },
                                    y: { stacked: true, grid: { display: false }, border: { display: false }, ticks: { color: bodyColor, font: { family: 'Inter', size: 11.5, weight: 600 } } }
                                }
                            }
                        });
                    }
                }
            };
        }

        document.addEventListener('livewire:initialized', () => {
            let charts = {};

            function cssVar(name){ return getComputedStyle(document.documentElement).getPropertyValue(name).trim(); }
            function destroyAll(){ Object.values(charts).forEach(c => c && c.destroy()); charts = {}; }

            function renderAllCharts(){
                destroyAll();
                const gridColor = cssVar('--grid'), bodyColor = cssVar('--body'), inkColor = cssVar('--ink');
                const success = cssVar('--success'), danger = cssVar('--danger'), warning = cssVar('--warning'), track = cssVar('--track');
                const brand = cssVar('--brand');

                // Pièce maîtresse : anneau complet
                const heroCtx = document.getElementById('heroRing');
                if(heroCtx) {
                    charts.hero = new Chart(heroCtx, {
                        type:'doughnut',
                        data:{ datasets:[{ data:[{{ $adminStats['executed'] }}, {{ max(0, $totalTemplates - $adminStats['executed']) }}], backgroundColor:[brand, track], borderWidth:0 }] },
                        options:{
                            responsive:true, maintainAspectRatio:false, cutout:'80%', rotation:-90,
                            plugins:{ legend:{ display:false }, tooltip:{ enabled:false } },
                            animation:{ animateRotate:true, duration:700 }
                        }
                    });
                }

                const baseScales = {
                    x:{ grid:{ display:false }, border:{ display:false }, ticks:{ color:bodyColor, font:{ family:'Inter', size:10.5 } } },
                    y:{ beginAtZero:true, grid:{ color:gridColor }, border:{ display:false }, ticks:{ color:bodyColor, font:{ family:'JetBrains Mono', size:10 }, precision:0 } }
                };

                const iatCtx = document.getElementById('iatBarChart');
                if(iatCtx) {
                    charts.iat = new Chart(iatCtx, {
                        type:'bar',
                        data:{ labels:['Validé','Non validé','Sous réserve','Optim.','Non exéc.'],
                            datasets:[{ data:[{{ $adminStats['valide'] }}, {{ $adminStats['non_valide'] }}, {{ $adminStats['sous_reserve'] }}, {{ $adminStats['optimisation'] }}, {{ max(0, $adminStats['total'] - $adminStats['executed']) }}], backgroundColor:[success, danger, '#a855f7', warning, track], borderRadius:4, maxBarThickness:32 }] },
                        options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales: baseScales }
                    });
                }

                const uatCtx = document.getElementById('uatBarChart');
                if(uatCtx) {
                    charts.uat = new Chart(uatCtx, {
                        type:'bar',
                        data:{ labels:['Approuvé','Rejeté','En attente'],
                            datasets:[{ data:[{{ $clientStats['validated'] }}, {{ $clientStats['rejected'] }}, {{ $clientStats['pending'] }}], backgroundColor:[success, danger, track], borderRadius:4, maxBarThickness:42 }] },
                        options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales: baseScales }
                    });
                }

                const teamCtx = document.getElementById('teamBarChart');
                if(teamCtx) {
                    const teamLabels = {!! json_encode(collect($testersCapacity)->pluck('name')) !!};
                    const teamAssigned = {!! json_encode(collect($testersCapacity)->pluck('total')) !!};
                    const teamExecuted = {!! json_encode(collect($testersCapacity)->pluck('done')) !!};
                    
                    charts.team = new Chart(teamCtx, {
                        type:'bar',
                        data:{ labels: teamLabels,
                            datasets:[
                                { label:'Assignés', data: teamAssigned, backgroundColor: track, borderRadius:4, maxBarThickness:14 },
                                { label:'Exécutés', data: teamExecuted, backgroundColor: brand, borderRadius:4, maxBarThickness:14 }
                            ] },
                        options:{
                            indexAxis:'y', responsive:true, maintainAspectRatio:false,
                            plugins:{ legend:{ display:true, position:'bottom', labels:{ color:bodyColor, font:{family:'Inter', size:10.5}, boxWidth:9, boxHeight:9 } } },
                            scales:{
                                x:{ beginAtZero:true, grid:{ color:gridColor }, border:{ display:false }, ticks:{ color:bodyColor, font:{ family:'JetBrains Mono', size:10 } } },
                                y:{ grid:{ display:false }, border:{ display:false }, ticks:{ color:bodyColor, font:{ family:'Inter', size:11.5, weight:600 } } }
                            }
                        }
                    });
                }
            }
            
            renderAllCharts();

            // Rerender on dark mode change
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        setTimeout(renderAllCharts, 50);
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });

            // Rerender on Livewire update
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    setTimeout(renderAllCharts, 50);
                })
            });
        });
    </script>
    @endpush
</div>
