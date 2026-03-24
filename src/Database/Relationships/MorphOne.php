<?php

namespace Laraform\Database\Relationships;

class MorphOne extends HasOne
{
    /**
     * Name of relationship type
     *
     * @var string
     */
    public $type = 'morphOne';
}