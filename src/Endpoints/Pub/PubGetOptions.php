<?php

namespace RainYun\Endpoints\Pub;

use RainYun\OptionsInterface;

/**
 * Options for Pub::get() API request.
 *
 * This class provides a fluent interface to configure query parameters
 * for public endpoint GET requests.
 */
class PubGetOptions implements OptionsInterface
{
    /**
     * Sorting configuration.
     *
     * @var array<string, string>
     */
    private array $sort = [];

    /**
     * Current page number (1-based).
     *
     * @var int
     */
    private int $page = 1;

    /**
     * Number of items per page.
     *
     * @var int
     */
    private int $perPage = 20;

    /**
     * Value filters for the request.
     *
     * @var array<string, mixed>
     */
    private array $valueFilters = [];

    /**
     * Create a new instance.
     */
    public function __construct()
    {
    }

    /**
     * Create a new instance (static factory).
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Set sorting options.
     *
     * Example:
     * ```php
     * $options->sort(['field' => 'asc', 'created_at' => 'desc']);
     * ```
     *
     * @param array<string, string> $sort Associative array of field => direction
     * @return $this
     */
    public function sort(array $sort): self
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * Set page number.
     *
     * @param int $page Page number (1-based)
     * @return $this
     */
    public function page(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Set items per page.
     *
     * @param int $perPage Number of items per page
     * @return $this
     */
    public function perPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Set value filters (batch).
     *
     * Example:
     * ```php
     * $options->valueFilters(['Product' => 'rcs', 'Status' => 'active']);
     * ```
     *
     * @param array<string, mixed> $filters Associative array of filters
     * @return $this
     */
    public function valueFilters(array $filters): self
    {
        $this->valueFilters = $filters;
        return $this;
    }

    /**
     * Add a single value filter.
     *
     * Example:
     * ```php
     * $options->filter('Product', 'rcs');
     * ```
     *
     * @param string $key Filter key
     * @param mixed $value Filter value
     * @return $this
     */
    public function filter(string $key, $value): self
    {
        $this->valueFilters[$key] = $value;
        return $this;
    }

    /**
     * Convert options to array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'sort' => $this->sort,
            'page' => $this->page,
            'perPage' => $this->perPage,
            'valueFilters' => $this->valueFilters,
        ];
    }

    /**
     * Convert options to JSON string for API request.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
