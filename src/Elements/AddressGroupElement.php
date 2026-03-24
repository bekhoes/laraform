<?php

namespace Laraform\Elements;

class AddressGroupElement extends GroupElement
{
    private $fields = [
        'address' => [
            'type' => 'text',
        ],
        'address2' => [
            'type' => 'text',
        ],
        'zip' => [
            'type' => 'text',
        ],
        'city' => [
            'type' => 'text',
        ],
        'state' => [
            'type' => 'text',
        ],
        'country' => [
            'type' => 'text',
        ],
    ];

    protected function childrenSchema()
    {
        return $this->schema['fields'] ?? $this->fields;
    }
}