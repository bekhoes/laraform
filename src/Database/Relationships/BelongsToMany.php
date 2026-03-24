<?php

namespace Laraform\Database\Relationships;

use Illuminate\Database\Eloquent\Model;

class BelongsToMany extends HasOrBelongsToMany
{  
    /**
     * Name of relationship type
     *
     * @var string
     */
    public $type = 'belongsToMany';

    /**
     * Create new relationship
     *
     * @param integer $value
     * @param Model $entity
     * @return Model
     */
    public function create($value, $entity)
    {
        return $this->related::find($value);
    }

    /**
     * Save relation for entity
     *
     * @param Model $entity
     * @return void
     */
    public function save(Model $entity)
    {
        if (!$this->isUpdated($entity)) {
            return;
        }

        $this->clean($entity);

        foreach ($this->getCollection($entity) as $item) {
            $entity->{$this->attribute}()->attach($item);
        }
    }

    /**
     * Remove relationship complately
     *
     * @param Model $entity
     * @return void
     */
    public function remove(Model $entity)
    {
        $this->clean($entity);
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
        return [$attribute => $value[$this->primaryKey]];
    }

    /**
     * Determine if entity is being updated
     *
     * @param Model $entity
     * @return boolean
     */
    protected function isUpdated(Model $entity)
    {
        return $this->getOriginalKeys($this->getOriginalValue($entity))->toArray()
               != $this->getCurrentKeys($entity)->toArray();
    }

    /**
     * Remove unnecessary relations
     *
     * @param Model $entity
     * @return void
     */
    protected function clean(Model $entity)
    {
        $entity->{$this->attribute}()->sync([]);
    }
}