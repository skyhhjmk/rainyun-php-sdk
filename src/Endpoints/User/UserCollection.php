<?php

namespace RainYun\Endpoints\User;

use RainYun\Collection;

/**
 * UserCollection - Collection for User API responses.
 *
 * Provides convenient access to user data:
 * - $result->code - HTTP code
 * - $result->data - User data object
 * - $result->data->ID - User ID
 * - $result->data->Name - User name
 * - $result->data->Email - User email
 */
class UserCollection extends Collection
{
    /**
     * Check if the request was successful.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return isset($this->attributes['code']) && $this->attributes['code'] === 200;
    }

    /**
     * Get the response code.
     *
     * @return int|null
     */
    public function getCode(): ?int
    {
        return $this->attributes['code'] ?? null;
    }

    /**
     * Get the user data.
     *
     * @return Collection|null
     */
    public function getData()
    {
        return $this->attributes['data'] ?? null;
    }
}
