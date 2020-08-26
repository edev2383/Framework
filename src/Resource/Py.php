<?php

namespace Edev\Resource\Py;

class Py 
{
    private $root = "./cgi-bin/py/";
    private $module;
    private $fileName;
    private $params = [];
    private $py = 'python3';

    public static function build(string $module, string $fileName, array $params = []) {
        return (new static )->script($module, $fileName, $params);
    }

    private function script(string $module, string $fileName, array $params = []) {
        $this->module = $module;  
        $this->fileName = $fileName;
        $this->params = $params; 
        return $this;
    }

    public function run() {
        $filePath = $this->root . $this->module . '/' . $this->fileName;
        $cmd = $this->py . ' ' . $filePath;
        return shell_exec($cmd);
    }
}