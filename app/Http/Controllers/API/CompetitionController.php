<?php

namespace App\Http\Controllers\API;

use App\Competition;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{

    /**
     * @OA\Info(title="Vault API", version="0.1")
     */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Competition::paginate(25)->values());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Competition  $competition
     * @return \Illuminate\Http\Response
     */
    public function show(Competition $competition)
    {
        return response()->json($competition);
    }
}
