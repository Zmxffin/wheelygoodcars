@extends('layouts.app')

@section('content')
    <h3 class="mb-3">Alle auto's</h3>

    <div class="row">
        {{-- F9 — tag filter sidebar --}}
        <div class="col-lg-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Filter op tags</h6>
                    <div id="tag-filters" class="d-flex flex-column gap-1" style="max-height: 320px; overflow-y: auto;">
                        @foreach ($tags as $tag)
                            <div class="form-check">
                                <input class="form-check-input filter-tag" type="checkbox" value="{{ $tag->id }}"
                                       id="filter-{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}>
                                <label class="form-check-label" for="filter-{{ $tag->id }}">
                                    <span class="badge" style="background-color: {{ $tag->color }};">{{ $tag->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            {{-- F7 — search without page reload --}}
            <div class="input-group mb-3">
                <span class="input-group-text">🔍</span>
                <input type="text" id="search" class="form-control" placeholder="Zoek op merk of model…"
                       value="{{ $search }}" autocomplete="off">
            </div>

            <div id="car-grid">
                @include('public.partials.grid')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const grid = document.getElementById('car-grid');
    const searchInput = document.getElementById('search');
    let timer = null;

    function selectedTags() {
        return Array.from(document.querySelectorAll('.filter-tag:checked')).map(c => c.value);
    }

    async function refresh(page = 1) {
        const params = new URLSearchParams();
        if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
        selectedTags().forEach(id => params.append('tags[]', id));
        params.set('page', page);

        grid.style.opacity = .5;
        const res = await fetch(`{{ route('public.index') }}?${params.toString()}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        grid.innerHTML = await res.text();
        grid.style.opacity = 1;
        // Keep the URL in sync so the page is shareable / bookmarkable.
        history.replaceState(null, '', `{{ route('public.index') }}?${params.toString()}`);
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => refresh(1), 300);
    });

    document.querySelectorAll('.filter-tag').forEach(cb =>
        cb.addEventListener('change', () => refresh(1)));

    // F8 — intercept paginator links so paging also happens without a full reload.
    grid.addEventListener('click', (e) => {
        const link = e.target.closest('.pagination a');
        if (!link) return;
        e.preventDefault();
        const page = new URL(link.href).searchParams.get('page') || 1;
        refresh(page);
    });
</script>
@endpush
