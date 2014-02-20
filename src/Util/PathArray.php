<?php

namespace Help;

class PathArray implements \ArrayAccess
{
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = [];

        $this->merge($data);
    }

    public function get($property = null, $default = null)
    {
        if ($property) {
            $result = array_dot_get($this->data, $property);

            return (null === $result)
                ? $default
                : $result;
        }

        return $this->data;
    }

    // public function getIndex($path) {
    //     $parts = explode('.', $path);

    //     if (1 < count($parts)) {
    //         $name = array_pop($parts);
    //         $parentPath = implode('.', $parts);
    //         $container = $this->get($parentPath);
    //         // d($container);
    //     } else {
    //         $name = $path;
    //         $container = $this->data;
    //     }

    //     foreach ($container as $index => $value) {
    //         if ($index === $name) {
    //             return $index;
    //         }
    //     }

    //     return null;
    // }

    public function set($key, $value = null)
    {
        if (is_array($key) && !$value) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            array_dot_set($this->data, $key, $value, true);
        }
    }

    public function merge(array $data)
    {
        $this->data = array_dot_merge($this->data, $data);
    }

    public function setBeforePath($beforePath, $path, $value)
    {
        $this->data = array_dot_insert_before_path($this->data, $beforePath, $path, $value);

        return $this->data;
    }

    public function offsetExists($path)
    {
        return array_dot_isset($this->data, $path);
    }

    public function offsetGet($path)
    {
        return array_dot_get($this->data, $path);
    }

    public function offsetSet($path, $value)
    {
        return array_dot_set($this->data, $path, $value, true);
    }

    public function offsetUnset($path)
    {
        return array_dot_unset($this->data, $path);
    }

    // public function __toString()
    // {
    //     return 'patharray';
    // }

    // public function __sleep()
    // {
    //     return ['sleep' => true];
    // }
}
