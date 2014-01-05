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

            return ($result)
                ? $result
                : $default;
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
            array_dot_set($this->data, $key, $value);
        }
    }

    public function merge(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
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
        return array_dot_set($this->data, $path, $value);
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
