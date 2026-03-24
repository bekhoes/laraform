<?php

namespace Laraform\Elements;

class GroupElement extends CollectionElement
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'group';

    /**
     * Get schema array
     *
     * @param string $side
     * @return array
     */
    public function getSchema($side)
    {
        $schema = $this->schema;

        foreach ($this->children as $child) {
            $schema['schema'][$child->name] = $child->getSchema($side);
        }

        $schema['component'] = $this->getComponent();

        return $schema;
    }

    /**
     * Set children based on schema
     *
     * @return void
     */
    protected function setChildren()
    {
        foreach ($this->childrenSchema() as $name => $schema) {
            $this->addChild($this->makeChild($schema, $name));
        }
    }

    /**
     * Returns children schema
     *
     * @return void
     */
    protected function childrenSchema()
    {
        return $this->schema['schema'];
    }

    /**
     * Initalize class properties
     *
     * @return void
     */
    protected function initProperties()
    {
        $this->name = $this->schema['name'] ?? $this->name;
        $this->attribute = $this->schema['attribute'] ?? $this->name;
        $this->persist = $this->schema['persist'] ?? $this->persist;
    }
}