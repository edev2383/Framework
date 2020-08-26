<?php

namespace Edev\Resource\Display\Parser;

class CmdExtractor
{

    protected $_reStart = '';
    protected $_reEnd = '';

    public $parent;

    public function _construct(array $downstreamContents)
    {
        $this->_returnObject = [
            'commandFragment' => [],
            'upstreamContents' => [],
        ];
        $this->_test = get_called_class();
        $this->_downstreamContents = $downstreamContents;
        $this->_loop();
    }

    private function _loop()
    {
        $nodeDepth = 0;
        $len = count($this->_downstreamContents);
        for ($ii = 0; $ii < $len; $ii++) {
            //
            $curr = $this->_downstreamContents[$ii];
            // echo '[ curr: ' . $curr . ' : ' . $this->_test . ' ]<hr />';
            if ($this->_compareRegex($curr, $this->_reStart)) {
                if ($ii != 0) {
                    $nodeDepth++;
                }
            }

            if ($this->_compareRegex($curr, $this->_reEnd)) {
                if ($nodeDepth > 0) {
                    $nodeDepth--;
                } else {
                    $this->_breakDownstreamContentsIntoFragments($ii);
                    break;
                }
            }
        }

        // $this->_debug('CmdExtractor _loop()');
    }

    private function _debug(string $msg = ''): void
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

    private function _breakDownstreamContentsIntoFragments(int $index)
    {

        $this->_returnObject['commandFragment'] = $this->_extractCommandFragment($index);
        $this->_returnObject['upstreamContents'] = $this->_extractUpstreamContents($index);
    }
    public function getCommandFragment(): array
    {
        return $this->_returnObject['commandFragment'];
    }
    public function getUpstreamFragment(): array
    {
        return $this->_returnObject['upstreamContents'];
    }

    public function output(): array
    {
        return $this->_returnObject;
    }
    private function _extractCommandFragment(int $index): array
    {

        return array_slice($this->_downstreamContents, 0, $index + 1);
    }
    private function _extractUpstreamContents(int $index): array
    {
        return array_slice($this->_downstreamContents, $index + 1);
    }
    private function _compareRegex(string $line, string $regExp)
    {
        return preg_match($regExp, $line, $m);
    }
}

class IfExtractor extends CmdExtractor
{
    protected $_reStart = '/@if\s?\((.*?)([<>==!]{1,2})(.*?)\)/';
    protected $_reEnd = '/@endif/';

    public function __construct(array $downstreamContents)
    {
        parent::_construct($downstreamContents);
    }
}

class ForeachExtractor extends CmdExtractor
{
    protected $_reStart = '/@foreach\s?\((.*?)\)/';
    protected $_reEnd = '/@endforeach/';
    public function __construct(array $downstreamContents)
    {
        $this->parent = '';
        parent::_construct($downstreamContents);
    }
}