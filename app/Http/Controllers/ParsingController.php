<?php

namespace App\Http\Controllers;

use App\Competition;
use App\Services\Parsers\Parser;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ParseError;

class ParsingController extends Controller
{
    public function parse(Request $request, Competition $competition): RedirectResponse | View
    {
        $competitionConfig = $competition->competition_config;

        if ($request->method() === 'PUT') {
            $this->saveConfigFromRequest($request, $competition);
            $action = $request->input('action');
            return match ($action) {
                'dry_run' => redirect()->route('competitions.dry_run', ['competition' => $competition]),
                'save_to_database' => redirect()->route('save_database', ['competition' => $competition, 'connection' => $action]),
                default => redirect()->route('competitions.parse', ['competition' => $competition]),
            };
        }

        $competitionParser = Parser::getInstance($competitionConfig);

        try {
            $rawData = $competitionParser->getRawData();
        } catch (Exception $exception) {
            $rawData = $exception->getMessage();
        }

        $data = [
            'file' => '',
            'competition' => $competition,
            'competitionConfig' => $competitionConfig,
            'rawData' => $rawData,
            'rawDataText' => $competitionParser->getRawData(true),
            'fileExtension' => $competitionParser->getFileExtension(),
            'config' => $competitionParser->config,
            'databases' => config('database.connections'),
        ];

        return view('competition.parse', $data);
    }

    private function saveConfigFromRequest(Request $request, Competition $competition): void
    {
        $competitionConfig = $competition->competition_config;
        $competitionParser = Parser::getInstance($competitionConfig);
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

    public function dryRun(Competition $competition): View
    {
        $competitionConfig = $competition->competition_config;
        $competitionParser = Parser::getInstance($competitionConfig);
        try {
            $parsedCompetition = $competitionParser->getParsedCompetition();
        } catch (ParseError $error) {
            return view('error', ['error' => $error->getMessage(), 'competition' => $competition]);
        }
        return view('dry_run', ['parsedCompetition' => $parsedCompetition, 'competition' => $competition]);
    }

    public function saveToDatabase(Competition $competition): View
    {
        $competitionParser = Parser::getInstance($competition->competition_config);
        $parsedCompetition = $competitionParser->getParsedCompetition();
        DB::transaction(function () use ($parsedCompetition) {
            $parsedCompetition->saveToDatabase();
        });
        return view('save_to_database', ['parsedCompetition' => $parsedCompetition, 'competition' => $competition]);
    }
}
