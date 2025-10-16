<?php

namespace RainYun;

/**
 * Interface for all options classes used in API requests.
 */
interface OptionsInterface
{
    /**
     * Convert options to array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;

    /**
     * Convert options to JSON string for API request.
     *
     * @return string
     */
    public function toJson(): string;
}
