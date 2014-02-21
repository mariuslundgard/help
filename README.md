php-util
========

Utility functions and classes for PHP.

Dictionary
---------------

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
<?php

require __DIR__.'/../vendor/autoload.php';

use Util\Dictionary;

class App
{
    protected $path;
    protected $config;

    public function __construct($path, array $config)
    {
        $this->path = $path;
        $this->config = new Dictionary($config);
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getConfig($key = null, $default = null)
    {
        return $this->config->get($key, $default);
    }
}

$app = new App(__DIR__, [
    'db.user' => 'root',
    'db.pass' => 'test',
]);

echo json_encode($app->getConfig('db')) . '<br>'; // { "user": "root", "pass": "test" }
echo $app->getConfig('db.user');                  // root

```
