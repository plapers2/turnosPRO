<?php

namespace App\Http\Controllers;

use App\Models\OpeningHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\OpeningHourRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OpeningHourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $openingHours = OpeningHour::withTrashed()->orderByRaw("
        FIELD(day_of_week,
        'monday','tuesday','wednesday','thursday','friday','saturday','sunday')
    ")->get()->groupBy('day_of_week');

        return view('opening-hour.index', compact('openingHours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $openingHour = new OpeningHour();

        return view('opening-hour.create', compact('openingHour'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OpeningHourRequest $request): RedirectResponse
    {
        OpeningHour::create($request->validated());

        return Redirect::route('opening-hours.index')
            ->with('success', 'Horario de atencion creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $openingHour = OpeningHour::find($id);

        return view('opening-hour.show', compact('openingHour'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $openingHour = OpeningHour::find($id);

        return view('opening-hour.edit', compact('openingHour'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OpeningHourRequest $request, OpeningHour $openingHour): RedirectResponse
    {
        $openingHour->update($request->validated());

        return Redirect::route('opening-hours.index')
            ->with('success', 'Actualizacion de horario de atencion exitosa');
    }

    public function destroy($id)
    {
        $hour = OpeningHour::findOrFail($id);
        $hour->delete();

        return response()->json(['success' => true]);
    }

    public function restore($id)
    {
        $hour = OpeningHour::withTrashed()->findOrFail($id);
        $hour->restore();

        return response()->json(['success' => true]);
    }
}
