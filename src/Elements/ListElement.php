<?php

namespace Laraform\Elements;

use Laraform\Database\StrategyBuilder as DatabaseBuilder;
use Laraform\Validation\StrategyBuilder as ValidatorBuilder;
use Laraform\Support\Json;
use Laraform\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class ListElement extends Element
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'list';

    /**
     * Schema template of a children
     *
     * @var array
     */
    protected $childSchema;

    /**
     * Type of children
     *
     * @var string
     */
    protected $schemaType;

    /**
     * Return new Element instance
     *
     * @param array $schema
     * @param array $options
     * @param Factory $factory
     * @param DatabaseBuilder $databaseBuilder
     * @param ValidatorBuilder $validatorBuilder
     */
    public function __construct($schema, $options, Factory $factory, DatabaseBuilder $databaseBuilder, ValidatorBuilder $validatorBuilder)
    {
        parent::__construct($schema, $options, $factory, $databaseBuilder, $validatorBuilder);

        $this->initSchemaType();
        $this->initChildSchema();
    }

    /**
     * Set Element level data from data array
     *
     * @param array $data
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;

        if (!isset($this->data[$this->name])) {
            return;
        }

        $this->setChildren(count($this->data[$this->name]));

        foreach ($this->children as $child) {
            $child->setData($this->data[$this->name]);
        }
    }

    /**
     * Convert Element's data to validation format
     *
     * @return mixed
     */
    public function getValidationData()
    {
        $data = [];

        foreach ($this->children as $child) {
            $data = array_merge($data, $child->getValidationData());
        }

        return [
            $this->name => $data
        ];
    }

    /**
     * Store related files and returns it's filenames
     *
     * @param mixed $entity
     * @return array
     */
    public function storeFiles($entity)
    {
        if (!is_object($entity)) {
            throw new \UnexpectedValueException('Attribute value must be an object, ' . gettype($entity) . 'given');
        }

        $updates = [];

        foreach ($this->children as $index => $child) {
            $update = $child->storeFiles($entity->{$this->attribute});

            if ($update) {
              $updates[$index] = $update[$index];
            }
        }

        if (empty($updates)) {
            return $updates;
        }

        return [
            $this->name => $updates
        ];
    }

    /**
     * Returns all files on entity
     *
     * @param mixed $entity
     * @return array
     */
    public function originalFiles($entity)
    {
        if (!is_object($entity)) {
            throw new \UnexpectedValueException('Attribute value must be an object, ' . gettype($entity) . ' given');
        }

        $files = [];

        $originalCollection = $entity->{$this->attribute} ?? [];

        if (!is_array($originalCollection) && !$originalCollection instanceof Collection && !$originalCollection instanceof EloquentCollection) {
            throw new \UnexpectedValueException('Attribute value must be an array, ' . gettype($originalCollection) . ' given');
        }

        foreach ($originalCollection as $index => $one) {
            $files = array_merge($files, $this->prototype($index)->originalFiles($originalCollection));
        }

        return $files;
    }

    /**
     * Returns all files based on current data
     *
     * @return array
     */
    public function currentFiles()
    {
        $files = [];

        foreach ($this->children as $child) {
            $files = array_merge($files, $child->currentFiles());
        }

        return $files;
    }

    /**
     * Get schema array
     *
     * @param string $side
     * @return array
     */
    public function getSchema($side)
    {
        $schema = $this->schema;

        $schema = $this->correctRules($side, $schema);

        $protoSchema = $this->prototype()->getSchema($side);
        $schema[$this->schemaType] = $this->isObject()
          ? array_merge($protoSchema, ['type' => 'object', 'component' => 'object-element'])
          : $protoSchema;

        $schema['component'] = $this->getComponent();

        return $schema;
    }

    /**
     * Determine if element has rules
     *
     * @param string $side
     * @return boolean
     */
    public function hasRules($side = 'backend')
    {
        return parent::hasRules($side) || $this->prototype()->hasRules($side);
    }

    /**
     * Return a prototype child Element
     *
     * @return Element
     */
    public function prototype($index = null)
    {
        return $this->factory->make($this->childSchema, $index, $this->options);
    }

    /**
     * Set children $count times
     *
     * @param integer $count
     * @return void
     */
    public function setChildren($count)
    {
        $this->children = [];

        for ($i = 0; $i < $count; $i++) {
            $this->children[$i] = $this->factory->make($this->childSchema, $i, $this->options);
        }
    }

    /**
     * Determine if children are objects
     * (or simple elements)
     *
     * @return boolean
     */
    public function isObject()
    {
        return $this->schemaType == 'object';
    }

    /**
     * Inits schema type
     *
     * @return void
     */
    protected function initSchemaType()
    {
        $this->schemaType = isset($this->schema['object']) ? 'object' : 'element';
    }

    /**
     * Inits child schema
     *
     * @return void
     */
    protected function initChildSchema()
    {
        $this->childSchema = $this->isObject()
          ? array_merge($this->schema['object'], [
            'type' => 'listObject'
          ])
          : $this->schema['element'];
    }
}