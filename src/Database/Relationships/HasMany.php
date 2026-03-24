<?php

namespace Laraform\Database\Relationships;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HasMany extends HasOrBelongsToMany
{
    /**
     * Name of relationship type
     *
     * @var string
     */
    public $type = 'hasMany';

    /**
     * Create new relationship
     *
     * @param integer $value
     * @param Collection $entity
     * @return Model
     */
    public function create($value, $entity, $index = null)
    {
        if ($this->hasKey($value)) {
            return $this->findInCollectionByKey($value[$this->primaryKey], $entity);
        }
        else if ($this->isIndexInCollection($index, $entity)) {
            return $this->findInCollectionByIndex($index, $entity);
        }
        else
        {
            return new $this->related;
        }
    }

    /**
     * Save relation for entity
     *
     * @param Model $entity
     * @return void
     */
    public function save(Model $entity)
    {
        $this->clean($entity);

        foreach ($this->getCollection($entity) as $item) {
            $this->hasKey($item)
                ? $this->update($entity, $item)
                : $this->insert($entity, $item);
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
        foreach ($this->getOriginalValue($entity) as $one) {
            $one->delete();
        }
    }

    /**
     * Find relation by key in collection
     *
     * @param integer $key
     * @param Collection $collection
     * @return Model
     */
    protected function findInCollectionByKey($key, Collection $collection)
    {
        if (!is_numeric($key)) {
            $key = $this->decrypt($key);
        }

        foreach ($collection as $item) {
            if ($this->keyEquals($key, $item)) {
                return $item;
            }
        }

        return new $this->related;
    }

    protected function findInCollectionByIndex($index, Collection $collection)
    {
        return isset($collection[$index])
            ? $collection[$index]
            : null;
    }

    protected function isIndexInCollection($index, Collection $collection) {
        return isset($collection[$index]);
    }

    /**
     * Remove unnecessary relations
     *
     * @param Model $entity
     * @return void
     */
    protected function clean(Model $entity)
    {
        $original = $this->getOriginalValue($entity);

        foreach ($this->getRemovableKeys($original, $entity) as $key) {
            $original->find($key)->delete();
        }
    }

    /**
     * Update relation
     *
     * @param Model $entity
     * @param Model $item
     * @return void
     */
    protected function update(Model $entity, Model $item)
    {
        $entity->{$this->attribute}()->find($item->{$this->primaryKey})->update($item->toArray());
    }

    /**
     * Insert new relation
     *
     * @param Model $entity
     * @param Model $item
     * @return void
     */
    protected function insert(Model $entity, Model $item)
    {
        $entity->{$this->attribute}()->save($item);
    }

    /**
     * Determine if value has key
     * 
     * @todo: replace array_key_exists with isset
     *
     * @param array|object $value
     * @return boolean
     */
    protected function hasKey($value)
    {
        return @array_key_exists($this->primaryKey, $value);
    }

    /**
     * Determine if key equals item's key
     *
     * @param [type] $key
     * @param Model $item
     * @return bool
     */
    protected function keyEquals($key, Model $item)
    {
        return !empty($item[$this->primaryKey])
            && $item[$this->primaryKey] == $key;
    }
}