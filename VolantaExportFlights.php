<?php

namespace App\Widgets;

use App\Contracts\Widget;
use App\Models\Pirep;
use Illuminate\Support\Facades\Auth;

class VolantaExportFlights extends Widget
{
    protected $config = [
        'name' => 'Volanta Export Flights',
        'icon' => 'fas fa-download',
        'enabled' => true,
    ];

    protected $limit = 250;

    public function run()
    {
        $user = Auth::user();
        $flights = collect(); 
        $startDate = '';
        $endDate = '';

        $validated = request()->validate([
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'] ?? '';
        $endDate = $validated['end_date'] ?? '';

        if ($startDate && $endDate && $user) {
            $flights = $this->getFlights($user->id, $startDate, $endDate);
        }

        return view('widgets.volanta_export_flights', [
            'config' => $this->config,
            'user' => $user,
            'flights' => $flights,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search_performed' => !empty($startDate) && !empty($endDate),
            'limit_reached' => $flights->count() >= $this->limit,
            'limit' => $this->limit,
        ]);
    }

    private function getFlights($userId, $startDate, $endDate)
    {
        return Pirep::select(
            'pireps.dpt_airport_id as Origin',
            'pireps.arr_airport_id as Destination',
            'pireps.block_off_time as DepartureTime',
            'pireps.flight_time as Duration',
            'airlines.name as Airline',
            'pireps.flight_number as Callsign',
            'pireps.flight_number as FlightNumber',
            'aircraft.icao as AircraftType',
            'aircraft.registration as AircraftRegistration',
            'pireps.route as Route',
            'pireps.block_on_time as ArrivalTime',
            'pireps.distance as Distance',
            'pireps.fuel_used as Fuel'
        )
        ->join('aircraft', 'aircraft.id', '=', 'pireps.aircraft_id')
        ->join('airlines', 'airlines.id', '=', 'pireps.airline_id')
        ->where('pireps.user_id', $userId)
        ->whereBetween('pireps.block_off_time', ["$startDate 00:00:00", "$endDate 23:59:59"])
        ->orderBy('pireps.block_on_time', 'desc')
        ->limit($this->limit)
        ->get();
    }
}