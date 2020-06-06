<?php

namespace App\Http\Controllers;

use App\Services\Parsers\Parser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function browse($path = null)
    {
        $breadcrumbs = [];
        $breadcrumbs[] = [
            'path' => '',
            'name' => 'root'
        ];
        $previousBreadcrumb = '';
        foreach (explode('/', $path) as $directory) {
            $breadcrumbs[] = [
                'path' => $previousBreadcrumb .= $directory . '/',
                'name' => $directory
            ];
        }

        $files = Storage::files($path);
        $filesWithoutYaml = preg_grep('/^.*(?<!\.yaml)$/', $files);

        $data = [
            'directories' => Storage::directories($path),
            'files' => $filesWithoutYaml,
            'path' => $path,
            'breadcrumbs' => $breadcrumbs
        ];

        return view('browse', $data);
    }

    public function upload(Request $request)
    {
        $fileName = $request->input('filename');
        $file = $request->file('results');
        $date = new Carbon($request->input('date'));
        $path = $date->year . DIRECTORY_SEPARATOR . $date->month;
        $file->storeAs($path, $fileName);

        $competitionParser = Parser::getInstance($path . DIRECTORY_SEPARATOR . $fileName);
        $config = $competitionParser->config;
        $config->{'info.date'} = $request->input('date');
        $config->save();

        return redirect()->route('config', ['file' => $path . '/' . $fileName]);
    }

    public function config($file)
    {
        $competitionParser = Parser::getInstance($file);

        // s3 url
        // Storage::temporaryUrl($file, Carbon::now()->addMinutes(5));
        $data = [
            'file' => $file,
            'temporaryUrl' => Storage::url($file),
            'rawData' => $competitionParser->getRawData(),
            'config' => $competitionParser->config
        ];

        return view('config', $data);
    }

    public function saveConfig(Request $request, $file)
    {
        $this->saveConfigFromRequest($request, $file);
        switch ($request->input('action')) {
            case 'dry_run':
                return redirect()->route('dry_run', ['file' => $file]);
            case 'save_database':
                return redirect()->route('save_database', ['file' => $file]);
            default:
                return redirect()->route('config', ['file' => $file]);
        }
    }

    public function dryRun($file)
    {
        $competitionParser = Parser::getInstance($file);
        $parsedCompetition = $competitionParser->getParsedCompetition();
        return view('dry_run', ['competition' => $parsedCompetition, 'file' => $file]);
    }

    public function saveToDatabase($file)
    {
        $competitionParser = Parser::getInstance($file);
        $parsedCompetition = $competitionParser->getParsedCompetition();
        $parsedCompetition->saveToDatabase();
        return view('save_to_database', ['competition' => $parsedCompetition, 'file' => $file]);
    }


    private function saveConfigFromRequest($request, $file): void
    {
        $competitionParser = Parser::getInstance($file);
        $config = $competitionParser->config;

        foreach ($request->all()['data'] as $name => $value) {
            if (Str::endsWith($name, '_custom')) {
                continue;
            }
            if ($value === 'custom') {
                $value = $request->input('data')[$name . '_custom'];
            }
            $config->{$name} = $value;
        }

        $config->save();
    }
}
