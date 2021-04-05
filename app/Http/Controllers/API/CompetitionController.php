<?php

namespace App\Http\Controllers\API;

use App\CompetitionConfig;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompetitionController extends Controller
{

    /**
     * @OA\Info(title="Vault API", version="0.1")
     */

    /**
     * @OA\Tag(
     *     name="competitions",
     *     description="Everything about competitions"
     * )
     */

    /**
     * @OA\Get(
     *   path="/api/competitions",
     *   summary="list competitions",
     *   tags={"competitions"},
     *   @OA\Response(
     *     response=200,
     *     description="A list with products"
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        return response()->json(CompetitionConfig::paginate(25)->all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Get(
     *     path="/api/competitions/{competitionId}",
     *     tags={"competitions"},
     *     summary="Find a competition by ID",
     *     description="Returns a single competition",
     *     operationId="getCompetitionById",
     *     @OA\Parameter(
     *         name="competitionId",
     *         in="path",
     *         description="ID of competition to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pet not found"
     *     )
     * )
     *
     * @param CompetitionConfig $competition
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CompetitionConfig $competition)
    {
        return response()->json($competition);
    }
}
