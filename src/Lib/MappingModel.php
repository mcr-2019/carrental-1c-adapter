<?php

namespace Dataloft\Carrental\Lib;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use LogicException;

class MappingModel implements Arrayable
{
    /** @var array  */
    protected $attributes = [];

    /** @var array  */
    protected $attributes_map = [];

    /** @var array  */
    protected $casts = [];

    /** @var bool  */
    protected $mapping_enabled = true;

    /** @var bool  */
    protected $cast_enabled = true;

    public function __construct(array $attributes = [])
    {
        $this->fillAttributes($attributes);
    }

    public function fillAttributes(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if ($this->mapping_enabled) {
                foreach ($this->attributes_map as $base_attribute_name => $synonyms) {
                    if ($attribute == $base_attribute_name || in_array($attribute, (array) $synonyms)) {
                        $this->attributes[$base_attribute_name] = $this->castValue($base_attribute_name, trim($value));
                        continue 2;
                    }
                }
            }
            $this->attributes[$attribute] = $this->castValue($attribute, trim($value));
        }
        return $this;
    }

    public function disableMapping()
    {
        $this->mapping_enabled = false;
        return $this;
    }

    public function enableMapping()
    {
        $this->mapping_enabled = true;
        return $this;
    }

    public function disableCast()
    {
        $this->cast_enabled = false;
        return $this;
    }

    public function enableCast()
    {
        $this->cast_enabled = true;
        return $this;
    }

    protected function castValue($attribute, $value)
    {
        if ($this->cast_enabled && array_key_exists($attribute, $this->casts)) {
            switch ($this->casts[$attribute]) {
                case 'bool':
                    return (bool) (int) $value;
                    break;
                case 'float':
                case 'double':
                    return (float) $value;
                    break;
                case 'int':
                case 'integer':
                    return (int) $value;
                    break;
                case 'date':
                    return empty($value) ? null : Carbon::parse($value);
                    break;
                default:
                    throw new LogicException('Unexpected cast type: '.print_r($this->casts[$attribute], true));
            }
        }
        return $value;
    }

    public function __get($property)
    {
        return array_key_exists($property, $this->attributes)
            ? $this->attributes[$property]
            : null;
    }

    public function __set($property, $value)
    {
        $this->fillAttributes([$property => $value]);
    }

    public function __isset($property)
    {
        return isset($this->attributes[$property]);
    }

    public function toArray()
    {
        return $this->attributes;
    }
}
