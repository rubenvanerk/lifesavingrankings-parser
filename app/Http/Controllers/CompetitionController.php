<?php

namespace App\Http\Controllers;

use App\Competition;
use App\Country;
use App\Http\Requests\StoreCompetitionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(): View
    {
        $competitions = Competition::orderBy('id')->paginate(15);
        return view('competition.index', ['competitions' => $competitions]);
    }

    public function create(): View
    {
        $data = [
            'countries' => Country::all(),
        ];
        return view('competition.create', $data);
    }

    public function store(StoreCompetitionRequest $request): RedirectResponse
    {
        $competition = Competition::create($request->validated());
        $competition->competition_config->addMediaFromRequest('file')->toMediaCollection('results_file');
        return redirect('/');
    }

    public function edit(Competition $competition): View
    {
        return view('competition.edit', ['competition' => $competition, 'countries' => Country::all()]);
    }

    public function update(StoreCompetitionRequest $request, Competition $competition): RedirectResponse
    {
        $competition->fill($request->validated());
        $competition->save();

        if ($request->hasFile('file')) {
            $competition->competition_config->getMedia('results_file')->each->delete();
            $competition->competition_config->addMediaFromRequest('file')->toMediaCollection('results_file');
        }

        return redirect()->route('competitions.edit', ['competition' => $competition]);
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $competition->delete();
        return redirect()->route('competitions.index');
    }
}
