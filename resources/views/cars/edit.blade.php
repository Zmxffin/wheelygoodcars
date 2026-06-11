@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h3 class="mb-3">Aanbod bewerken — {{ $car->brand }} {{ $car->model }}</h3>

            @include('layouts.error')

            <form action="{{ route('cars.update', $car) }}" method="POST">
                @csrf @method('PUT')

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Vraagprijs (&euro;)</label>
                            <input type="number" step="0.01" name="price" class="form-control"
                                   value="{{ old('price', $car->price) }}" required>
                        </div>

                        <div class="form-check form-switch">
                            <input type="hidden" name="sold" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="sold"
                                   name="sold" value="1" {{ $car->isSold() ? 'checked' : '' }}>
                            <label class="form-check-label" for="sold">
                                Gemarkeerd als verkocht (verdwijnt van de publieke site)
                            </label>
                        </div>
                    </div>
                </div>

                {{-- F11 — adjust tags --}}
                <div class="card mb-3">
                    <div class="card-body">
                        <h6 class="card-title">Tags</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <input type="checkbox" class="btn-check" name="tags[]" value="{{ $tag->id }}"
                                       id="tag-{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', $car->tags->pluck('id')->all())) ? 'checked' : '' }}>
                                <label class="btn btn-outline-secondary btn-sm" for="tag-{{ $tag->id }}"
                                       style="border-color: {{ $tag->color }};">{{ $tag->name }}</label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('cars.index') }}" class="btn btn-secondary">Terug</a>
                    <button type="submit" class="btn btn-primary text-dark">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
