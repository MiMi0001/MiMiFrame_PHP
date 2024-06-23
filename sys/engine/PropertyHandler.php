<?php

namespace app\engine;
#[\AllowDynamicProperties]


class PropertyHandler
{
    public function set(string $property, $value)
    {
        $this->{$property} = $value;

        return $this;
    }

    public function get(string $property)
    {
        return $this->{$property};
    }

    public function has(string $property)
    {
        return property_exists($this, $property);
    }


}