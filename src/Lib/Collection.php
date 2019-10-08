<?php

namespace Dataloft\Carrental\Lib;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection
{
    /**
     * Обычная коллекция подразумевает что элементы - исключительно массивы, а не объекты
     *
     * @param array|string $value
     * @param null $key
     * @return static
     */
    public function pluck($value, $key = null)
    {
        return new static(array_pluck(array_map(function (Arrayable $item) {
            return $item->toArray();
        }, $this->items), $value, $key));
    }
}
