<?php

namespace App\Libraries;

interface IXMLParser
{
    /**
     * Tries to parse XML file from a given path.
     * @param  string  $path
     * @return boolean
     */
    public function loadFile($path);

    public function getParsedData();
}
