<?php

namespace Util;

use ArrayAccess;
use Exception;

class Dictionary implements ArrayAccess
{
    protected $data;

    public function __construct(array $data = [], array $config = [])
    {
        $this->data = [];

        $this->config = $config + [
            'delimiter' => '.',
        ];

        $this->merge($data);
    }

    public function getDelimiter()
    {
        $delim = $this->config['delimiter'];

        if (! $delim) {
            throw new Exception('The delimiter cannot be empty');
        }

        return $delim;
    }

    public function get($property = null, $default = null)
    {
        if (! $default && is_array($property)) {
            $this->data = [];
            $this->merge($property);

            return;
        }

        if ($property) {
            $result = delim_get($this->data, $property, $this->getDelimiter());

            return (null === $result)
                ? $default
                : $result;
        }

        return $this->data;
    }

    public function set($key, $value = null)
    {
        if (is_array($key) && !$value) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            delim_set($this->data, $key, $value, $this->getDelimiter(), true);
        }
    }

    public function merge(array $data)
    {
        $this->data = delim_merge($this->data, $data, $this->getDelimiter(), true);
    }

    public function setBeforePath($beforePath, $path, $value)
    {
        $this->data = array_set_insert_before_path($this->data, $beforePath, $path, $value, $this->getDelimiter());

        return $this->data;
    }

    public function offsetExists($path)
    {
        return delim_isset($this->data, $path, $this->getDelimiter());
    }

    public function offsetGet($path)
    {
        return $this->get($path);
        // return delim_get($this->data, $path, $this->getDelimiter());
    }

    public function offsetSet($path, $value)
    {
        return $this->set($path, $value);
        // return delim_set($this->data, $path, $value, $this->getDelimiter(), true);
    }

    public function offsetUnset($path)
    {
        return delim_unset($this->data, $path, $this->getDelimiter());
    }
}
