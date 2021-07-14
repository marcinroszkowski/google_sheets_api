<?php

namespace App\Decorators;

use App\Interfaces\JsonInputFilterInterface;

/**
 * Base Decorator
 *
 * Class JsonFilterDecorator
 * @package App\Decorators
 */
class JsonFilter implements JsonInputFilterInterface
{
    protected $jsonInputFilter;

    /**
     * JsonFilter constructor.
     * @param JsonInputFilterInterface $jsonInputFilter
     */
    public function __construct(JsonInputFilterInterface $jsonInputFilter)
    {
        $this->jsonInputFilter = $jsonInputFilter;
    }

    /**
     * @param array $json
     * @return array
     */
    public function filterInput(array $json): array
    {
        return $this->jsonInputFilter->filterInput($json);
    }
}