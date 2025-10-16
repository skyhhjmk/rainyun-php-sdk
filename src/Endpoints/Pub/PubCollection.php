<?php

namespace RainYun\Endpoints\Pub;

use RainYun\Collection;

class PubCollection extends Collection
{
    // Example helper for Pub module; same method name can differ in other modules
    public function count(): int
    {
        return (int) $this->data->TotalRecords;
    }
}
