<?php

namespace Laraform\Database\Strategies;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ListStrategy extends Strategy
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
            return $this->loadChildren();
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

        // $this->setChildren($value);

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

        // $this->emptyChildren();

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
        
        if (count($this->relationship->related->getRelations()) > 0) {
          $this->relationship->remove($this->entity);
        }
    }

    /**
     * Return freshly inserted keys
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return array
     */
    public function getNewKeys($target)
    {
        $this->entity = $target;

        if (!$this->isRelational()) {
            return;
        }

        $keys = [];
        foreach ($this->children() as $index => $child) {
            $childKeys = $child->getNewKeys($this->value()[$index]);

            if (!empty($childKeys)) {
                $keys[$index] = $childKeys;
            }
        }

        return $keys;
    }

    /**
     * Load children
     *
     * @return array
     */
    protected function loadChildren()
    {
        $this->removeChildren();

        $this->setChildren($this->value());

        $data = [];
        foreach ($this->children() as $child) {
            $data = array_merge($data, $child->load($this->prepare($child)));
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

        // $this->setChildren($data[$this->name()]);

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
        foreach ($this->children() as $index => $child) {

            $relation = $this->createRelation($data[$index], $this->value(), $index);

            if ($this->isObject()) {
                $child->fill($relation, $data, $emptyOnNull);
            }

            $collection[$index] = $relation;
        }

        return $collection;
    }

    

    /**
     * Save children relations
     *
     * @return void
     */
    protected function saveChildrenRelations()
    {
        if (!$this->isObject() || !$this->isRelational()) {
            return;
        }

        foreach ($this->getRelations() as $index => $one) {
            $this->children()[$index]->saveRelations();
        }
    }

    /**
     * Empty children
     *
     * @return void
     */
    protected function emptyChildren()
    {
        $this->setChildren($this->value());

        foreach ($this->children() as $child) {
            $child->empty($this->value());
        }

        $this->removeChildren();
    }

    /**
     * Set value of children
     *
     * @param array|Collection $children
     * @return void
     */
    protected function setChildren($children)
    {
        if (!is_array($children)) {
            $children = $children->toArray();
        }

        $this->element->setChildren(count($children));
    }

    /**
     * Remove children
     *
     * @return void
     */
    protected function removeChildren()
    {
        $this->element->setChildren(0);
    }

    /**
     * Determine if the list is object type
     *
     * @return boolean
     */
    protected function isObject()
    {
        return $this->element->isObject();
    }
}