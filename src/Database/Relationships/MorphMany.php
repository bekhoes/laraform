<?php

namespace Laraform\Database\Relationships;

class MorphMany extends HasMany
{
    /**
     * Name of relationship type
     *
     * @var string
     */
    public $type = 'morphMany';
}