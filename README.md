PharCompiler
============

[![Build Status](https://travis-ci.org/WalterTamboer/pharcompiler.png)](https://travis-ci.org/WalterTamboer/pharcompiler)

PharCompiler is a compiler to easily create PHAR files. The concept is that you create a 
new instance of the compiler to which you give the name of the PHAR file. Next you add the 
files that should be packed into the archive and last you call `compile`.

Compiling
---------
Compiling a .phar file is easy. 
```
<?php

// build.php:
$compiler = new \WT\PharCompiler\Compiler('my.phar');
$compiler->setVariable('package_version', '1.0.0');
$compiler->addFile(__DIR__ . '/src/test.php');
$compiler->compile();
```

By using `addFile` and `addDirectory` you can add a list of files to the archive.

Meta Data Variables
-------------------
It's possible to add meta data to the compiler. This meta data is injected in the source 
files. For example:

```
<?php

// build.php:
$compiler = new \WT\PharCompiler\Compiler();
$compiler->setVariable('package_version', '1.0.0');
$compiler->addFile(__DIR__ . '/src/test.php');
$compiler->compile();
```

```
<?php

// test.php:

echo 'Version: @package_version@';
```

Real Life Example
-----------------
An real life example of how to use it can be seen here: https://github.com/pixelpolishers/resolver
