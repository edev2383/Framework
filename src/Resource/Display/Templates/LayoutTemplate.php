<?php

declare(strict_types=1);

namespace Edev\Resource\Display\Template;

use Edev\System\Helpers\Domain;

class LayoutTemplate
{

    private $_templateName;
    private $_filePath;
    private $_fileContents;

    private $_reTemplate = '/@include\((.*?)\)/';

    private $_path = '/home/zerodock/public_html/View/template/';

    public function __construct()
    {
        $this->_overridePathForDevEnvironment();
    }

    private function _overridePathForDevEnvironment()
    {
        if (Domain::isDev()) {
            $this->_path = '/home/zerodock/dev/View/template/';
        }
    }

    public function parse(array $fileContents)
    {
        $this->_fileContents = $fileContents;
        $this->_loopForLayoutTemplates();
    }

    private function _loopForLayoutTemplates()
    {
        $len = count($this->_fileContents);
        for ($ii = 0; $ii < $len; $ii++) {
            $curr = $this->_fileContents[$ii];
            $this->_checkLineForInclude($curr, $ii);
            $len = count($this->_fileContents);
        }
    }


    public function output()
    {
        return $this->_fileContents;
    }

    /**
     *
     * @param string $line
     * @param integer $index
     * @return void
     */
    private function _checkLineForInclude(string $line, int $index)
    {
        if (preg_match($this->_reTemplate, $line, $match)) {
            $templatePath = $this->_createFilePath($match[1]);

            $newTemplateArray = $this->_getFileContents($templatePath);
            $this->_pushToParentArray($newTemplateArray, $index);
        }
    }

    private function _pushToParentArray(array $newArray, int $index)
    {

        $fileContentsBeforeBreak = array_slice($this->_fileContents, 0, $index);
        $fileContentsAfterBreak = array_slice($this->_fileContents, $index + 1, count($this->_fileContents));
        $this->_fileContents = array_merge($fileContentsBeforeBreak, $newArray, $fileContentsAfterBreak);
    }

    /**
     *
     * @return void
     */
    private function _createFilePath(string $templateName): string
    {
        return $this->_path . $templateName . '.html';
    }

    /**
     *
     * @param string $filePath
     * @return array
     */
    private function _getFileContents(string $filePath)
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception\MissingFileException('Template File: <i>' . $filePath . '</i> not found.');
            }
            $content = \file_get_contents($filePath);
            return explode("\n", $content);
        } catch (\Exception\MissingFileException $e) {
            die($e->getMessage());
        }
    }

    /**
     *
     * @param string $newPath
     * @return void
     */
    public function overridePath(string $newPath)
    {
        $this->_path = $newPath;
    }
}