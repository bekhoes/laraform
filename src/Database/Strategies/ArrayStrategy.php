<?php

namespace Laraform\Database\Strategies;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ArrayStrategy extends Strategy
{
    /**
     * Load value to target
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return void
     */
    public function load($target)
    {
        $this->entity = $target;

        if ($this->isRelational()) {
            return $this->loadRelations();
        }

        return [
            $this->name() => $this->entity->{$this->attribute()}
        ];
    }

    /**
     * Fill value to target from data
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, array $data, $emptyOnNull = true)
    {
        $this->entity = $target;

        if ($this->isRelational()) {
            return $this->addRelations($data, $target, $emptyOnNull);
        }

        $value = $data[$this->name()];

        $this->entity->{$this->attribute()} = $value;
    }

    /**
     * Empty value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        $this->entity = $target;

        if ($this->value() === null) {
            return;
        }

        if ($this->isRelational()) {
            return $this->removeRelations();
        }

        $this->entity->{$this->attribute()} = null;
    }

    /**
     * Remove relations
     *
     * @return void
     */
    public function removeRelations()
    {
        $this->initRelationship();

        $this->entity->setRelation($this->name(), new Collection);

        $this->relationship->remove($this->entity);
    }

    /**
     * Return freshly inserted keys
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return array
     */
    public function getNewKeys($target)
    {
        return;
    }

    /**
     * Load children
     *
     * @return array
     */
    protected function loadRelations()
    {
        $data = [];

        foreach ($this->value() as $entity) {
            $data[] = $entity->getKey();
        }

        return [
            $this->name() => $data
        ];
    }

    /**
     * Add relations to current entity
     *
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    protected function addRelations(array $data, $target, $emptyOnNull = true)
    {
        $this->initRelationship();

        $collection = $this->fillRelation(new Collection, $data[$this->name()], $emptyOnNull);

        $this->entity->setRelation($this->attribute(), $collection);
    }

    /**
     * Fill relation with data
     *
     * @param Collection $collection
     * @param array $data
     * @param boolean $emptyOnNull
     * @return Collection
     */
    protected function fillRelation(Collection $collection, array $data, $emptyOnNull = true)
    {
        foreach ($data as $index => $value) {
           $collection[$index] = $this->createRelation($value, $this->value(), $index);
        }

        return $collection;
    }
}