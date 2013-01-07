<?php

namespace WT\PharCompilerTest;

use WT\PharCompiler\Compiler;
use PHPUnit_Framework_TestCase;

class CompilerTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        $path = $this->getPharPath();
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function getPharPath($pharFile = 'test.phar')
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '_assets' . DIRECTORY_SEPARATOR . $pharFile;
    }

    public function testGetFullPath()
    {
        $compiler = new Compiler($this->getPharPath());

        $this->assertSame($this->getPharPath(), $compiler->getFullPath());
    }

    public function testGetOutputName()
    {
        $compiler = new Compiler($this->getPharPath());

        $this->assertSame('test.phar', $compiler->getOutputName());
    }

    public function testGetOutputNameNoExtension()
    {
        $compiler = new Compiler($this->getPharPath('test'));

        $this->assertSame('test.phar', $compiler->getOutputName());
    }

    public function testGetOutputPath()
    {
        $compiler = new Compiler($this->getPharPath());

        $this->assertSame(dirname($this->getPharPath()), $compiler->getOutputPath());
    }

    public function testGetOutputPathWithEmptyConstructor()
    {
        $compiler = new Compiler($this->getPharPath());

        $this->assertSame(dirname($this->getPharPath()), $compiler->getOutputPath());
    }

    public function testGetExistingVariable()
    {
        $compiler = new Compiler($this->getPharPath());
        $compiler->setVariable('TestVariable', 'TestValue');

        $this->assertSame('TestValue', $compiler->getVariable('TestVariable'));
    }

    public function testGetNonExistingVariable()
    {
        $compiler = new Compiler($this->getPharPath());

        $this->assertSame(null, $compiler->getVariable('TestVariable'));
    }

    public function testGetVariables()
    {
        $compiler = new Compiler($this->getPharPath());
        $compiler->setVariables(array(
            'TestVariable1' => 'TestValue1',
            'TestVariable2' => 'TestValue2',
        ));

        $this->assertArrayHasKey('TestVariable1', $compiler->getVariables());
        $this->assertArrayHasKey('TestVariable2', $compiler->getVariables());
    }

    public function testSetVariable()
    {
        $compiler = new Compiler($this->getPharPath());
        $compiler->setVariable('TestVariable', 'TestValue');

        $this->assertArrayHasKey('TestVariable', $compiler->getVariables());
    }

    public function testSetVariables()
    {
        $compiler = new Compiler($this->getPharPath());
        $compiler->setVariables(array(
            'TestVariable1' => 'TestValue1',
            'TestVariable2' => 'TestValue2',
        ));

        $this->assertArrayHasKey('TestVariable1', $compiler->getVariables());
        $this->assertArrayHasKey('TestVariable2', $compiler->getVariables());
    }

    public function testCompile()
    {
        $compiler = new Compiler($this->getPharPath());
        $compiler->compile();
		
        $this->assertFileExists($this->getPharPath());
    }
}
