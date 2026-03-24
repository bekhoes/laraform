<?php

namespace Laraform\Database\Strategies;

use Laraform\Database\StrategyBuilder;
use Laraform\Database\Relationships\Factory as RelationshipFactory;
use Laraform\Contracts\Database\Strategy as StrategyContract;
use Laraform\Contracts\Elements\Element;

class Strategy implements StrategyContract
{
    /**
     * Element to implement the strategy for
     *
     * @var Laraform\Contracts\Elements\Element
     */
    protected $element;

    /**
     * Current entity
     *
     * @var object
     */
    protected $entity;
    
    /**
     * Relationship instance
     *
     * @var Laraform\Contracts\Database\Relationship
     */
    protected $relationship;

    /**
     * Relationship factory instance
     *
     * @var Laraform\Database\Relationships\Factory
     */
    protected $relationshipFactory;
    
    /**
     * Return new Strategy instance
     *
     * @param StrategyBuilder $builder
     * @param RelationshipFactory $relationshipFactory
     */
    public function __construct(StrategyBuilder $builder, RelationshipFactory $relationshipFactory)
    {
        $this->setElement($builder->getElement());

        $this->relationshipFactory = $relationshipFactory;
    }

    /**
     * Load value to target
     *
     * @param object $target
     * @return void
     */
    public function load($target)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Fill value to target from data
     *
     * @param object $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, array $data, $emptyOnNull = true)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Empty value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Return freshly inserted keys
     *
     * @param object $target
     * @return array
     */
    public function getNewKeys($target)
    {
        throw new \BadMethodCallException('Unimplemented method');
    }

    /**
     * Save relations
     *
     * @return void
     */
    public function saveRelations()
    {
        if ($this->isRelational() && $this->hasRelationship()) {
            $this->relationship->save($this->entity);
        }

        $this->saveChildrenRelations();
    }

    /**
     * Remove relations
     *
     * @return void
     */
    public function removeRelations()
    {
        $this->initRelationship();
        
        $this->relationship->remove($this->entity);
    }

    /**
     * Prepare value for loading
     *
     * @param Element $element
     * @return mixed
     */
    protected function prepare(Element $element)
    {
        if (!$this->isRelational()) {
            return $this->value();
        }

        if (!$this->hasRelationship()) {
            $this->initRelationship();
        }

        return $this->relationship->prepare(
            $this->value()[$element->attribute],
            $element->attribute
        );
    }

    /**
     * Create new relation
     *
     * @param mixed $value
     * @param object $entity
     * @param integer $index
     * @return void
     */
    protected function createRelation($value = null, $entity = null, $index = null)
    {
        if (!$this->hasRelationship()) {
            $this->initRelationship();
        }

        return $this->relationship->create(
            $value ?: $this->value(),
            $entity ?: $this->entity,
            $index
        );
    }

    /**
     * Save relations of children
     *
     * @return void
     */
    protected function saveChildrenRelations()
    {
        // unimplemented on purpose
    }

    /**
     * Get relations of element
     *
     * @return array
     */
    protected function getRelations()
    {
        return $this->getEntityRelations()[$this->attribute()];
    }

    /**
     * Get all relations of entity
     *
     * @return array
     */
    protected function getEntityRelations()
    {
        return $this->entity->getRelations();
    }

    /**
     * Determine if element has relations on entity
     *
     * @return boolean
     */
    protected function hasRelations()
    {
        return array_key_exists($this->attribute(), $this->getEntityRelations());
    }

    /**
     * Set relationship instance
     *
     * @return void
     */
    protected function initRelationship()
    {
        $this->relationship = $this->makeRelationship();
    }

    /**
     * Make new relationship instance
     *
     * @return Relationship
     */
    protected function makeRelationship()
    {
        return $this->relationshipFactory->make(
            $this->entity->{$this->attribute()}(),
            $this->attribute()
        );
    }

    /**
     * Determine if this has relationship
     *
     * @return boolean
     */
    protected function hasRelationship()
    {
        return $this->relationship !== null;
    }

    /**
     * Determine if relational
     * 
     * Checks if the current attribute name is a method on the model
     *
     * @return boolean
     */
    protected function isRelational()
    {
        return $this->hasEntity() && method_exists($this->entity, $this->attribute());
    }

    /**
     * Determine if this has entity
     *
     * @return boolean
     */
    protected function hasEntity()
    {
        return $this->entity !== null;
    }

    /**
     * Set value for Element
     *
     * @param Element $element
     * @return void
     */
    protected function setElement(Element $element)
    {
        $this->element = $element;
    }

    /**
     * Return children of element
     *
     * @return Laraform\Contracts\Elements\Element[]
     */
    protected function children()
    {
        return $this->element->children;
    }

    /**
     * Return value of element from entity
     *
     * @return mixed
     */
    protected function value()
    {
        return $this->entity[$this->attribute()];
    }

    /**
     * Return attribute of element
     *
     * @return string
     */
    protected function attribute()
    {
        return $this->element->attribute;
    }

    /**
     * Return name of element
     *
     * @return string
     */
    protected function name()
    {
        return $this->element->name;
    }
}