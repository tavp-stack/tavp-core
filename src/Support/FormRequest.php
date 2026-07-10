<?php

declare(strict_types=1);

namespace Tavp\Core\Support;

/**
 * Form request validation — validate input data with rules.
 */
class FormRequest
{
    private array $errors = [];
    private array $data = [];

    /**
     * Set data to validate.
     */
    public function setData(array $data): void
    {
        $this->data = $data;
        $this->errors = [];
    }

    /**
     * Validate data against rules.
     */
    public function validate(array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Get validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message.
     */
    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }
        return null;
    }

    /**
     * Check if validation passed.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed.
     */
    public function fails(): bool
    {
        return !$this->passes();
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $params = [];
        if (str_contains($rule, ':')) {
            [$ruleName, $paramsStr] = explode(':', $rule, 2);
            $rule = $ruleName;
            $params = explode(',', $paramsStr);
        }

        match ($rule) {
            'required' => $this->validateRequired($field, $value),
            'string' => $this->validateString($field, $value),
            'integer' => $this->validateInteger($field, $value),
            'email' => $this->validateEmail($field, $value),
            'min' => $this->validateMin($field, $value, (int)($params[0] ?? 0)),
            'max' => $this->validateMax($field, $value, (int)($params[0] ?? 255)),
            'confirmed' => $this->validateConfirmed($field, $value),
            'unique' => $this->validateUnique($field, $value, $params[0] ?? '', $params[1] ?? 'id'),
            default => null,
        };
    }

    private function validateRequired(string $field, mixed $value): void
    {
        if ($value === null || $value === '') {
            $this->errors[$field][] = "{$field} is required.";
        }
    }

    private function validateString(string $field, mixed $value): void
    {
        if ($value !== null && !is_string($value)) {
            $this->errors[$field][] = "{$field} must be a string.";
        }
    }

    private function validateInteger(string $field, mixed $value): void
    {
        if ($value !== null && !ctype_digit((string)$value)) {
            $this->errors[$field][] = "{$field} must be an integer.";
        }
    }

    private function validateEmail(string $field, mixed $value): void
    {
        if ($value !== null && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$field} must be a valid email address.";
        }
    }

    private function validateMin(string $field, mixed $value, int $min): void
    {
        if (is_string($value) && strlen($value) < $min) {
            $this->errors[$field][] = "{$field} must be at least {$min} characters.";
        }
    }

    private function validateMax(string $field, mixed $value, int $max): void
    {
        if (is_string($value) && strlen($value) > $max) {
            $this->errors[$field][] = "{$field} must not exceed {$max} characters.";
        }
    }

    private function validateConfirmed(string $field, mixed $value): void
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;

        if ($value !== $confirmValue) {
            $this->errors[$field][] = "{$field} confirmation does not match.";
        }
    }

    private function validateUnique(string $field, mixed $value, string $table, string $column): void
    {
        // Placeholder for database uniqueness check
        // In production, this would query the database
    }
}
