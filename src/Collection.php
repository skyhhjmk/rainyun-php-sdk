<?php

namespace RainYun;

use ArrayIterator;
use IteratorAggregate;
use Countable;

class Collection implements IteratorAggregate, Countable
{
    /**
     * Stored attributes from response (nested arrays may be wrapped into Collection lazily)
     */
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $this->hydrate($attributes);
    }

    protected function hydrate($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        $isAssoc = $this->isAssoc($data);
        if ($isAssoc) {
            $out = [];
            foreach ($data as $k => $v) {
                $out[$k] = $this->wrap($v);
            }
            return $out;
        }
        // list
        return array_map(fn($v) => $this->wrap($v), $data);
    }

    protected function wrap($value)
    {
        if (is_array($value)) {
            if ($this->isAssoc($value)) {
                return new self($value);
            }
            return array_map(fn($v) => is_array($v) && $this->isAssoc($v) ? new self($v) : $v, $value);
        }
        return $value;
    }

    protected function isAssoc(array $arr): bool
    {
        if ($arr === []) return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function toArray(): array
    {
        return $this->unwrap($this->attributes);
    }

    protected function unwrap($value)
    {
        if ($value instanceof self) {
            return $value->toArray();
        }
        if (is_array($value)) {
            return array_map(fn($v) => $this->unwrap($v), $value);
        }
        return $value;
    }

    // IteratorAggregate
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->attributes);
    }

    // Countable
    public function count(): int
    {
        return count($this->attributes);
    }
}
