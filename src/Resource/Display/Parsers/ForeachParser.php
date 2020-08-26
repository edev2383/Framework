<?php

declare(strict_types=1);

namespace Edev\Resource\Display\Parser;

use \Edev\Resource\Display\Template\Templater;

class ForeachParser extends CommandParser
{

    private $_outputContainer = [];
    private $_templateContainer = [];
    public function __construct()
    {
        // echo '<h2> BANANAS </h2>';
        // print_r($this);
    }

    public function parse(array $downstreamContents, array $displayData)
    {
        $this->Templater = $this->_registerTemplater(new Templater());
        $this->_downstreamContents = $downstreamContents;
        $this->_incomingData = $displayData;

        $this->_breakIntoComponents();
        $this->_findValue();

        $this->_removeCommandTextFromDownstreamContents();
        $this->_loopForTemplateContainer();

        $this->_loopForValues();
    }

    private function _loopForValues()
    {
        $templateString = implode("\n", $this->_templateContainer);
        echo "<!-- [ templateString: " . htmlentities($templateString) . " ] -->";
        if (!is_null($this->_foundData) || !empty($this->_foundData)) {

            foreach ($this->_foundData as $k => $v) {

                $templateStringWithValues = $this->Templater::parse($templateString, $v);
                $this->_pushValuesToOutputContainer(explode("\n", $templateStringWithValues));
            }

            $this->_upstreamContents = $this->_outputContainer;
        } else {
            echo '<!-- empty _foundData object. Foreach() skipped [array: ' . $this->_array . '] -->';
        }
    }

    private function _pushValuesToOutputContainer(array $arrayOfLines)
    {
        $this->_outputContainer = array_merge($this->_outputContainer, $arrayOfLines);
    }
    private function _breakIntoComponents(): void
    {
        $commandLine = current($this->_downstreamContents);
        if (preg_match($this->_reForeach, $commandLine, $match)) {
            $this->_command = trim($match[0]);
            $this->_array = trim($match[1]);
            $this->_close = '@endforeach';
        }
    }

    private function _loopForTemplateContainer()
    {
        $len = count($this->_downstreamContents);
        for ($ii = 0; $ii < $len; $ii++) {
            $curr = $this->_downstreamContents[$ii];
            $this->_templateContainer[] = $curr;
        }
    }
    private function _findValue(): void
    {
        $this->_foundData = $this->_incomingData[$this->_array];
    }
    private function _removeCommandTextFromDownstreamContents()
    {
        $firstLine = $this->_downstreamContents[0];
        $lastKey = count($this->_downstreamContents) - 1;
        $this->_downstreamContents[0] = str_replace($this->_command, '', $firstLine);
        $lastLine = $this->_downstreamContents[$lastKey];
        $this->_downstreamContents[$lastKey] = str_replace($this->_close, '', $lastLine);
    }
    private function _registerTemplater(\Edev\Resource\Display\Template\Templater $templater)
    {
        return $templater;
    }
    public function getArrayName()
    {
        return $this->_array;
    }
}