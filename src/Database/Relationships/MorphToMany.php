<?php

namespace Laraform\Database\Relationships;

class MorphToMany extends BelongsToMany
{
    /**
     * Name of relationship type
     *
     * @var string
     */
    public $type = 'morphToMany';
}