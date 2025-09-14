# Installing the Volanta Export Widget

## 📦 Manual Installation

## Step 1: Copy widget files

### Copy these files to your phpVMS installation:
VolantaExportFlights.php
  → app/Widgets/VolantaExportFlights.php

volanta_export_flights.blade.php
  → resources/views/widgets/volanta_export_flights.blade.php
  
```
├── app/
│   └── Widgets/
│       └── VolantaExportFlights.php
└── resources/
    └── views/
        └── widgets/
            └── volanta_export_flights.blade.php
```
  
## Step 2: Integrate into the Profile view

### Add these lines to the file:

resources/views/layouts/your_theme_name/profile/index.blade.php

```
    {{-- Widget Volanta Export Flights - Solo visible para el propio usuario --}}
@if (Auth::check() && $user->id === Auth::user()->id)
    <div class="row mt-5">
        <div class="col-sm-12">
        @widget('VolantaExportFlights')
        </div>
    </div>
@endif
```

## Step 3: Clear cache

In administration panel->maintenance->Clear all caches

✅ Verification
Go to your profile (/profile)
You should see the ‘Volanta Export Flights’ widget
Select dates and test the CSV download
🗑️ Uninstallation
Delete the 2 copied files
Remove the lines added to the profile file
Clear cache
📋 CSV format generated
The CSV includes these columns for Volanta:
Origin, Destination, DepartureTime, Duration
Airline, Callsign, FlightNumber, AircraftType
AircraftRegistration, Route, ArrivalTime, Distance, Fuel
