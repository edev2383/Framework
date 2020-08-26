<?php

namespace Ifs;

interface CommandParserInterface
{

    /**
     *  takes the inital string input and preg_matches to break down into
     *  three separate components:
     *  1.) _command: if/for
     *  2.) _evaluation: the eval statement, ie (x > 5)
     *  3.) _effect: anything actionable between the brackets
     */
    public function parseComponents($string);


    /**
     *  the effect is currently restricted to relatively simple echo statements 
     *  and template replacement
     *  more planned for future
     */
    public function parseEffect($effect);

    public function parseEval($evaluation);

}