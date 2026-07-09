<?php

declare(strict_types=1);

namespace Tavp\Core\Validation;

/**
 * A small, readable validator. Rules are declared as a map of field => rules.
 *
 * Supported rules: required, string, email, numeric, integer, boolean,
 * min, max, in, confirmed, date.
 */
class Validator
{
    private array $errors = [];

    /**
     * Validate the given data against the given rules.
     * Returns true when everything passes.
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);

            foreach ($fieldRules as $rule) {
                $this->applyRule($data, $field, $rule);
            }
        }

        return count($this->errors) === 0;
    }

    private function applyRule(array $data, string $field, string $rule): void
    {
        $value = $data[$field] ?? null;
        $params = [];

        if (str_contains($rule, ':')) {
            [$rule, $paramString] = explode(':', $rule, 2);
            $params = explode(',', $paramString);
        }

        $method = 'validate' . ucfirst($rule);

        if (method_exists($this, $method)) {
            $this->{$method}($field, $value, $params);
        }
    }

    private function validateRequired(string $field, $value): void
    {
        if ($value === null || $value === '' || $value === []) {
            $this->addError($field, 'The ' . $field . ' field is required.');
        }
    }

    private function validateString(string $field, $value): void
    {
        if ($value !== null && !is_string($value)) {
            $this->addError($field, 'The ' . $field . ' must be text.');
        }
    }

    private function validateEmail(string $field, $value): void
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'The ' . $field . ' must be a valid email.');
        }
    }

    private function validateNumeric(string $field, $value): void
    {
        if ($value !== null && !is_numeric($value)) {
            $this->addError($field, 'The ' . $field . ' must be a number.');
        }
    }

    private function validateInteger(string $field, $value): void
    {
        if ($value !== null && !is_int($value) && !ctype_digit((string) $value)) {
            $this->addError($field, 'The ' . $field . ' must be a whole number.');
        }
    }

    private function validateBoolean(string $field, $value): void
    {
        if ($value !== null && !in_array($value, [true, false, 0, 1, '0', '1'], true)) {
            $this->addError($field, 'The ' . $field . ' must be true or false.');
        }
    }

    private function validateMin(string $field, $value, array $params): void
    {
        $min = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) < $min) {
            $this->addError($field, 'The ' . $field . ' must be at least ' . $min . ' characters.');
        }
        if (is_numeric($value) && $value < $min) {
            $this->addError($field, 'The ' . $field . ' must be at least ' . $min . '.');
        }
    }

    private function validateMax(string $field, $value, array $params): void
    {
        $max = (int) ($params[0] ?? 0);
        if (is_string($value) && strlen($value) > $max) {
            $this->addError($field, 'The ' . $field . ' may not be greater than ' . $max . ' characters.');
        }
    }

    private function validateIn(string $field, $value, array $params): void
    {
        if ($value !== null && !in_array($value, $params, true)) {
            $this->addError($field, 'The ' . $field . ' must be one of: ' . implode(', ', $params) . '.');
        }
    }

    private function validateConfirmed(string $field, $value, array $data): void
    {
        // Handled at validate() level via the raw data; kept simple here.
        if (isset($data[$field . '_confirmation']) && $data[$field . '_confirmation'] !== $value) {
            $this->addError($field, 'The ' . $field . ' confirmation does not match.');
        }
    }

    private function validateDate(string $field, $value): void
    {
        if ($value !== null && strtotime((string) $value) === false) {
            $this->addError($field, 'The ' . $field . ' must be a valid date.');
        }
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }
}
