<?php

declare(strict_types=1);

namespace Tavp\Core\Http;

use Tavp\Core\Validation\Validator;

/**
 * Base class for validated requests.
 *
 * Subclasses declare rules() and optionally messages(). When used in a
 * controller, calling validate() automatically checks the input and
 * returns a 422 response with field errors if it fails.
 */
abstract class FormRequest
{
    protected Request $request;
    protected Validator $validator;

    public function __construct()
    {
        $this->request = new Request();
        $this->validator = new Validator();
    }

    /**
     * Return the validation rules as a field => rules map.
     */
    abstract public function rules(): array;

    /**
     * Optional custom error messages.
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Run validation. Returns true when the input is valid.
     * On failure, the errors are available via errors().
     */
    public function validate(): bool
    {
        return $this->validator->validate($this->request->input('') ?: $_POST, $this->rules());
    }

    /**
     * Validated input (only the keys present in the rules).
     */
    public function validated(): array
    {
        return $this->request->only(array_keys($this->rules()));
    }

    public function errors(): array
    {
        return $this->validator->errors();
    }

    public function fails(): bool
    {
        return $this->validator->fails();
    }
}
