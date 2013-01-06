<?php

namespace WT\PharCompilerTest;

use WT\PharCompiler\Compiler;
use PHPUnit_Framework_TestCase;

class CompilerTest extends PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        $path = $this->getPharPath() . DIRECTORY_SEPARATOR . 'test.phar';
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function getPharPath()
    {
        return __DIR__ . '/_assets';
    }

    private function createCompiler($outputPath = null)
    {
        if (!$outputPath) {
            $outputPath = $this->getPharPath();
        }
        return new Compiler('test.phar', $outputPath);
    }

    public function testGetFullPath()
    {
        $compiler = $this->createCompiler('./output');

        $this->assertSame('./output/test.phar', $compiler->getFullPath());
    }

    public function testGetOutputName()
    {
        $compiler = $this->createCompiler();

        $this->assertSame('test.phar', $compiler->getOutputName());
    }

    public function testGetOutputNameNoExtension()
    {
        $compiler = new Compiler('test');

        $this->assertSame('test.phar', $compiler->getOutputName());
    }

    public function testGetOutputPath()
    {
        $compiler = $this->createCompiler('./output');

        $this->assertSame('./output', $compiler->getOutputPath());
    }

    public function testGetOutputPathWithEmptyConstructor()
    {
        $compiler = $this->createCompiler('./output');

        $this->assertSame('./output', $compiler->getOutputPath());
    }

    public function testGetExistingVariable()
    {
        $compiler = $this->createCompiler();
        $compiler->setVariable('TestVariable', 'TestValue');

        $this->assertSame('TestValue', $compiler->getVariable('TestVariable'));
    }

    public function testGetNonExistingVariable()
    {
        $compiler = $this->createCompiler();

        $this->assertSame(null, $compiler->getVariable('TestVariable'));
    }

    public function testGetVariables()
    {
        $compiler = $this->createCompiler();
        $compiler->setVariables(array(
            'TestVariable1' => 'TestValue1',
            'TestVariable2' => 'TestValue2',
        ));

        $this->assertArrayHasKey('TestVariable1', $compiler->getVariables());
        $this->assertArrayHasKey('TestVariable2', $compiler->getVariables());
    }

    public function testSetVariable()
    {
        $compiler = $this->createCompiler();
        $compiler->setVariable('TestVariable', 'TestValue');

        $this->assertArrayHasKey('TestVariable', $compiler->getVariables());
    }

    public function testSetVariables()
    {
        $compiler = $this->createCompiler();
        $compiler->setVariables(array(
            'TestVariable1' => 'TestValue1',
            'TestVariable2' => 'TestValue2',
        ));

        $this->assertArrayHasKey('TestVariable1', $compiler->getVariables());
        $this->assertArrayHasKey('TestVariable2', $compiler->getVariables());
    }

    public function testCompile()
    {
        $compiler = $this->createCompiler();
        $compiler->compile();

        $path = $this->getPharPath() . DIRECTORY_SEPARATOR . 'test.phar';
        $this->assertFileExists($path);
    }
}
