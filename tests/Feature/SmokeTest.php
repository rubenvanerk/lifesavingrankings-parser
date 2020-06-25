<?php

namespace Tests\Feature;

use App\Services\Parsers\Parser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Storage;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRescueSoft()
    {
        $this->seed();

        Storage::put('test.pdf', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../competitions/2018-worlds-nat-open.pdf'));
        Storage::put('test.pdf.yaml', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '../competitions/2018-worlds-nat-open.pdf.yaml'));
        $parser = Parser::getInstance('test.pdf');

        $parsedCompetition = $parser->getParsedCompetition();

        $this->assertCount(
            842,
            $parsedCompetition->results
        );
    }
}
