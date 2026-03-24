<?php

namespace Laraform\Validation\Strategies;

use Laraform\Contracts\Validation\Validator;

class ObjectStrategy extends GroupStrategy
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
        $prefix = $prefix . $this->name() . '.';

        parent::validate($validator, $prefix);
    }
}