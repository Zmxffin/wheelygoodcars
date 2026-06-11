@extends('layouts.app')

@section('content')
    <a href="{{ route('public.index') }}" class="btn btn-link ps-0 mb-2">&larr; Terug naar aanbod</a>

    <div class="row g-4">
        <div class="col-md-7">
            @if ($car->image)
                <img src="{{ asset('storage/'.$car->image) }}" class="img-fluid rounded shadow-sm w-100"
                     style="max-height: 420px; object-fit: cover;" alt="{{ $car->brand }} {{ $car->model }}">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted"
                     style="height: 360px;">Geen foto beschikbaar</div>
            @endif
        </div>

        <div class="col-md-5">
            <div class="d-flex justify-content-between align-items-start">
                <h2 class="mb-1">{{ $car->brand }} {{ $car->model }}</h2>
                <span class="badge bg-warning text-dark fs-6">{{ $car->license_plate }}</span>
            </div>
            <p class="text-primary fw-bold display-6">&euro; {{ number_format($car->price, 0, ',', '.') }}</p>

            <div class="mb-3">
                @foreach ($car->tags as $tag)
                    <span class="badge" style="background-color: {{ $tag->color }};">{{ $tag->name }}</span>
                @endforeach
            </div>

            <table class="table table-sm">
                <tr><th>Bouwjaar</th><td>{{ $car->production_year ?? '—' }}</td></tr>
                <tr><th>Kilometerstand</th><td>{{ number_format($car->mileage, 0, ',', '.') }} km</td></tr>
                <tr><th>Kleur</th><td>{{ $car->color ?? '—' }}</td></tr>
                <tr><th>Zitplaatsen</th><td>{{ $car->seats ?? '—' }}</td></tr>
                <tr><th>Deuren</th><td>{{ $car->doors ?? '—' }}</td></tr>
                <tr><th>Gewicht</th><td>{{ $car->weight ? $car->weight.' kg' : '—' }}</td></tr>
                <tr><th>Aangeboden door</th><td>{{ $car->user->name }}</td></tr>
                @if ($car->user->phone_number)
                    <tr><th>Telefoon</th><td>{{ $car->user->phone_number }}</td></tr>
                @endif
            </table>
            <p class="text-muted small">{{ $car->views }} keer bekeken</p>
        </div>
    </div>

    {{-- F4 — popup ("toast") that appears 10 seconds after opening the page --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="views-toast" class="toast" role="alert" data-bs-autohide="false">
            <div class="toast-header">
                <strong class="me-auto text-primary">🔥 Populaire auto</strong>
                <small>nu</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Sluiten"></button>
            </div>
            <div class="toast-body" id="views-toast-body">
                Andere klanten bekijken deze auto ook!
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    setTimeout(async () => {
        let count = null;
        try {
            const res = await fetch(`{{ route('public.views', $car) }}`, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            count = data.today;
        } catch (e) { /* fall back to fixed text */ }

        const body = document.getElementById('views-toast-body');
        if (count && count > 1) {
            body.textContent = `${count} klanten bekeken deze auto vandaag. Wees er snel bij!`;
        } else {
            body.textContent = 'Meerdere klanten bekeken deze auto vandaag. Wees er snel bij!';
        }
        new bootstrap.Toast(document.getElementById('views-toast')).show();
    }, 10000);
</script>
@endpush
