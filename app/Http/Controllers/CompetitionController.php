<?php

namespace App\Http\Controllers;

use App\CompetitionConfig;
use App\Services\Parsers\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $competitions = CompetitionConfig::paginate(15);
        return view('competition.index', ['competitions' => $competitions]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        CompetitionConfig::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CompetitionConfig $competition
     * @return \Illuminate\Http\Response
     */
    public function show(CompetitionConfig $competition)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CompetitionConfig $competition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|View
     */
    public function edit(CompetitionConfig $competition)
    {
        return view('competition.edit', ['competition' => $competition]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CompetitionConfig $competition
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompetitionConfig $competition)
    {
        $competition->fill($request->all());
        $competition->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CompetitionConfig $competition
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompetitionConfig $competition)
    {
        $competition->delete();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param \App\CompetitionConfig $competition
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|View
     */
    public function parse(Request $request, CompetitionConfig $competition)
    {
        if ($request->method() === 'PUT') {
            $this->saveConfigFromRequest($request, $competition);
            $action = $request->input('action');
            switch ($action) {
                case 'dry_run':
                    return redirect()->route('competitions.dry_run', ['competition' => $competition]);
                case 'save_to_database':
                    return redirect()->route('save_database', ['competition' => $competition, 'connection' => $action]);
                default:
                    return redirect()->route('competitions.parse', ['competition' => $competition]);
            }
        }

        $competitionParser = Parser::getInstance($competition);

        try {
            $rawData = $competitionParser->getRawData();
        } catch (\Exception $exception) {
            $rawData = $exception->getMessage();
        }

        $data = [
            'file' => '',
            'competition' => $competition,
            'rawData' => $rawData,
            'config' => $competitionParser->config,
            'databases' => config('database.connections'),
        ];

        return view('competition.parse', $data);
    }

    private function saveConfigFromRequest(Request $request, CompetitionConfig $competition): void
    {
        $competitionParser = Parser::getInstance($competition);
        $config = $competitionParser->config;

        foreach ($request->all()['data'] as $name => $value) {
            if (Str::endsWith($name, '_custom')) {
                continue;
            }
            if ($value === 'custom') {
                $value = $request->input('data')[$name . '_custom'];
            }
            $config->{$name} = (string)$value;
        }

        $config->save();
    }

    public function dryRun(CompetitionConfig $competition): View
    {
        $competitionParser = Parser::getInstance($competition);
        $parsedCompetition = $competitionParser->getParsedCompetition();
        return view('dry_run', ['parsedCompetition' => $parsedCompetition, 'competition' => $competition]);
    }


    public function saveToDatabase(CompetitionConfig $competition): View
    {
        $competitionParser = Parser::getInstance($competition);
        $parsedCompetition = $competitionParser->getParsedCompetition();
        DB::transaction(function () use ($parsedCompetition) {
            $parsedCompetition->saveToDatabase();
        });
        return view('save_to_database', ['parsedCompetition' => $parsedCompetition, 'competition' => $competition]);
    }

}
