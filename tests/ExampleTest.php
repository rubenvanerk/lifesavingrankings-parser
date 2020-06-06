<?php

class ExampleTest extends TestCase
{
    public function testRescueSoft()
    {
        $parser = App\Services\Parsers\Parser::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'competitions/2018-worlds-nat-open.pdf');

        $parsedCompetition = $parser->getParsedCompetition();

        $this->assertCount(
            842, $parsedCompetition->results
        );
    }
}
