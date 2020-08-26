<?php

namespace Edev\Resource;

use Ifs\DisplayInterface as DisplayInterface;

class Display implements DisplayInterface
{

    private $_cmd = [];

    /**
     * Renders views.
     * @param string filePath - the path to the view including file name and extension.
     * @param array data - data to be processed during the render.
     */
    public function __construct($filePath, $data)
    {

        $this->_displayData = $data;
        $this->_fileContents = $this->_readContents($filePath);

        $this->_replaceTemplateComponents();

        $this->_searchForCMD();

        $this->_outputString = $this->_templateString($this->_fileContents, $this->_displayData);

        $this->_paint();
    }

    private function _replaceTemplateComponents()
    {
        $newInc = new IncludeParser($this->_fileContents);
        $pathToRoot = $_SERVER['DOCUMENT_ROOT'] . '/View/template/';

        if (!empty($newInc->string_to_replace)) {
            $filePath = $pathToRoot . $newInc->filename . '.html';
            $templateContents = file_exists($filePath) ? file_get_contents($filePath) : null;

            if ($templateContents != null) {

                $this->_fileContents = str_replace($newInc->string_to_replace, $templateContents, $this->_fileContents);
                $recurs = $this->_replaceTemplateComponents($this->_fileContents);

                if ($recurs != null) {
                    return $recurs;
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    private function _readContents($page)
    {
        $file = $page;
        if (file_exists($file)) {

            $this->_fileContents = file_get_contents($file);

            return $this->_fileContents;
        } else {

            echo 'File not found...<br />';
            echo 'File name: ' . $page;
        }
    }

    private function _searchForCMD()
    {

        $cmd = new CommandParser($this->_fileContents, $this->_displayData);

        $this->_cmd = $cmd->output();

        if ($cmd->_command == 'foreach') {
            $this->_cmdTemplate($this->_fileContents, $this->_displayData);
        } else if ($cmd->_command == 'if') {
            $this->_checkForIfParser($this->_fileContents, $this->_displayData);
        }
    }

    private function _cmdTemplate($string, $data)
    {
        // echo '<pre>';
        // echo $string;
        // echo '============================================<br /><br /><br />';
        // regEx to find template items {item} format
        $primaryRegex = '/\{(.+?)\}/m';

        $cmdReplaceArray = [];

        // get all matches from file content string
        preg_match_all($primaryRegex, $string, $matches);
        // echo 'testing ---------------------------- <br /> <pre>';
        // print_r($matches);

        // alias to second matches array (i.e., without the brackets)
        $matches = $matches[1];

        // echo 'tmpArrHolder: <br />';
        // print_r($tmpArrHolder);
        // // echo 'end';

        // if matches is not empty...
        if (!empty($matches)) {

            // $iterations = sizeof($matches);

            // for ($ii = 0; $ii < $iterations; $ii++) {

            // $array = $this->_cmd['array'];
            // echo '------------------------------------------------------------((((<br />';
            // echo $array;

            // set array name
            $array = $this->_cmd['array'];

            // search the sent data for the array and set to newArray var
            $newArray = $this->_iterateSearch($array, $this->_displayData);
            // echo '------------------------------------------------------------((((<br />';
            // echo $newArray;
            // echo '<pre>';
            // print_r($newArray);

            // loop through the newArray
            foreach ($newArray as $value) {
                // foreach ($data[$array] as $value) {

                $holderString = $this->_cmd['output'];

                $holderString = $this->_checkForIfParser($holderString, $value);

                // given this is a foreach loop, $value MUST be an array.
                // TODO - we could implement a check for an array at this point to bail if not.
                foreach ($value as $k => $v) {

                    // check for presence of '||' in $k to evaluate $v's value. If $v = false/null/undefined, overrwite w/ value to right of ||
                    // example: {total_time||0} would insert 0 into the $v value
                    // There's more to it than my original thought. I'd need additional

                    // $v can be an array or not, so if it's an array, loop through
                    if (is_array($v)) {

                        // loop matches for key/val pairs
                        foreach ($matches as $key => $val) {

                            // echo $v . ' => ' . $k . ' :: ' . $val;
                            // echo '<br />';

                            // if($k != '') {
                            if (strlen($k) == 0) {
                                $k = 'nullValue';
                            }

                            // if k is within $val, i.e., $k=test, $val=test:foo [ {test:foo} ]
                            if (strpos($val, $k) !== false) {

                                // subVal is secondary value, ex. 'foo' from above
                                $subVal = explode(':', trim(trim($val, '}'), '{'))[1];
                                // define holderSTring as the replacement
                                $holderString = preg_replace('/\{' . $val . '\}/', $value[$k][$subVal], $holderString);
                            }
                            // }

                        }
                    } else {

                        // make the replacement
                        $holderString = preg_replace('/\{' . $k . '\}/', $v, $holderString);
                    }
                }

                // push to cmdReplaceArray container
                $cmdReplaceArray[] = $holderString;
            }
            // echo '<pre>';
            // echo 'the array<br />';
            // print_r($cmdReplaceArray);

            // define the string to inject as imploding the above created container
            $injectString = implode(' ', $cmdReplaceArray);

            // replace the 'replace' string w/ the injectString
            $this->_fileContents = str_replace($this->_cmd['replace'], $injectString, $string);

            // preg_match($primaryRegex, $this->_fileContents, $matches);
            // return $this->cmdTemplate($this->_fileContents, $this->_displayData);
            // print_r($matches);
            // echo sizeof($matches);
            $recurs = $this->_searchForCMD();

            if ($recurs != null) {

                return $recurs;
            }
        } else {
            return null;
        }
    }

    private function _checkForIfParser($string, $data)
    {
        // echo '<pre>';
        $newCMD = new CommandParser($string, $data);

        if ($newCMD->_command == 'if') {
            // echo '<pre>';
            // print_r($newCMD);

            $test = $newCMD->output();
            // print_r($test);

            $string = str_replace($test['replace'], $test['output'], $string);

            $this->_fileContents = $string;

            $recurs = $this->_searchForCMD();

            if ($recurs != $string) {

                return $recurs;
            }
        } else {

            return $string;
        }
    }

    private function _templateString($string, $data)
    {
        if (!empty($data)) {

            // regEx to find template items {item} format
            $re = '/\{(.+?)\}/m';

            // check for matches
            if (preg_match_all($re, $string, $m)) {

                // loop through matches ...
                foreach ($m[1] as $k => $v) {

                    // ... and replace if the template has matching key
                    // if (!$data[$v]) {
                    //     $data[$v] = " ";
                    // }

                    // if (strpos($v, ':') != false) {

                    //     $vals = explode(':', $v);
                    //     $arrayName = $vals[0];
                    //     $valueName = $vals[1];
                    //     $array = $this->_iterateSearch($arrayName, $this->_displayData);
                    //     $string = preg_replace('/\{' . $v . '\}/', $array[$valueName], $string);

                    // } else {

                    if (isset($data[$v])) {

                        if (!is_array($data[$v])) {

                            // if (strpos($data))
                            // $newArray = $this->_iterateSearch($array, $this->_displayData);

                            $string = preg_replace('/\{' . $v . '\}/', $data[$v], $string);
                        }
                    }
                    // }
                }
                // return the string
            }
        }
        // echo $string ;
        // echo 'testing here too';
        return $string;
    }

    private function _iterateSearch($needle, $haystack)
    {
        // check for value and return if set break recursion
        if (isset($haystack[$needle])) {

            return $haystack[$needle];
        } else {

            // get the values of the array
            $values = array_values($haystack);

            // loop through  and check if the values are arrays
            foreach ($values as $k => $v) {

                if (is_array($v)) {

                    // assign the variable path to ensure the loop doesn't overwrite
                    // and falsely return false/null
                    $recurs = $this->_iterateSearch($needle, $v);

                    // checks each recursive path and if it returns something, return it
                    if ($recurs !== null) {

                        return $recurs;
                    }
                }
            }
        }
    }

    private function _paint()
    {
        echo $this->_outputString;
    }
}