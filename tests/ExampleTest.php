<?php

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRescueSoft()
    {
        $parser = App\Services\Parsers\Parser::getInstance(__DIR__ . DIRECTORY_SEPARATOR . 'competitions/2018-worlds-nat-open.pdf');

        $parsedCompetition = $parser->getParsedCompetition();

        $this->assertEquals(
            791, $parsedCompetition->resultCount
        );
    }
}
