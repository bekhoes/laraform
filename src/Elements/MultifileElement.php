<?php

namespace Laraform\Elements;

use Laraform\Database\StrategyBuilder as DatabaseBuilder;
use Laraform\Validation\StrategyBuilder as ValidatorBuilder;

class MultifileElement extends ListElement
{
    /**
     * Type of element to use as a file wrapper
     *
     * @var string
     */
    protected $fileType = 'file';

    /**
     * List of available metas for file
     *
     * @var array
     */
    protected $metas = ['extension', 'mime', 'size', 'originalName'];

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
        $schema['initial'] = 0;

        parent::__construct($schema, $options, $factory, $databaseBuilder, $validatorBuilder);

        if (!isset($this->schema['storeFile']) && $this->schemaHasAnyMeta()) {
            throw new \InvalidArgumentException('storeFile argument must be defined when using any `store` variable');
        }
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

        $schema['component'] = $this->getComponent();

        // Passing over to frontend the default
        // file url if not provided in schema
        $schema['url'] = $schema['url'] ?? $this->elementSchema()['url'];

        return $schema;
    }

    /**
     * Returns element schema
     *
     * @return array
     */
    protected function elementSchema()
    {
        $schema = array_merge([
            'type' => $this->fileType,
            'controls' => false,
        ], $this->fileAttributes());

        // Make sure we have an actual file element schema
        return $this->factory
                    ->make($schema, $this->name, $this->options)
                    ->getSchema('backend');
    }

    /**
     * Returns object schema
     *
     * @return array
     */
    protected function objectSchema()
    {
        return [
            'type' => 'listObject',
            'schema' => array_merge(
                $this->fileSchema(),
                $this->metasSchema(),
                $this->fieldsSchema()
            )
        ];
    }

    protected function fileSchema() {
        return [
            $this->schema['storeFile'] => $this->elementSchema(),
        ];
    }

    protected function fieldsSchema()
    {
        return array_key_exists('fields', $this->schema)
            ? $this->schema['fields']
            : [];
    }

    protected function metasSchema()
    {
        $schema = [];

        foreach ($this->requiredMetas() as $meta => $storeName) {
            $schema[$storeName] = [
                'type' => 'meta',
            ];
        }

        return $schema;
    }

    /**
     * Returns attributes for file field from 
     *
     * @return void
     */
    protected function fileAttributes()
    {
        $attributes = [];

        $copy = [
            'fileRules' => 'rules', 'delete', 'store', 'folder',
            'disk', 'storeSize', 'storeMime', 'storeExtension',
            'storeOriginalName', 'prunable', 'url', 'clickable',
        ];

        foreach ($copy as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if (array_key_exists($key, $this->schema)) {
                $attributes[$value] = $this->schema[$key];
            }
        }

        return $attributes;
    }

    protected function requiredMetas() {
        $required = [];

        foreach ($this->metas as $meta) {
            if (isset($this->schema['store' . ucfirst($meta)])) {
                $required[$meta] = $this->schema['store' . ucfirst($meta)];
            }
        }

        return $required;
    }

    protected function schemaHasAnyMeta() {
        return (bool) count($this->requiredMetas());
    }

    protected function initSchemaType()
    {
        $this->schemaType = isset($this->schema['storeFile']) ? 'object' : 'element';
    }

    protected function initChildSchema()
    {
        $this->childSchema = $this->isObject()
            ? $this->objectSchema()
            : $this->elementSchema();
    }
}