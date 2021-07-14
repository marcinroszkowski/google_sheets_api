<?php

namespace App\Decorators;

use App\Interfaces\JsonInputFilterInterface;

/**
 * Class JsonInput
 * @package App\Decorators
 */
class JsonInput implements JsonInputFilterInterface
{
    /**
     * @param array $json
     * @return array
     */
    public function filterInput(array $json): array
    {
        return $json;
    }
}