<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="{{ $config['icon'] }}"></i>
            {{ $config['name'] }}
        </h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Export your flights to use with Volanta. Select the date range and click "Search flights" to load your flight data.
        </div>

        <!-- Date selection form -->
        <form method="GET" action="{{ url()->current() }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_date">Start date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="{{ $start_date }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_date">End date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="{{ $end_date }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">
                            <i class="fas fa-search"></i> Search flights
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @if($search_performed)
            @if($flights->count() > 0)
                <div class="mb-3">
                    <h5>Flights found: {{ $flights->count() }}</h5>
                    <p class="text-muted">Period: {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</p>
                    <button type="button" class="btn btn-success" id="download-csv">
                        <i class="fas fa-download"></i> Download CSV for Volanta
                    </button>
                </div>

                @if(isset($limit_reached) && $limit_reached)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>                        
                        Only the first {{ $limit }} flights have been displayed. There may be more results in the selected range.
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Origin</th>
                                <th>Destination</th>
                                <th>Departure</th>
                                <th>Arrival</th>
                                <th>Airline</th>
                                <th>Flight</th>
                                <th>Aircraft</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($flights as $flight)
                            <tr>
                                <td>{{ $flight->Origin ?? '' }}</td>
                                <td>{{ $flight->Destination ?? '' }}</td>
                                <td>{{ $flight->DepartureTime ?? '' }}</td>
                                <td>{{ $flight->ArrivalTime ?? '' }}</td>
                                <td>{{ $flight->Airline ?? '' }}</td>
                                <td>{{ $flight->FlightNumber ?? '' }}</td>
                                <td>{{ $flight->AircraftType ?? '' }} ({{ $flight->AircraftRegistration ?? '' }})</td>
                                <td>{{ $flight->Distance ?? '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No flights found for the selected period. Try with a different date range.
                </div>
            @endif
        @else
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('download-csv');

    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            // Create CSV with flight data
            const flights = @json($flights);

            let csv = 'Origin,Destination,DepartureTime,Duration,Airline,Callsign,FlightNumber,AircraftType,AircraftRegistration,Route,ArrivalTime,Distance,Fuel\n';

            flights.forEach(flight => {
                csv += `"${flight.Origin || ''}","${flight.Destination || ''}","${flight.DepartureTime || ''}","${flight.Duration || ''}","${flight.Airline || ''}","${flight.Callsign || ''}","${flight.FlightNumber || ''}","${flight.AircraftType || ''}","${flight.AircraftRegistration || ''}","${flight.Route || ''}","${flight.ArrivalTime || ''}","${flight.Distance || ''}","${flight.Fuel || ''}"` + '\n';
            });

            // Download CSV file
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = 'volanta_flights_{{ $user->pilot_id }}_' + new Date().toISOString().slice(0, 10) + '.csv';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });
    }
});
</script>
