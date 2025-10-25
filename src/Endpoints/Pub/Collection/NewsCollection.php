<?php

namespace RainYun\Endpoints\Pub\Collection;

use RainYun\Collection;

/**
 * NewsCollection - Collection for News API responses.
 *
 * Provides convenient access to news data:
 * - $result->code - HTTP code (200)
 * - $result->data - Array of news items
 * - $result->data[0]->Type - News type (e.g., "更新动态", "最新活动")
 * - $result->data[0]->Title - News title
 * - $result->data[0]->TimeStamp - News timestamp
 * - $result->data[0]->URL - News URL
 */
class NewsCollection extends Collection
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
     * Get the news data array.
     *
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->attributes['data'] ?? null;
    }

    /**
     * Get news items filtered by type.
     *
     * @param string $type News type (e.g., "更新动态", "最新活动")
     * @return array
     */
    public function getByType(string $type): array
    {
        $data = $this->getData();
        if (!$data) {
            return [];
        }

        return array_filter($data, function ($item) use ($type) {
            return isset($item->Type) && $item->Type === $type;
        });
    }

    /**
     * Get the count of news items.
     *
     * @return int
     */
    public function count(): int
    {
        $data = $this->getData();
        return $data ? count($data) : 0;
    }
}
