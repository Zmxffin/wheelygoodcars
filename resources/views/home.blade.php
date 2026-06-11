@extends('layouts.app')

@section('content')
    <div class="fullheight flex-column text-center">
        <h1 class="mb-2 fw-light">Een auto verkopen begint hier</h1>
        <p class="text-muted mb-4">Voer je kenteken in en wij vullen de rest alvast in.</p>

        <form action="{{ route('cars.create') }}" method="GET" class="kenteken-bar shadow-sm">
            <span class="kenteken-country">NL</span>
            <input type="text" name="plate" class="kenteken-input" placeholder="AA-BB-12"
                   value="{{ request('plate') }}" autocomplete="off" autofocus>
            <button type="submit" class="kenteken-go">Go!</button>
        </form>

        <a href="{{ route('public.index') }}" class="btn btn-link mt-4">Of bekijk het volledige aanbod &rarr;</a>
    </div>
@endsection
