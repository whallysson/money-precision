<?php

declare(strict_types=1);

if (!function_exists('match')) {
    /**
     * A simple match function that compares a value against multiple cases.
     *
     * @param mixed $value The value to match.
     * @param array $cases An associative array of cases, where keys can be callables or values to compare.
     * @param mixed|null $default Optional default value to return if no matches are found.
     * @return mixed The result associated with the matched case or the default value.
     */
    function match($value, array $cases, $default = null)
    {
        foreach ($cases as $case => $result) {
            if ((is_callable($case) && $case($value)) || $case === $value) {
                return is_callable($result) ? $result($value) : $result;
            }
        }

        return $default;
    }
}
