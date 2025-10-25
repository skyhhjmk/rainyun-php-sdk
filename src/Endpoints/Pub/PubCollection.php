<?php

namespace RainYun\Endpoints\Pub;

use RainYun\Collection;

abstract class PubCollection extends Collection
{
    public function count(): int
    {
        return (int) $this->attributes['data']->TotalRecords ?? 0;
    }
}
