<?php

declare(strict_types=1);

namespace Edev\Resource\Display\Parser;

use Edev\System\Helpers\Arr;

class CommandParser
{

    protected $_reIf = '/@if\s?\((.*?)([<>==!]{1,2})(.*?)\)/';
    // protected $_reElse = '/@else/';
    protected $_reEndIf = '/@endif/';
    protected $_reForeach = '/@foreach\s?\((.*?)\)/';
    // protected $_reEndForeach = '/@endforeach/';

    protected $_downstreamContents;
    protected $_upstreamContents = [];
    protected $_data;

    public $parent;

    protected $_test;
    //
    public function __construct()
    {
        //
        // echo '<pre>';
        $this->_test = get_called_class();
    }

    public function parse(array $fileContents, array $displayData)
    {
        //
        $this->_downstreamContents = $fileContents;
        $this->_data = $displayData;

        $this->_loopOutputForNestedCommandStructures();
    }

    protected function _loopOutputForNestedCommandStructures()
    {

        // echo '<h2>AT THE START OF _loopOutputForNestedCommandStructures()</h2>';
        // print_r($this->_downstreamContents);
        // echo '<hr />';
        $len = count($this->_downstreamContents);

        // echo 'NEW ONE: <br />';
        // print_r($this->_downstreamContents);
        for ($ii = 0; $ii < $len; $ii++) {
            //
            $curr = $this->_downstreamContents[$ii];

            if ($this->_if($curr)) {
                // echo 'found an if';
                if ($this->_parseFoundIfStructure($ii)) {

                    $this->_loopOutputForNestedCommandStructures();
                    // die();
                    break;
                } else {
                    continue;
                };
                // echo '<hr />';
                // echo '<pre>';
                // print_r($cmdFrag);
                // echo '<br />DOWNSTREAM ==========================================================<br />';
                // print_r($upstream);
                // echo '<br />DOWNSTREAM ==========================================================<br />';
                // echo '<hr />';
            }

            if ($this->_foreach($curr)) {

                // echo "[ found a foreach: $curr ]";
                if ($this->_parseFoundForeachStructure($ii)) {

                    // echo 'parse is passed on foreach';
                    $this->_loopOutputForNestedCommandStructures();
                    // die();
                    break;
                } else {
                    // echo 'we-re continuing....';
                    continue;
                };
                // // echo " <h2> HEADING: curr:   $curr </h2>";
                // // print_r($this->_downstreamContents);
                // // $this->_loopOutputForNestedCommandStructures();
                // echo '------------------------------- forEachExtractor: <br />';
                // $this->_parseFoundForeachStructure($ii);

                // echo '------------------------------- forEachExtractor: <br />';
                // // die('end');
                // $this->_loopOutputForNestedCommandStructures();
                // break;
            }

            $this->_pushToUpstreamContainer([$curr]);
        }
    }

    public function output()
    {
        return $this->_upstreamContents;
    }
    protected function _parseFoundIfStructure(int $index): bool
    {

        // Arr::pre($this->_breakFragment($index));

        $extractor = $this->_createNewCmdExtractor(new IfExtractor($this->_breakFragment($index)));

        $parser = $this->_createNewCommandParser(new IfParser([]));

        return $this->_parseFoundCommandStructure($extractor, $parser);
    }
    protected function _parseFoundForeachStructure(int $index): bool
    {
        $extractor = $this->_createNewCmdExtractor(new ForeachExtractor($this->_breakFragment($index)));
        $parser = $this->_createNewCommandParser(new ForeachParser([]));

        return $this->_parseFoundCommandStructure($extractor, $parser);
    }
    protected function _parseFoundCommandStructure($extractor, $parser): bool
    {

        // fragment needs to be parsed and pushed to downstream
        $cmdFrag = $extractor->getCommandFragment();
        if (!empty($cmdFrag)) {
            // echo 'confused...';
            // print_r($cmdFrag);

            $parser->parse($cmdFrag, $this->_data);

            $parsedCommandOutput = $parser->output();
            // echo '<div style="background-color: lightblue"><pre>';
            // print_r($parsedCommandOutput);
            // echo '</pre></div>';

            // this waits until frag is parsed, then both are pushed to upstream, in order. Frag first, upstream last
            $downStream = $extractor->getUpstreamFragment();
            // echo '<div style="background-color: lightcoral"><pre>';
            // print_r($downStream);
            // echo '</pre></div>';
            //

            // echo '<span style="background-color:lightcoral">' . htmlentities(implode(" ", $commandFragment)) . '</span>';

            $this->_resetDownstreamContainer();
            $this->_pushToDownstreamContainer(array_merge($parsedCommandOutput, $downStream));

            return true;
        }

        return false;
    }
    private function _resetDownstreamContainer()
    {
        $this->_downstreamContents = [];
    }
    private function _pushToDownstreamContainer(array $arrayOfLines)
    {

        // echo '<h3>pushing to downstreamContainer: </h3>';
        // echo '<pre>';
        // print_r($arrayOfLines);

        $this->_downstreamContents = array_merge($this->_downstreamContents, $arrayOfLines);
    }
    private function _createNewCommandParser(\Edev\Resource\Display\Parser\CommandParser $commandParser)
    {
        // $commandParser->parent = $this;
        return $commandParser;
    }
    // private function _overrideDownStreamContents(array $newDownstreamContents): void
    // {
    //     $this->_downstreamContents = $newDownstreamContents;
    //     // echo '<span style="background-color:lightgreen">' . htmlentities(implode(" ", $newDownstreamContents)) . '</span>';

    // }
    private function _extract($index)
    {
    }
    private function _createNewCmdExtractor(\Edev\Resource\Display\Parser\CmdExtractor $cmdExtractor)
    {
        $cmdExtractor->parent = $this;
        return $cmdExtractor;
    }
    private function _pushToUpstreamContainer(array $arrayOfLines): void
    {
        $this->_upstreamContents = array_merge($this->_upstreamContents, $arrayOfLines);
    }
    private function _if(string $line): int
    {
        return preg_match($this->_reIf, $line, $m);
    }

    private function _foreach(string $line): int
    {
        return preg_match($this->_reForeach, $line, $m);
    }

    private function _breakFragment(int $index): array
    {
        // echo "<pre>[ index: $index ] <br />";
        // print_r($this->_downstreamContents);
        // echo '<hr /><hr /><hr />';
        return array_slice($this->_downstreamContents, $index);
    }
    protected function debug(string $msg = ''): void
    {
        echo "<hr />";
        echo "<p>DEBUG =======================================================||</p>";
        echo "<h2>message: $msg</h2>";
        echo "<p>" . get_called_class() . "</p>";
        echo "<pre>";
        print_r($this);
        echo "</pre>";
        echo "<hr />";
    }
}