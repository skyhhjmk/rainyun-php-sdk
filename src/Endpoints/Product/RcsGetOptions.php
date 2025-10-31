<?php

namespace RainYun\Endpoints\Product;

use RainYun\OptionsInterface;

/**
 * Options class for RCS GET requests.
 *
 * Provides a fluent interface for building RCS query options.
 */
class RcsGetOptions implements OptionsInterface
{
    private array $data = [
        'is_rgpu' => null,
        'options' => '{}',
    ];

    public function __construct()
    {
    }

    public static function make(): self
    {
        return new self();
    }

    /**
     * Set whether to filter for RGPU instances.
     *
     * @param bool $isRgpu Whether to filter for RGPU instances
     * @return self
     */
    public function isRgpu(bool $isRgpu): self
    {
        $this->data['is_rgpu'] = $isRgpu;
        return $this;
    }

    /**
     * Set custom options (as JSON string or array).
     *
     * @param string|array $options Custom options
     * @return self
     */
    public function options($options): self
    {
        if (is_array($options)) {
            if (empty($options)) {
                $this->data['options'] = '{}';
            } else {
                $this->data['options'] = json_encode($options, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        } else {
            $this->data['options'] = $options;
        }
        return $this;
    }

    /**
     * Convert to JSON string for API request.
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get data as array for query parameters.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        
        if ($this->data['is_rgpu'] !== null) {
            $result['is_rgpu'] = $this->data['is_rgpu'];
        }
        
        $result['options'] = $this->data['options'];
        
        return $result;
    }
}
