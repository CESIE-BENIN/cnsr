@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')


<!-- Formulaire de filtre -->
<div class="card mb-4 p-3">
    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="commune" class="form-label">Commune</label>
            <select name="commune" id="commune" class="form-select">
                <option value="">Toutes les communes</option>
                @foreach($communes as $commune)
                    <option value="{{ $commune }}" {{ request('commune') == $commune ? 'selected' : '' }}>
                        {{ $commune }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label for="date" class="form-label">Date </label>
            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> Filtrer
            </button>
        </div>
    </form>
</div>

<<!-- Liste des déclarations -->
<div class="card mb-5">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>🕒 Déclarations récentes</strong>

        <!-- Sélecteur afficher plus / moins -->
        <form method="GET" action="{{ route('admin.dashboard') }}">
            <!-- conserver les filtres -->
            <input type="hidden" name="commune" value="{{ request('commune') }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">

            <select name="per_page" class="form-select form-select-sm"
                    onchange="this.form.submit()">
                <option value="5" {{ request('per_page',5)==5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page')==10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page')==20 ? 'selected' : '' }}>20</option>
            </select>
        </form>
    </div>

    <ul class="list-group list-group-flush">
        @forelse($accidents as $accident)
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            {{ $accident->commune }}
                        </h6>
                        <small class="text-muted">
                            <i class="fas fa-road"></i> {{ $accident->lieu }}
                            |
                            <i class="fas fa-car"></i>
                            <strong>{{ $accident->nombre_vehicules }}</strong> véhicule(s)
                        </small>
                    </div>

                    <span class="badge bg-secondary">
                        {{ $accident->created_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            </li>
        @empty
            <li class="list-group-item text-center text-muted">
                Aucune déclaration trouvée
            </li>
        @endforelse
    </ul>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $accidents->links('pagination::bootstrap-5') }}
</div>

<!-- Graphiques -->
<div class="row">
    <div class="col-md-6">
        <div class="card p-3">
            <h5><i class="fas fa-chart-bar"></i> Accidents par Commune</h5>
            <canvas id="communeChart" height="200"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-3">
            <h5><i class="fas fa-chart-line"></i> Accidents par Mois</h5>
            <canvas id="monthChart" height="200"></canvas>
        </div>
    </div>
</div>

<script>
    // Données pour le graphique Commune
    const communeLabels = {!! json_encode($accidents->pluck('commune')->unique()) !!};
    const communeData = {!! json_encode($accidents->groupBy('commune')->map->count()->values()) !!};

    new Chart(document.getElementById('communeChart'), {
        type: 'bar',
        data: {
            labels: communeLabels,
            datasets: [{
                label: 'Nombre d\'accidents',
                data: communeData,
                backgroundColor: 'rgba(255,127,0,0.7)',
                borderColor: 'rgba(255,127,0,1)',
                borderWidth: 1
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // Données pour le graphique Mois
    const monthLabels = {!! json_encode($accidents->map(fn($a)=> $a->created_at->format('m/Y'))->unique()) !!};
    const monthData = {!! json_encode($accidents->groupBy(fn($a)=> $a->created_at->format('m/Y'))->map->count()->values()) !!};

    new Chart(document.getElementById('monthChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Nombre d\'accidents',
                data: monthData,
                fill: true,
                backgroundColor: 'rgba(0,123,255,0.3)',
                borderColor: 'rgba(0,123,255,1)',
                tension: 0.4
            }]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection
