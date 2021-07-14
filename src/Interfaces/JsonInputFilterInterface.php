<?php

namespace App\Interfaces;

/**
 * Interface JsonInputFilterInterface
 * @package App\Interfaces
 */
interface JsonInputFilterInterface
{
    /**
     * @param array $json
     * @return array
     */
    public function filterInput(array $json): array;
}