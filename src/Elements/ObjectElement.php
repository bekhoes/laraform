<?php

namespace Laraform\Elements;

class ObjectElement extends GroupElement
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'object';

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

        foreach ($this->children as $child) {
            $child->setData($this->data[$this->name]);
        }
    }

    /**
     * Get Element's data with it's key
     *
     * @return array
     */
    public function getData()
    {
        $data = [];

        foreach ($this->children as $child) {
            $data = array_merge($data, $child->getData());
        }

        return [
            $this->name => $data
        ];
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

        foreach ($this->children as $child) {
            $updates = array_merge($updates, $child->storeFiles($entity->{$this->attribute}));
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
            throw new \UnexpectedValueException('Attribute value must be an object, ' . gettype($entity) . 'given');
        }

        $files = [];

        if (!isset($entity->{$this->attribute})) {
            return $files;
        }

        foreach ($this->children as $child) {
            $files = array_merge($files, $child->originalFiles($entity->{$this->attribute}));
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

        foreach ($this->children as $child) {
            $schema['schema'][$child->name] = $child->getSchema($side);
        }

        $schema['component'] = $this->getComponent();

        return $schema;
    }

    /**
     * Determine if the element is presented in data
     *
     * @param array $data
     * @return boolean
     */
    public function presentedIn(array $data)
    {
        return array_key_exists($this->name, $data);
    }
}