php-util
========

[![Build Status](https://travis-ci.org/mariuslundgard/php-util.svg)](https://travis-ci.org/mariuslundgard/php-util)
[![Coverage Status](https://coveralls.io/repos/mariuslundgard/php-util/badge.png)](https://coveralls.io/r/mariuslundgard/php-util)

[![Latest Stable Version](https://poser.pugx.org/mariuslundgard/php-util/v/stable.png)](https://packagist.org/packages/mariuslundgard/php-util)


Utility functions and classes for PHP.

## Examples

### The Util\Dictionary class

Usage example:

```php
<?php

require 'vendor/autoload.php';

use Util\Dictionary;

$dict = new Dictionary([
	'path.to.item'    => 123,
	'path.to.another' => 124,
]);

echo json_encode($dict->get()); // -> { "path": { "to": { "item": 123, "another": "124 " }}}
echo $dict['path.to.item'];     // -> 123

```

Using a Dictionary object for filesystem representation.

```php
use Util\Dictionary;

$rootDir = dirname(__DIR__);

$dir = new RecursiveDirectoryIterator($rootDir);
$iter = new RecursiveIteratorIterator($dir);
$regex = new RegexIterator($iter, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$phpFiles = new Dictionary([], [
    'delimiter' => '/',
]);

foreach ($regex as $key => $file) {
    $phpFiles[trim(realpath($file[0]), '/')] = 'Modified '.time_elapsed_string(filemtime($file[0]));
}

echo '<pre>';
echo json_encode($phpFiles[trim($rootDir, '/')], JSON_PRETTY_PRINT);
echo '</pre>';

// {
//     "example": {
//         "index.php": "Modified 13 hours ago"
//     },
//     "src": {
//         "array.php": "Modified 9 hours ago",
//         "object.php": "Modified 20 days ago",
//         "string.php": "Modified 2 days ago",
//         "time.php": "Modified 7 hours ago",
//         "Util": {
//             "Dictionary.php": "Modified 5 seconds ago"
//         }
//     },
//     ...

```

Using a Dictionary object for application configuration.

```php
use Util\Dictionary;

class MyApplication
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = new Dictionary($config);
    }

    public function __get($property)
    {
        switch ($property) {

            case 'config':
                return $this->config;

            default:
                throw new Exception('Unknown application property: '.$property);
        }
    }

    public function configure(array $config)
    {
        $this->config->merge($config);

        return $this;
    }
}

$app = (new App())
    ->configure([
        'db.user' => 'root',
        'db.pass' => 'test',
    ]);

echo $app->config['db.user'];         // root
echo json_encode($app->config['db']); // { "user": "root", "pass": "test" }

```
