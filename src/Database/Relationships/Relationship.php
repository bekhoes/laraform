<?php

namespace Laraform\Database\Relationships;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Laraform\Contracts\Database\Relationship as RelationshipContract;
use Laraform\Support\Hash;

class Relationship implements RelationshipContract
{
    /**
     * Related class instance
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    public $related;

    /**
     * Primary key of related class
     *
     * @var string
     */
    public $primaryKey;

    /**
     * Attribute which connects the related class to parent
     *
     * @var string
     */
    public $attribute;

    /**
     * Return new Relationship instance
     *
     * @param Relation $relation
     * @param string $attribute
     */
    public function __construct(Relation $relation, $attribute)
    {
        $this->related = $relation->getRelated();
        $this->primaryKey = $this->related->getKeyName();
        $this->attribute = $attribute;
    }

    /**
     * Create new relationship
     *
     * @param integer $value
     * @param object $entity
     * @return Model
     */
    public function create($value, $entity)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Save relation for entity
     *
     * @param Model $entity
     * @return void
     */
    public function save(Model $entity)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Prepare value for loading
     *
     * @param array $value - array containing current element value
     * @param string $attribute - key of element on value
     * @return array
     */
    public function prepare($value, $attribute)
    {
        return $value;
    }

    /**
     * Decrypt key
     *
     * @param string $key
     * @return integer
     */
    public function decrypt($key)
    {
        if ($key === null) {
            return $key;
        }

        return Hash::decode($key);
    }
}