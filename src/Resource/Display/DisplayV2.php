<?php

declare(strict_types=1);

namespace Edev\Resource\Display;

class DisplayVersionTwo
{

    private $_filePath;
    private $_displayData;
    private $_fileContents;

    private $LayoutTemplate;
    private $CommandParser;

    private $_viewPath = '/home/zerodock/public_html/';
    private $_layoutPath = '/home/zerodock/public_html/View/layout/';


    public function __construct()
    {
        $this->_setViewPathForDev();
    }

    public function _setViewPathForDev()
    {
        $this->_viewPath = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/';
    }

    public function render()
    {
        // define fileContent value
        $this->_fileContents = $this->_locateAndJoinLayoutAndContent();

        //
        $this->_registerSubClassComponents();

        //
        $this->_parse();

        //

        $this->_render();
    }

    /** ------------------------------------------------------------------------
     * CONTENT INITIALIZERS
     * -----------------------------------------------------------------------*/

    /**
     *
     */
    private function _locateAndJoinLayoutAndContent()
    {
        $layout = $this->_getLayoutContents();
        $content = $this->_getFileContents();

        return $this->_injectViewContent($layout, $content);
    }

    /**
     *
     */
    private function _injectViewContent(string $layout, string $content): array
    {

        $contentSearchString = '/@content/';

        if (preg_match($contentSearchString, $layout, $match)) {
            $rawView = preg_replace($contentSearchString, $content, $layout);
        } else {
            $rawView = $layout;
        }

        return explode("\n", $rawView);
    }

    /**
     *
     */
    private function _getLayoutContents(): string
    {
        $layoutPath = $this->_layoutPath . $this->_layout . '.html';
        //

        try {

            if (!file_exists($layoutPath)) {
                throw new \Exception\MissingFileException('Template File: <i>' . $layoutPath . '</i> not found.');
            }

            $content = \file_get_contents($layoutPath);

            return $content;
        } catch (\Exception\MissingFileException $e) {
            die($e->getMessage());
        }
    }

    /**
     *
     * @param string $filePath
     * @return array
     */
    private function _getFileContents(): string
    {

        //
        try {

            if (!file_exists($this->_filePath)) {
                throw new \Exception\MissingFileException('Template File: <i>' . $this->_filePath . '</i> not found.');
            }

            $content = \file_get_contents($this->_filePath);

            return $content;
        } catch (\Exception\MissingFileException $e) {
            die($e->getMessage());
        }
    }

    /** ------------------------------------------------------------------------
     * SETTERS
     * -----------------------------------------------------------------------*/

    /**
     *
     */
    public function setLayout(string $layout)
    {
        $this->_layout = $layout;
    }

    /**
     *
     */
    public function setFilepath(string $filePath)
    {
        $this->_filePath = $this->_createFilePath($filePath);
    }

    /**
     *
     */
    public function setData(array $displayData)
    {
        $this->_displayData = $displayData;
    }

    /**
     *
     * @param string $filePath
     * @return string
     */
    private function _createFilePath(string $filePath): string
    {
        return $this->_viewPath . $filePath;
    }

    private function _parse()
    {
        $this->_layoutTemplate();
        $this->_commandStructures();
        $this->_simpleTemplate();
    }

    private function _layoutTemplate()
    {
        //
        $this->LayoutTemplate->parse($this->_fileContents);
        $this->_fileContents = $this->LayoutTemplate->output();
    }
    private function _commandStructures()
    {
        //
        $this->CommandParser->parse($this->_fileContents, $this->_displayData);
        $this->_fileContents = $this->CommandParser->output();

        // echo '<pre>
        // <h3>Below this line is the Display class output. <span style="color: red"><hr />';
        // print_r($this->_fileContents);
    }
    private function _simpleTemplate()
    {
        //
        $preTemplateString = implode("\n", $this->_fileContents);
        $templatedString = $this->Templater::parse($preTemplateString, $this->_displayData);

        $this->output = $templatedString;
    }
    private function _registerSubClassComponents()
    {
        $this->_registerLayoutTemplate(new \Edev\Resource\Display\Template\LayoutTemplate());
        $this->_registerCommandParser(new \Edev\Resource\Display\Parser\CommandParser());
        $this->_registerTemplater(new \Edev\Resource\Display\Template\Templater());
    }
    private function _registerCommandParser(\Edev\Resource\Display\Parser\CommandParser $CommandParser)
    {
        $this->CommandParser = $CommandParser;
        $this->CommandParser->parent = $this;
    }
    private function _registerLayoutTemplate(\Edev\Resource\Display\Template\LayoutTemplate $LayoutTemplate)
    {
        $this->LayoutTemplate = $LayoutTemplate;
    }
    private function _registerTemplater(\Edev\Resource\Display\Template\Templater $Templater)
    {
        $this->Templater = $Templater;
    }
    public function debug()
    {
        echo '<pre>';
        print_r($this);
    }
    public function getUpstreamContentFromChild(array $upstreamContent)
    {
        echo "<h2>DISPLAY</h2>";
        print_r($upstreamContent);
    }
    private function _render()
    {
        echo $this->output;
    }
}
