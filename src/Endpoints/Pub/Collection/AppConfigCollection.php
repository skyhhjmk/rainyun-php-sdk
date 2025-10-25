<?php

namespace RainYun\Endpoints\Pub\Collection;

use RainYun\Endpoints\Pub\PubCollection;

/**
 * AppConfigCollection
 */
class AppConfigCollection extends PubCollection
{
    public function count(): int
    {
        return count($this->data);
    }

}