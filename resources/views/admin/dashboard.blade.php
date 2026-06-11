@extends('layouts.app')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">📊 Live dashboard</h3>
        <span class="text-muted small">Laatst ververst: <span id="last-update">—</span></span>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['offered', "Auto's aangeboden", 'primary'],
                ['sold', 'Verkocht', 'success'],
                ['today', 'Vandaag aangeboden', 'info'],
                ['sellers', 'Aanbieders', 'warning'],
                ['views_today', 'Views vandaag', 'danger'],
                ['avg_per_seller', "Gem. auto's per aanbieder", 'dark'],
            ];
        @endphp
        @foreach ($cards as [$key, $label, $color])
            <div class="col-md-4 col-6">
                <div class="card text-bg-{{ $color }} shadow-sm h-100">
                    <div class="card-body text-center">
                        <div class="display-5 fw-bold" id="stat-{{ $key }}">—</div>
                        <div>{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">Verkocht t.o.v. totaal aanbod</h6>
                    <div class="progress mb-2" style="height: 2rem;">
                        <div id="sold-progress" class="progress-bar bg-success fw-bold" role="progressbar" style="width: 0%;">0%</div>
                    </div>
                    <p class="text-muted small mb-0">Aandeel van het aanbod dat al verkocht is.</p>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title">Aanbod-verdeling</h6>
                    <canvas id="status-chart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('status-chart');
    const chart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Te koop', 'Verkocht'],
            datasets: [{ data: [0, 0], backgroundColor: ['#ff7a00', '#198754'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });

    async function refresh() {
        try {
            const res = await fetch(`{{ route('admin.dashboard.data') }}`, { headers: { 'Accept': 'application/json' } });
            const d = await res.json();

            ['offered','sold','today','sellers','views_today','avg_per_seller'].forEach(k => {
                document.getElementById('stat-' + k).textContent = d[k];
            });

            const bar = document.getElementById('sold-progress');
            bar.style.width = d.sold_percentage + '%';
            bar.textContent = d.sold_percentage + '%';

            chart.data.datasets[0].data = [d.for_sale, d.sold];
            chart.update();

            document.getElementById('last-update').textContent = new Date().toLocaleTimeString('nl-NL');
        } catch (e) { /* ignore transient errors */ }
    }

    refresh();
    setInterval(refresh, 5000); // ververst minstens elke 10 seconden
</script>
@endpush
