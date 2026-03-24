<?php

namespace Laraform\Database\Strategies;

class ListObjectStrategy extends ObjectStrategy
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

        return $this->loadChildren($target);
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

        $this->fillRelation($target, $data[$this->name()], $emptyOnNull);
    }

    /**
     * Save relations
     *
     * @return void
     */
    public function saveRelations()
    {
        $this->saveChildrenRelations();
    }

    /**
     * Return freshly inserted keys
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return array
     */
    public function getNewKeys($target)
    {
        $keys = [];
        foreach ($this->children() as $child) {
            $childKeys = $child->getNewKeys($target);

            if (!empty($childKeys)) {
                $keys[$child->attribute] = $childKeys;
            }
        }

        return $keys;
    }
}