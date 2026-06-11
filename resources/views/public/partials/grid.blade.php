@if ($cars->isEmpty())
    <div class="alert alert-info">Geen auto's gevonden die aan je zoekopdracht voldoen.</div>
@else
    <div class="row g-3">
        @foreach ($cars as $car)
            @php $big = in_array($car->id, $highlighted); @endphp
            <div class="{{ $big ? 'col-md-8' : 'col-md-4' }} col-sm-6">
                <a href="{{ route('public.show', $car) }}" class="text-decoration-none text-dark">
                    <div class="card h-100 shadow-sm {{ $big ? 'car-card-highlight' : '' }}">
                        @if ($car->image)
                            <img src="{{ asset('storage/'.$car->image) }}" class="card-img-top"
                                 style="height: {{ $big ? 260 : 160 }}px; object-fit: cover;" alt="{{ $car->brand }} {{ $car->model }}">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted"
                                 style="height: {{ $big ? 260 : 160 }}px;">Geen foto</div>
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title mb-0">{{ $car->brand }} {{ $car->model }}</h5>
                                <span class="badge bg-warning text-dark">{{ $car->license_plate }}</span>
                            </div>
                            <p class="text-primary fw-bold fs-5 mb-1">&euro; {{ number_format($car->price, 0, ',', '.') }}</p>
                            <p class="text-muted small mb-2">
                                {{ $car->production_year }} &middot; {{ number_format($car->mileage, 0, ',', '.') }} km
                            </p>
                            {{-- F12 — tags as badges --}}
                            <div>
                                @foreach ($car->tags as $tag)
                                    <span class="badge mb-1" style="background-color: {{ $tag->color }};">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- F8 — pagination --}}
    <div class="mt-4">
        {{ $cars->links() }}
    </div>
@endif
