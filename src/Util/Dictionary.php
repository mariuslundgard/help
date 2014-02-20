<?php

namespace Help;

use ArrayAccess;

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

    public function get($property = null, $default = null)
    {
        if ($property) {
            $result = array_delim_get($this->data, $property, $this->config['delimiter']);

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
            array_delim_set($this->data, $key, $value, $this->config['delimiter'], true);
        }
    }

    public function merge(array $data)
    {
        $this->data = array_delim_merge($this->data, $data, $this->config['delimiter']);
    }

    public function setBeforePath($beforePath, $path, $value)
    {
        $this->data = array_set_insert_before_path($this->data, $beforePath, $path, $value, $this->config['delimiter']);

        return $this->data;
    }

    public function offsetExists($path)
    {
        return array_delim_isset($this->data, $path, $this->config['delimiter']);
    }

    public function offsetGet($path)
    {
        return array_delim_get($this->data, $path, $this->config['delimiter']);
    }

    public function offsetSet($path, $value)
    {
        return array_delim_set($this->data, $path, $value, true, $this->config['delimiter']);
    }

    public function offsetUnset($path)
    {
        return array_delim_unset($this->data, $path, $this->config['delimiter']);
    }
}
