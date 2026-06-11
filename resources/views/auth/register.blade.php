@extends('layouts.app')

@section('content')

    <div class="fullheight">
        <div class="card p-4" style="min-width: 50%;">
            <div class="card-body">
                <h5 class="card-title mb-3">Registreren</h5>
                @include('layouts.error')
                <form action="{{ route('register.do') }}" method="POST">
                    @csrf
                    <div class="mb-3 row">
                        <label for="name" class="col-form-label col-sm-4">Naam:</label>
                        <div class="col-sm-8">
                        <input type="text" name="name" class="form-control" id="name"></div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-form-label col-sm-4">E-mailadres:</label>
                        <div class="col-sm-8">
                        <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com"></div>
                    </div>
                    <div class="mb-3 row">
                        <label for="phone_number" class="col-form-label col-sm-4">Telefoonnummer:</label>
                        <div class="col-sm-8">
                        <input type="text" name="phone_number" class="form-control" id="phone_number" value="{{ old('phone_number') }}" placeholder="06... (optioneel)"></div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password" class="col-form-label col-sm-4">Wachtwoord:</label>
                        <div class="col-sm-8">
                        <input type="password" name="password" class="form-control" id="password"></div>
                    </div>
                    <div class="mb-3 row">
                        <label for="password_confirmation" class="col-form-label col-sm-4">Wachtwoord (bevestigen):</label>
                        <div class="col-sm-8">
                        <input type="password" name="password_confirmation" class="form-control" id="password_confirmation"></div>
                    </div>
                    <div class="mb-3">
                        <input type="submit" class="btn btn-primary text-dark" value="Registreren">
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
