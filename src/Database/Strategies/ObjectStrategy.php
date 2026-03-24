<?php

namespace Laraform\Database\Strategies;

use Illuminate\Database\Eloquent\Model;

class ObjectStrategy extends Strategy
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
            return $this->loadChildren($this->value());
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
            return $this->addRelations($data, $emptyOnNull);
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

        $this->emptyChildren();

        if ($this->isRelational()) {
            return $this->removeRelations();
        }

        $this->entity->{$this->attribute()} = null;
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
        foreach ($this->children() as $child) {
            $childKeys = $child->getNewKeys($this->value());

            if (!empty($childKeys)) {
                $keys[$child->attribute] = $childKeys;
            }
        }

        return $keys;
    }

    /**
     * Load children
     *
     * @param Model $target
     * @return void
     */
    protected function loadChildren(Model $target = null)
    {
        $data = [];

        foreach ($this->children() as $child) {
            $data = array_merge($data, $child->load($target));
        }

        return [
            $this->name() => $data
        ];
    }

    /**
     * Fill relation with data
     *
     * @param Model $relation
     * @param array $data
     * @param boolean $emptyOnNull
     * @return Collection
     */
    protected function fillRelation(Model $relation, array $data, $emptyOnNull = true)
    {
        foreach ($this->children() as $child) {
            $child->fill($relation, $data, $emptyOnNull);
        }

        return $relation;
    }

    /**
     * Add relations to current entity
     *
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    protected function addRelations(array $data, $emptyOnNull = true)
    {
        $relation = $this->createRelation();

        $this->fillRelation($relation, $data[$this->name()], $emptyOnNull);

        $this->entity->setRelation($this->attribute(), $relation);
    }

    /**
     * Save children relations
     *
     * @return void
     */
    protected function saveChildrenRelations()
    {
        foreach ($this->children() as $child) {
            $child->saveRelations();
        }
    }

    /**
     * Empty children
     *
     * @return void
     */
    protected function emptyChildren()
    {
        foreach ($this->children() as $child) {
            $child->empty($this->value());
        }
    }

    /**
     * Return value of element from entity
     *
     * @return mixed
     */
    protected function value()
    {
        return $this->isRelational()
          ? $this->entity->{$this->attribute()}()->withDefault(false)->first()
          : $this->entity[$this->attribute()];
    }
}