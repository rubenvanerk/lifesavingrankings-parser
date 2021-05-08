<?php

namespace App\Http\Controllers;

use App\Competition;
use App\CompetitionConfig;
use App\Country;
use App\Http\Requests\StoreCompetitionRequest;
use App\Services\Parsers\Parser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ParseError;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $competitions = Competition::orderBy('id')->paginate(15);
        return view('competition.index', ['competitions' => $competitions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'countries' => Country::all(),
        ];
        return view('competition.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCompetitionRequest $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(StoreCompetitionRequest $request)
    {
        $competition = Competition::create($request->validated());
        $competition->competition_config->addMediaFromRequest('file')->toMediaCollection('results_file');
        return redirect('/');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Competition $competition
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function edit(Competition $competition)
    {
        return view('competition.edit', ['competition' => $competition, 'countries' => Country::all()]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CompetitionConfig $competition
     *
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCompetitionRequest $request, Competition $competition)
    {
        $competition->fill($request->validated());
        $competition->save();

        if ($request->hasFile('file')) {
            $competition->competition_config->getMedia('results_file')->each->delete();
            $competition->competition_config->addMediaFromRequest('file')->toMediaCollection('results_file');
        }

        return redirect()->route('competitions.edit', ['competition' => $competition]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Competition $competition
     *
     * @throws Exception
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Competition $competition)
    {
        $competition->delete();
        return redirect()->route('competitions.index');
    }
}
