@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="mb-3">Aanbod plaatsen</h3>

            {{-- F6 — progressbar showing how far you are in the form --}}
            <div class="progress mb-4" style="height: 1.5rem;">
                <div id="form-progress" class="progress-bar bg-primary text-dark fw-bold" role="progressbar"
                     style="width: 25%;">Stap 1 van 4</div>
            </div>

            @include('layouts.error')

            <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data" id="car-form">
                @csrf

                {{-- Step 1: license plate + RDW lookup (A1 / B1) --}}
                <div class="form-step" data-step="0">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-3">Stap 1 — Kenteken</h5>
                            <p class="text-muted">Vul je kenteken in; wij halen de autogegevens op bij de RDW.</p>
                            <div class="kenteken-bar shadow-sm mx-auto mb-3">
                                <span class="kenteken-country">NL</span>
                                <input type="text" name="license_plate" id="license_plate" class="kenteken-input"
                                       placeholder="AA-BB-12" value="{{ old('license_plate', request('plate')) }}" autocomplete="off" required>
                            </div>
                            <button type="button" class="btn btn-outline-secondary" id="rdw-lookup">Gegevens ophalen (RDW)</button>
                            <div id="rdw-feedback" class="mt-2 small"></div>
                        </div>
                    </div>
                </div>

                {{-- Step 2: car details --}}
                <div class="form-step d-none" data-step="1">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Stap 2 — Gegevens</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Merk *</label>
                                    <input type="text" name="brand" id="brand" class="form-control" value="{{ old('brand') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Model *</label>
                                    <input type="text" name="model" id="model" class="form-control" value="{{ old('model') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Vraagprijs (&euro;) *</label>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kilometerstand *</label>
                                    <input type="number" name="mileage" class="form-control" value="{{ old('mileage') }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Bouwjaar</label>
                                    <input type="number" name="production_year" id="production_year" class="form-control" value="{{ old('production_year') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Zitplaatsen</label>
                                    <input type="number" name="seats" id="seats" class="form-control" value="{{ old('seats') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Deuren</label>
                                    <input type="number" name="doors" id="doors" class="form-control" value="{{ old('doors') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gewicht (kg)</label>
                                    <input type="number" name="weight" id="weight" class="form-control" value="{{ old('weight') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kleur</label>
                                    <input type="text" name="color" id="color" class="form-control" value="{{ old('color') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3: image upload (B7) --}}
                <div class="form-step d-none" data-step="2">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Stap 3 — Foto</h5>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            <div class="mt-3 text-center">
                                <img id="image-preview" class="img-fluid rounded d-none" style="max-height: 240px;" alt="Voorbeeld">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 4: tags (F10) --}}
                <div class="form-step d-none" data-step="3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Stap 4 — Tags</h5>
                            <p class="text-muted">Selecteer de eigenschappen die op deze auto van toepassing zijn.</p>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($tags as $tag)
                                    <input type="checkbox" class="btn-check" name="tags[]" value="{{ $tag->id }}"
                                           id="tag-{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary btn-sm" for="tag-{{ $tag->id }}"
                                           style="border-color: {{ $tag->color }};">{{ $tag->name }}</label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Navigation --}}
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-secondary" id="prev-step" disabled>Vorige</button>
                    <button type="button" class="btn btn-primary text-dark" id="next-step">Volgende</button>
                    <button type="submit" class="btn btn-success d-none" id="submit-step">Aanbod plaatsen</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const progress = document.getElementById('form-progress');
    const prevBtn = document.getElementById('prev-step');
    const nextBtn = document.getElementById('next-step');
    const submitBtn = document.getElementById('submit-step');
    let current = {{ $errors->any() ? 1 : 0 }};

    function render() {
        steps.forEach((s, i) => s.classList.toggle('d-none', i !== current));
        const pct = Math.round(((current + 1) / steps.length) * 100);
        progress.style.width = pct + '%';
        progress.textContent = `Stap ${current + 1} van ${steps.length}`;
        prevBtn.disabled = current === 0;
        nextBtn.classList.toggle('d-none', current === steps.length - 1);
        submitBtn.classList.toggle('d-none', current !== steps.length - 1);
    }

    function validStep() {
        // Require the visible step's required fields before advancing.
        const inputs = steps[current].querySelectorAll('input[required]');
        for (const input of inputs) {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                input.focus();
                return false;
            }
            input.classList.remove('is-invalid');
        }
        return true;
    }

    nextBtn.addEventListener('click', () => {
        if (!validStep()) return;
        if (current < steps.length - 1) current++;
        render();
    });
    prevBtn.addEventListener('click', () => {
        if (current > 0) current--;
        render();
    });

    // B1 — RDW lookup
    document.getElementById('rdw-lookup').addEventListener('click', async () => {
        const plate = document.getElementById('license_plate').value.trim();
        const feedback = document.getElementById('rdw-feedback');
        if (!plate) { feedback.innerHTML = '<span class="text-danger">Vul eerst een kenteken in.</span>'; return; }
        feedback.innerHTML = '<span class="text-muted">Bezig met ophalen…</span>';
        try {
            const res = await fetch(`/rdw/${encodeURIComponent(plate)}`, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            if (!json.found) {
                feedback.innerHTML = `<span class="text-warning">${json.message || 'Niet gevonden.'} Je kunt de gegevens handmatig invullen.</span>`;
                return;
            }
            const d = json.data;
            const set = (id, val) => { if (val !== null && val !== undefined) document.getElementById(id).value = val; };
            set('brand', d.brand); set('model', d.model); set('seats', d.seats);
            set('doors', d.doors); set('production_year', d.production_year);
            set('weight', d.weight); set('color', d.color);
            feedback.innerHTML = '<span class="text-success">Gegevens opgehaald! Ga door naar de volgende stap.</span>';
        } catch (e) {
            feedback.innerHTML = '<span class="text-danger">Ophalen mislukt.</span>';
        }
    });

    // B7 — image preview
    document.getElementById('image').addEventListener('change', (e) => {
        const file = e.target.files[0];
        const preview = document.getElementById('image-preview');
        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
        } else {
            preview.classList.add('d-none');
        }
    });

    render();
</script>
@endpush
