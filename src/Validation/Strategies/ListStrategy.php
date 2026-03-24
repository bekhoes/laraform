<?php

namespace Laraform\Validation\Strategies;

use Laraform\Contracts\Validation\Validator;

class ListStrategy extends Strategy
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
        $rules = $this->rules();

        if (!empty($rules)) {
            $this->addRules($validator, $rules, $this->name());
        }

        $this->validateChildren($validator, $prefix);
    }

    /**
     * Validate child elements
     *
     * @param Validator $validator
     * @param string $prefix
     * @return void
     */
    protected function validateChildren(Validator $validator, $prefix = null)
    {
        $name = $this->name();

        $key = $prefix . $name . '.';
        
        // $this->element->setChildren(count($data[$name]));

        foreach ($this->children() as $i => $child) {
            $child->validate($validator, $key);
        }
    }
}