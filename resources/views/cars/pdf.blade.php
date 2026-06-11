<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #222; }
        .header { border-bottom: 4px solid #ff7a00; padding-bottom: 10px; margin-bottom: 20px; }
        .brand { color: #ff7a00; font-weight: bold; font-size: 26px; }
        .plate {
            display: inline-block; background: #ffdd00; border: 2px solid #000;
            border-radius: 6px; padding: 4px 14px; font-weight: bold; font-size: 20px; letter-spacing: 2px;
        }
        h1 { font-size: 24px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; }
        td.label { color: #777; width: 40%; }
        .price { font-size: 28px; color: #ff7a00; font-weight: bold; }
        .tag { display: inline-block; padding: 3px 8px; border-radius: 4px; color: #fff; font-size: 12px; margin: 2px; }
        .footer { margin-top: 30px; font-size: 11px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <span class="brand">Wheely good cars!</span>
    </div>

    <h1>{{ $car->brand }} {{ $car->model }}</h1>
    <p><span class="plate">{{ $car->license_plate }}</span></p>
    <p class="price">&euro; {{ number_format($car->price, 0, ',', '.') }}</p>

    <table>
        <tr><td class="label">Kilometerstand</td><td>{{ number_format($car->mileage, 0, ',', '.') }} km</td></tr>
        <tr><td class="label">Bouwjaar</td><td>{{ $car->production_year ?? '—' }}</td></tr>
        <tr><td class="label">Kleur</td><td>{{ $car->color ?? '—' }}</td></tr>
        <tr><td class="label">Zitplaatsen</td><td>{{ $car->seats ?? '—' }}</td></tr>
        <tr><td class="label">Deuren</td><td>{{ $car->doors ?? '—' }}</td></tr>
        <tr><td class="label">Gewicht</td><td>{{ $car->weight ? $car->weight.' kg' : '—' }}</td></tr>
        <tr><td class="label">Aangeboden door</td><td>{{ $car->user->name }}@if($car->user->phone_number) — {{ $car->user->phone_number }}@endif</td></tr>
    </table>

    @if ($car->tags->isNotEmpty())
        <p style="margin-top:16px;">
            @foreach ($car->tags as $tag)
                <span class="tag" style="background: {{ $tag->color }};">{{ $tag->name }}</span>
            @endforeach
        </p>
    @endif

    <div class="footer">
        Gegenereerd op {{ now()->format('d-m-Y H:i') }} — Wheely Good Cars
    </div>
</body>
</html>
