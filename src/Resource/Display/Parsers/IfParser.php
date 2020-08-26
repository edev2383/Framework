<?php

declare(strict_types=1);

namespace Edev\Resource\Display\Parser;

use Edev\System\Helpers\Arr;

class IfParser extends CommandParser
{

    private $_reElse = '/@else/';

    private $_var;
    private $_eval;
    private $_value;

    private $_trueContainer = [];
    private $_falseContainer = [];
    private $_inlineWrapper = [];

    private $_isInline = false;

    private $_evalutationKeys = ['==' => '_equalTo', '!=' => '_notEqualTo', '<=' => '_lessThanOrEqualTo', '>=' => '_greaterThanOrEqualTo', '>' => '_greaterThan', '<' => '_lessThan'];

    public $parent;

    public function __construct()
    {
        //
        // $this->_data['loggedIn'] = 0;
    }
    private function _removeCommandTextFromDownstreamContents()
    {
        $count = count($this->_downstreamContents);

        // Arr::pre($this->_downstreamContents);
        if ($count == 1) {
            // single line if
            $this->_isInline = true;
            $this->_processInlineCommand();
        } else {
            $this->_processMultilineCommand();
        }
    }

    private function _processInlineCommand()
    {
        $this->_breakInlineContent();
    }

    private function _breakInlineContent()
    {
        $this->_createContainerWrap();
    }

    private function _createContainerWrap()
    {
        $this->_createInlineWrapperStart();
        $this->_createInlineWrapperEnd();
        $this->_resetDownstreamContents();
    }

    private function _resetDownstreamContents()
    {
        $inline = current($this->_downstreamContents);
        $tmp = explode($this->_command, $inline);
        $this->_downstreamContents = [trim(current(explode($this->_close, end($tmp))))];
    }
    private function _createInlineWrapperEnd()
    {
        $inline = current($this->_downstreamContents);
        $tmp = explode($this->_command, $inline);
        $this->_inlineWrapper['end'] = trim(end(explode($this->_close, end($tmp))));
    }

    private function _createInlineWrapperStart()
    {
        $inline = current($this->_downstreamContents);
        $tmp = explode($this->_command, $inline);
        $this->_inlineWrapper['start'] = trim(current($tmp));
    }

    private function _processMultilineCommand()
    {
        $firstLine = $this->_downstreamContents[0];
        $lastKey = count($this->_downstreamContents) - 1;
        $this->_downstreamContents[0] = str_replace($this->_command, '', $firstLine);
        $lastLine = $this->_downstreamContents[$lastKey];
        $this->_downstreamContents[$lastKey] = str_replace($this->_close, '', $lastLine);
    }

    private function _loopForOutputContainers()
    {
        $len = count($this->_downstreamContents);
        $falseFlag = false;
        for ($ii = 0; $ii < $len; $ii++) {
            $curr = $this->_downstreamContents[$ii];

            // echo "[ currentLine: $curr ] <hr />";
            if (preg_match($this->_reElse, $curr, $m)) {
                $falseFlag = true;
                $curr = str_replace('@else', '', $curr);
            }

            if (!$falseFlag) {
                $this->_trueContainer[] = $curr;
            } else {
                $this->_falseContainer[] = $curr;
            }
        }
    }
    private function _evaluateForOutput()
    {

        if ($this->_isInline) {
            $this->_evaluateInline();
        } else {
            $this->_evaluateMultiline();
        }
    }

    private function _evaluateInline()
    {
        ///
        extract($this->_inlineWrapper);
        $output = $this->_evaluate() ? current($this->_trueContainer) : current($this->_falseContainer);
        $this->_downstreamContents = ["$start $output $end"];
    }

    private function _evaluateMultiline()
    {
        $this->_downstreamContents = $this->_evaluate() ? $this->_trueContainer : $this->_falseContainer;
    }

    public function parse(array $downstreamContents, array $displayData)
    {
        $this->_downstreamContents = $downstreamContents;
        $this->_data = $displayData;

        $this->_breakIntoComponents();
        $this->_findValue();

        $this->_removeCommandTextFromDownstreamContents();
        $this->_loopForOutputContainers();

        $this->_evaluateForOutput();

        $this->_loopOutputForNestedCommandStructures();


        if (isset($_GET['display_debug'])) {
            Arr::pre($this);
        }
        // echo '<pre>';
        // print_r($this);
        // echo '<hr />';

    }

    // private function _loopOutputForNestedCommandStructures() {

    // }
    // =========================================================================== \\
    // EVALUATION ================================================================ \\
    // =========================================================================== \\
    private function _breakIntoComponents(): void
    {
        $commandLine = current($this->_downstreamContents);
        if (preg_match($this->_reIf, $commandLine, $match)) {
            // print_r($match);
            $this->_command = trim($match[0]);
            $this->_var = trim($match[1]);
            $this->_eval = trim($match[2]);
            $this->_value = str_replace('"', '', trim($match[3]));
            $this->_close = '@endif';
        }
        $this->_checkValueForNull();
    }

    private function _checkValueForNull()
    {
        if ($this->_value == 'null') {
            $this->_value = null;
        }
    }

    private function _findValue()
    {
        $this->_foundValue = $this->_getValueInArray();
    }
    /**
     * Returns the evalution of the if statement
     *
     * @return boolean
     */
    private function _evaluate()
    {
        // get method name from evaluationKeys array, based on provided _eval
        $method = $this->_evalutationKeys[$this->_eval];

        // if the method exists within this class
        if (method_exists($this, $method)) {
            // return the method
            return $this->{$method}();
        }
    }

    /**
     *
     * @return boolean
     */
    private function _equalTo()
    {

        if ($this->_foundValue == $this->_value) {

            return true;
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    private function _greaterThan()
    {
        return $this->_foundValue > $this->_value;
    }

    /**
     *
     * @return boolean
     */
    private function _lessThan()
    {

        if ($this->_foundValue < $this->_value) {

            return true;
        }

        return false;
    }

    /**
     *
     * @return boolean
     */
    private function _notEqualTo()
    {

        $fv = $this->_foundValue;
        $v = $this->_value;

        return $this->_foundValue != $this->_value;
    }

    /**
     *
     * @return boolean
     */
    private function _greaterThanOrEqualTo()
    {
        if ($this->_equalTo() || $this->_greaterThan()) {
            return true;
        }
        return false;
    }

    /**
     *
     * @return boolean
     */
    private function _lessThanOrEqualTo()
    {
        if ($this->_equalTo() || $this->_lessThan()) {
            return true;
        }
        return false;
    }

    /**
     * Get value from provided array
     *
     * @return mixed
     */
    private function _getValueInArray()
    {
        $returnValue = null;

        $var = $this->_var;

        try {

            if ($this->_variableIsArray()) {
                $arrayName = explode(':', $var)[0];
                $propName = explode(':', $var)[1];

                $returnValue = $this->_data[$arrayName][$propName];
            } else {

                $returnValue = $this->_data[$var];
            }

            if (is_array($returnValue) || is_object($returnValue)) {
                throw new \Exception('Error: _var value cannot be an array or object. Please see documentation.');
            }

            return $returnValue;
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    /**
     * Check if variable is a compound value, i.e., {array:prop}
     * @return boolean
     */
    private function _variableIsArray()
    {
        $var = $this->_var;
        if (strpos($var, ':') != false) {
            return true;
        }
        return false;
    }
}