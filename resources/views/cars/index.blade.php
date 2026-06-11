@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Mijn aanbod</h3>
        <a href="{{ route('cars.create') }}" class="btn btn-primary text-dark">+ Aanbod plaatsen</a>
    </div>

    @if ($cars->isEmpty())
        <div class="alert alert-info">Je hebt nog geen auto's aangeboden.
            <a href="{{ route('cars.create') }}">Plaats je eerste aanbod.</a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle bg-white shadow-sm">
                <thead class="table-dark">
                    <tr>
                        <th>Foto</th>
                        <th>Auto</th>
                        <th>Kenteken</th>
                        <th class="text-end">Prijs</th>
                        <th class="text-end">Km-stand</th>
                        <th class="text-center">Views</th>
                        <th>Tags</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cars as $car)
                        <tr>
                            <td>
                                @if ($car->image)
                                    <img src="{{ asset('storage/'.$car->image) }}" alt="" style="width:64px;height:48px;object-fit:cover;" class="rounded">
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $car->brand }}</strong> {{ $car->model }}<br>
                                <span class="text-muted small">{{ $car->production_year }}</span>
                            </td>
                            <td><span class="badge bg-warning text-dark">{{ $car->license_plate }}</span></td>
                            <td class="text-end">&euro; {{ number_format($car->price, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($car->mileage, 0, ',', '.') }} km</td>
                            <td class="text-center" title="Aantal weergaven">{{ $car->views }}</td>
                            <td>
                                @foreach ($car->tags as $tag)
                                    <span class="badge mb-1" style="background-color: {{ $tag->color }};">{{ $tag->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                {{-- F1 — one-click status toggle without page reload --}}
                                <button class="btn btn-sm status-toggle {{ $car->isSold() ? 'btn-success' : 'btn-outline-secondary' }}"
                                        data-url="{{ route('cars.toggleStatus', $car) }}">
                                    {{ $car->isSold() ? 'Verkocht' : 'Te koop' }}
                                </button>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('cars.edit', $car) }}" class="btn btn-sm btn-outline-primary">Bewerk</a>
                                <a href="{{ route('cars.pdf', $car) }}" class="btn btn-sm btn-outline-dark">PDF</a>
                                <form action="{{ route('cars.destroy', $car) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Weet je zeker dat je dit aanbod wilt verwijderen?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Verwijder</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    document.querySelectorAll('.status-toggle').forEach(btn => {
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            try {
                const res = await fetch(btn.dataset.url, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.sold) {
                    btn.textContent = 'Verkocht';
                    btn.classList.remove('btn-outline-secondary');
                    btn.classList.add('btn-success');
                } else {
                    btn.textContent = 'Te koop';
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }
            } finally {
                btn.disabled = false;
            }
        });
    });
</script>
@endpush
