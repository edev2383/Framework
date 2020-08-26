<?php

namespace Edev\Resource;

class Shell
{
    public static function execute($db, $user, $password, $host, $path)
    {
        $old_path = getcwd();
        echo "[ old_path: $old_path ]";
        chdir('/home/zerodock/public_html/App/Installer/');

        // $x = scandir($new_path);
        // print_r($x);

        $x = file_exists('./install.sh');

        echo "[ x: $x ]";

        $output = shell_exec("./install.sh $db $host $user $password");

        chdir($old_path);

        echo $output;
    }

    public function script(string $path, string $filename, $sudo = true)
    {
        try {
            if (!$this->fileExists($path, $filename)) {
                throw new \Exception('Shell exception. File not found. Path: ' . $path);
            }

            $this->sudo = $sudo;
            $this->setFilename($filename);
            $this->setPath($path);
            return $this;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function run()
    {

        $args = join(" ", func_get_args());

        $file = $this->path . $this->filename;

        if (!$this->fileExists($file)) {
            die('File not found.');
        } 

        $script = $this->sudo ? 'sudo ' : '';

        $script .= $file . " $args";

        $output = shell_exec($script);

        return $output;
    }

    // public function resetScript($script)
    // {
    //     $path = '/home/zerodock/dev/cgi-bin/' . $script . ' ';

    //     if (!file_exists($path)) {
    //         die('file does not exist. script terminated');
    //     }

    //     $output = shell_exec($path);
    //     echo "<p>Script: $path</p>";
    //     echo "<p>Output: $output</p>";
    // }
    public function setFilename(string $filename)
    {
        $this->filename = $filename;
    }
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    private function fileExists($file)
    {
        return file_exists($file);
    }
}
