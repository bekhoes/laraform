<?php

namespace Laraform\Database\Relationships;

use Illuminate\Database\Eloquent\Model;

class HasOne extends Relationship
{
    /**
     * Name of relationship type
     *
     * @var string
     */
    public $type = 'hasOne';

    /**
     * Create new relationship
     *
     * @param integer $value
     * @param Model $entity
     * @return Model
     */
    public function create($value, $entity)
    {
        return $value ?: new $this->related;
    }

    /**
     * Save relation for entity
     *
     * @param Model $entity
     * @return void
     */
    public function save(Model $entity)
    {
        $relation = $this->getRelation($entity);

        if (empty($relation)) {
            // $this->remove($entity);
            return;
        }
        
        $entity->{$this->attribute}()->save($relation);
    }

    /**
     * Remove relationship complately
     *
     * @param Model $entity
     * @return void
     */
    public function remove(Model $entity)
    {
        $entity->{$this->attribute}()->delete();
    }

    /**
     * Get current element's relations on entity
     *
     * @param Model $entity
     * @return array
     */
    protected function getRelation(Model $entity)
    {
        $relations = $entity->getRelations();

        return isset($relations[$this->attribute])
            ? $relations[$this->attribute]
            : [];
    }
}