<?php

class ExampleTest extends TestCase
{
    public function testRescueSoft()
    {
        Storage::put('test.pdf', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'competitions/2018-worlds-nat-open.pdf'));
        Storage::put('test.pdf.yaml', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'competitions/2018-worlds-nat-open.pdf.yaml'));
        $parser = App\Services\Parsers\Parser::getInstance('test.pdf');

        $parsedCompetition = $parser->getParsedCompetition();

        $this->assertCount(
            842,
            $parsedCompetition->results
        );
    }
}
