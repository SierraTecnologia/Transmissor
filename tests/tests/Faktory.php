<?php

namespace AdamWathan\Faktory;

class Faktory
{
    protected $definitions = [];

    public function define($name, $callback)
    {
        $key = is_array($name) ? $name[0] : $name;
        $class = is_array($name) ? $name[1] : null;

        $this->definitions[$key] = [
            'class' => $class,
            'callback' => $callback,
        ];
    }

    public function build($name, $attributes = [])
    {
        if (!isset($this->definitions[$name])) {
            throw new \InvalidArgumentException("Factory definition '{$name}' not found.");
        }

        $def = $this->definitions[$name];
        $class = $def['class'];

        $proxy = new \stdClass();
        call_user_func($def['callback'], $proxy);

        $data = array_merge((array) $proxy, $attributes);
        $instance = new $class();
        foreach ($data as $key => $val) {
            $instance->{$key} = $val;
        }

        return $instance;
    }

    public function create($name, $attributes = [])
    {
        $instance = $this->build($name, $attributes);
        $instance->save();

        return $instance;
    }
}
