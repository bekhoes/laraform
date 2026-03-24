<?php

namespace Laraform\Database\Relationships;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HasOrBelongsToMany extends Relationship
{
    /**
     * Get relations on entity
     *
     * @param Model $entity
     * @return Collection
     */
    protected function getCollection(Model $entity)
    {
        return $entity->getRelations()[$this->attribute];
    }

    /**
     * Get original value on entity
     *
     * @param Model $entity
     * @return Collection
     */
    protected function getOriginalValue(Model $entity)
    {
        return get_class($entity)
            ::find($entity->{$this->primaryKey})
            ->{$this->attribute};
    }

    /**
     * Get original keys
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function getOriginalKeys(Collection $collection)
    {
        return $collection->count() == 0
            ? collect([])
            : $collection->keyBy($this->primaryKey)->keys();
    }

    /**
     * Get current keys
     *
     * @param Model $entity
     * @return Collection
     */
    protected function getCurrentKeys(Model $entity)
    {
        return $entity->{$this->attribute}->keyBy($this->primaryKey)->keys()->filter();
    }

    /**
     * Get removeable keys
     *
     * @param Collection $original
     * @param Model $entity
     * @return void
     */
    protected function getRemovableKeys(Collection $original, Model $entity)
    {
        return $this->getOriginalKeys($original)
            ->diff($this->getCurrentKeys($entity))
            ->toArray();
    } 
}