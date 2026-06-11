@extends('layouts.app')

@section('content')
    <h3 class="mb-1">Opvallende aanbieders</h3>
    <p class="text-muted">Aanbieders die op één of meer risico-signalen scoren en mogelijk gereviewd moeten worden.</p>

    @if ($notable->isEmpty())
        <div class="alert alert-success">Geen opvallende aanbieders gevonden.</div>
    @else
        <p><strong>{{ $notable->count() }}</strong> aanbieder(s) gemarkeerd.</p>
        <div class="row g-3">
            @foreach ($notable as $row)
                <div class="col-md-6">
                    <div class="card shadow-sm h-100 border-start border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <h5 class="card-title mb-0">{{ $row->seller->name }}</h5>
                                <span class="badge bg-danger">{{ $row->reasons->count() }} signaal(en)</span>
                            </div>
                            <p class="text-muted small mb-2">
                                {{ $row->seller->email }}
                                @if ($row->seller->phone_number) &middot; {{ $row->seller->phone_number }} @endif
                                &middot; {{ $row->seller->cars->count() }} auto('s)
                            </p>
                            <ul class="mb-0">
                                @foreach ($row->reasons as $reason)
                                    <li>{{ $reason }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
