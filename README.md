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

echo json_encode($dict->get()); // -> { "path": { "to": { "item": 123, "another": "124 "}}}
echo $dict['path.to-item'];     // -> 123

```
