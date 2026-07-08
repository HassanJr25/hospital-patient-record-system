<?php

class Validator
{
    public static function isEmpty(string $value): bool
    {
        return trim($value) === '';
    }

    public static function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isValidDate(string $value): bool
    {
        $date = DateTime::createFromFormat('Y-m-d', $value);
        return $date && $date->format('Y-m-d') === $value;
    }

    public static function minLength(string $value, int $length): bool
    {
        return mb_strlen($value) >= $length;
    }

    public static function isAlphaSpace(string $value): bool
    {
        // Allows letters and spaces only - useful for name fields.
        return preg_match('/^[a-zA-Z\s]+$/', $value) === 1;
    }
}
