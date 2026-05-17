<?php

namespace App\Helpers;

class CombinationHelper
{
    /**
     * Generate all combinations from an array of arrays.
     * Replacement for Laracon21\Combinations package.
     *
     * @param array $arrays
     * @return array
     */
    public static function makeCombinations($arrays)
    {
        $result = [[]];

        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property_value]);
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}
