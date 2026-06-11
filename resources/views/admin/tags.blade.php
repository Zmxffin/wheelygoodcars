@extends('layouts.app')

@section('content')
    <h3 class="mb-3">Tag-statistieken</h3>
    <p class="text-muted">Hoe vaak elke tag wordt gebruikt, uitgesplitst naar verkochte en niet-verkochte auto's.</p>

    <div class="table-responsive">
        <table class="table table-hover align-middle bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>Tag</th>
                    <th class="text-center">Niet verkocht</th>
                    <th class="text-center">Verkocht</th>
                    <th class="text-center">Totaal</th>
                    <th style="width: 30%;">Verhouding</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td><span class="badge" style="background-color: {{ $tag->color }};">{{ $tag->name }}</span></td>
                        <td class="text-center">{{ $tag->unsold_count }}</td>
                        <td class="text-center">{{ $tag->sold_count }}</td>
                        <td class="text-center fw-bold">{{ $tag->total_count }}</td>
                        <td>
                            @if ($tag->total_count > 0)
                                <div class="progress" style="height: 1.2rem;">
                                    <div class="progress-bar bg-primary" style="width: {{ $tag->unsold_count / $tag->total_count * 100 }}%">
                                        {{ $tag->unsold_count }}
                                    </div>
                                    <div class="progress-bar bg-success" style="width: {{ $tag->sold_count / $tag->total_count * 100 }}%">
                                        {{ $tag->sold_count }}
                                    </div>
                                </div>
                            @else
                                <span class="text-muted small">Niet in gebruik</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
