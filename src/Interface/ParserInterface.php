<?php

namespace Ifs;

interface ParserInterface 
{
    /**
     *  Future functionality will allow for parsing more complex
     *  slugs that contain more standarized options and values
     */
    public function init();
    public function parseUri();
    public function parseSlug();
    // parse incoming URI and return variables
    public function returnUri();
    public function returnParse();
}