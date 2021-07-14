<?php

namespace App\Decorators;

/**
 * Class JsonWithoutRedundantData
 * @package App\Decorators
 */
class JsonWithoutRedundantData extends JsonFilter
{
    const UNNECESSARY_KEYS = ['status', 'timestamp','token'];

    /**
     * @param array $json
     * @return array
     */
    public function filterInput(array $json): array
    {
        $json = parent::filterInput($json);
        foreach (self::UNNECESSARY_KEYS as $unnecessaryKey) {
            if (isset($unnecessaryKey, $json)) {
                unset($json[$unnecessaryKey]);
            }
        }

        return $json;
    }
}