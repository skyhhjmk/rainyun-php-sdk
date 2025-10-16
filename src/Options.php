<?php

namespace RainYun;

/**
 * Generic options class (deprecated).
 *
 * @deprecated Use endpoint-specific options classes instead (e.g., PubGetOptions).
 *             This class will be removed in a future version.
 */
class Options implements OptionsInterface
{
    private array $data = [
        'sort' => [],
        'page' => 1,
        'perPage' => 20,
        'valueFilters' => [],
    ];

    public function __construct()
    {
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * Set sorting options.
     */
    public function sort(array $sort = []): self
    {
        $this->data['sort'] = $sort;
        return $this;
    }

    /**
     * Set page number.
     */
    public function page(int $page): self
    {
        $this->data['page'] = $page;
        return $this;
    }

    /**
     * Set items per page.
     */
    public function perPage(int $perPage): self
    {
        $this->data['perPage'] = $perPage;
        return $this;
    }

    /**
     * Set value filters.
     */
    public function valueFilters(array $filters): self
    {
        $this->data['valueFilters'] = $filters;
        return $this;
    }

    /**
     * Add a single value filter.
     */
    public function filter(string $key, $value): self
    {
        $this->data['valueFilters'][$key] = $value;
        return $this;
    }

    /**
     * Convert to JSON string for API request.
     */
    public function toJson(): string
    {
        return json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get data as array.
     */
    public function toArray(): array
    {
        return $this->data;
    }
}