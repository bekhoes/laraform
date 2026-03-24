<?php

namespace Laraform\Elements;

// Not intended for direct use

class ListObjectElement extends ObjectElement
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'listObject';

    /**
     * Defines how the element should be validated
     *
     * @var string
     */
    public $validateAs = 'object';

    /**
     * Store related files and returns it's filenames
     *
     * @param mixed $entity
     * @return array
     */
    public function storeFiles($entity)
    {
        if ($entity === null) {
            return;
        }

        if (!$entity instanceof \Countable && !is_array($entity)) {
            throw new \UnexpectedValueException('Attribute value must be an array or instance of Countable');
        }

        $updates = [];

        foreach ($this->children as $child) {
            $updates = array_merge($updates, $child->storeFiles($entity[$this->attribute]));
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
        $files = [];

        if (!isset($entity[$this->attribute])) {
            return $files;
        }

        foreach ($this->children as $child) {
            $files = array_merge($files, $child->originalFiles($entity[$this->attribute]));
        }

        return $files;
    }
}