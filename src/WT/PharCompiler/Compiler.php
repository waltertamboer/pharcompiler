<?php

namespace WT\PharCompiler;

use Phar;
use RuntimeException;
use Symfony\Component\Finder\Finder;

class Compiler
{
    private $outputName;
    private $outputPath;
    private $finders;
    private $variables;
    private $phar;

    public function __construct($outputName, $outputPath = null)
    {
        $this->outputName = $this->createOutputName($outputName);
        $this->outputPath = $outputPath ? rtrim($outputPath, '/') : './output';
        $this->finders = array();
        $this->variables = array();
    }

    public function getFullPath()
    {
        return $this->outputPath . '/' . $this->outputName;
    }

    public function getVariable($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->variables) ?
            $this->variables[$name] : $defaultValue;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    public function setVariable($name, $value)
    {
        $this->variables[$name] = $value;
        return $this;
    }

    public function setVariables($variables)
    {
        $this->variables = $variables;
        return $this;
    }

    public function getFinder($name = 'default')
    {
        if (!array_key_exists($name, $this->finders)) {
            $this->finders[$name] = new Finder();
        }
        return $this->finders[$name];
    }

    public function getOutputName()
    {
        return $this->outputName;
    }

    public function getOutputPath()
    {
        return $this->outputPath;
    }

    public function getPHAR()
    {
        if (!$this->phar) {
            $this->phar = new Phar($this->getFullPath(), 0, $this->outputName);
            $this->phar->setSignatureAlgorithm(Phar::SHA1);
        }
        return $this->phar;
    }

    private function createOutputName($outputName)
    {
        if (substr($outputName, -5) != '.phar') {
            $outputName .= '.phar';
        }
        return $outputName;
    }

    public function compile()
    {
        $pharFile = $this->getFullPath();

        if (file_exists($pharFile)) {
            unlink($pharFile);
        } elseif (!is_dir($this->outputPath)) {
            throw new RuntimeException('The output path does not exist.');
        }

        $phar = $this->getPHAR();
        $phar->startBuffering();

        foreach ($this->finders as $finder) {
            foreach ($finder as $file) {
                $this->addFile($phar, $file);
            }
        }

        $phar->stopBuffering();
        $phar->setStub($this->getStub());

        unset($phar);
    }

    private function addFile($phar, $file, $stripWhitespace = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR, '', $file->getRealPath());

        $content = file_get_contents($file);

        if ($stripWhitespace) {
            $content = $this->stripWhitespace($content);
        }

        foreach ($this->variables as $name => $value) {
            $content = str_replace('@' . $name . '@', $value, $content);
        }

        $phar->addFromString($path, $content);
    }

    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function getStub()
    {
        $stub = '#!/usr/bin/env php' . PHP_EOL;
        $stub .= '<?php' . PHP_EOL;
        $stub .= "Phar::mapPhar('" . $this->outputName . "');" . PHP_EOL;
        $stub .= "require 'phar://" . $this->outputName . "/bootstrap.php';" . PHP_EOL;
        $stub .= '__HALT_COMPILER();';

        return $stub;
    }
}
