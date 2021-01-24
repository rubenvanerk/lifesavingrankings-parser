<?php

namespace App\Http\Controllers;

use App\CompetitionConfig;
use App\Country;
use Exception;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * @param Request $request
     *
     * @throws Exception
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function upload(Request $request)
    {
        if ($request->method() === 'GET') {
            $data = [
                'countries' => Country::all(),
            ];
            return view('upload', $data);
        }

//        $request->validate([
//            'name' => 'required',
//            'city' => 'required',
//            'country' => 'required',
//            'start_date' => 'required',
//            'end_date' => 'required',
//            'timekeeping' => 'required',
//        ]);

        $competition = CompetitionConfig::create($request->all());


        $competition->addMediaFromRequest('file')->toMediaCollection('results_file');

        return redirect('/');
    }
}
