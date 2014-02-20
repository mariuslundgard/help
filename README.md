php-util
========

Utility functions and classes for PHP.

Util\Dictionary
---------------

Usage example:

```php
use Util;

$dict = new Dict([
	'path.to.item'    => 123,
	'path.to.another' => 124,
]);

echo json_encode($dict->get()); // -> { "path": { "to": { "item": 123, "another": "124 "}}}
echo $dict['path.to-item'];     // -> 123

```
