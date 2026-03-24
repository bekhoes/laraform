<?php

namespace Laraform\Database\Relationships;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class Factory
{
    /**
     * Allowed relation types
     *
     * @var array
     */
    private $allowed = [
        'HasOne', 'HasMany', 'BelongsToMany',
        'MorphMany', 'MorphToMany', 'MorphOne',
    ];

    /**
     * Make new Relation instance
     *
     * @param Relation $relation
     * @param string $attribute
     * @return Model
     */
    public function make(Relation $relation, $attribute)
    {
        $class = __NAMESPACE__ . '\\' . $this->getClassName($relation);

        return new $class($relation, $attribute);;
    }

    /**
     * Return name of relation class
     *
     * @param Relation $relation
     * @return string
     */
    private function getClassName(Relation $relation)
    {
        $class = get_class($relation);

        preg_match('/(' . implode('|', $this->allowed) . ')+$/', $class, $matches);

        if (empty($matches)) {
            throw new \UnexpectedValueException('Unexpected relation type: ' . $class);
        }

        return $matches[0];
    }
}