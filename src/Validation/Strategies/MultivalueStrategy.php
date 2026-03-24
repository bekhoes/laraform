<?php

namespace Laraform\Validation\Strategies;

use Illuminate\Validation\Rule;
use Laraform\Contracts\Validation\Validator;

class MultivalueStrategy extends Strategy
{
    /**
     * Validate element
     *
     * @param Validator $validator
     * @param string $prefix
     * @return void
     */
    public function validate(Validator $validator, $prefix = null)
    {
        $name = $prefix . $this->name();

        foreach ($this->element->value() as $key => $value) {
            $this->addRules($validator, $this->rules(), $name . '.' . $key);
            $this->addMessages($validator, $this->rules(), $name . '.' . $key);
        }
    }
}