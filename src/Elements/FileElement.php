<?php

namespace Laraform\Elements;

use Laraform\File\Factory as FileFactory;
use Illuminate\Http\UploadedFile;

class FileElement extends Element
{
    /**
     * Disk to store file
     *
     * @var string
     */
    protected $disk;

    /**
     * Folder to store / view file
     *
     * @var string
     */
    protected $folder;

    /**
     * Custom file store method
     *
     * @var Callable
     */
    protected $store;

    /**
     * Custom file delete method
     *
     * @var Callable
     */
    protected $delete;

    /**
     * Name of field where Size should be stored
     *
     * @var string
     */
    protected $storeSize;

    /**
     * Name of field where MimeType should be stored
     *
     * @var string
     */
    protected $storeMime;

    /**
     * Name of field where Extension should be stored
     *
     * @var string
     */
    protected $storeExtension;

    /**
     * Name of field where Original Name should be stored
     *
     * @var string
     */
    protected $storeOriginalName;

    /**
     * Whether the files should be physically deleted when data is removed
     *
     * @var boolean
     */
    protected $prunable = true;

    /**
     * Determines if the element should be validated
     *
     * @return boolean
     */
    public function shouldValidate()
    {
        if (!$this->presentedIn($this->data) || !$this->hasRules()) {
            return false;
        }

        $value = $this->data[$this->name];

        if (empty($value)) {
            return true;
        }

        return $this->isFile();
    }

    /**
     * Fill element data to target
     *
     * @param object $target
     * @param array $data
     * @param boolean $$emptyOnNull
     * @return void
     */
    public function fill($target, $data, $emptyOnNull = true)
    {
        if ($this->isFile()) {
            return;
        }

        parent::fill($target, $data, $emptyOnNull);
    }

    /**
     * Get schema array
     *
     * @param string $side
     * @return array
     */
    public function getSchema($side)
    {
        $schema = parent::getSchema($side);

        $schema['url'] = $this->getUrl();

        return $schema;
    }

    /**
     * Store related files and returns it's filenames
     *
     * @param mixed $entity
     * @return array
     */
    public function storeFiles($entity)
    {
        if (!$this->presentedIn($this->data) || !$this->isFile()) {
            return [];
        }
        
        $file = $this->data[$this->name];

        $return = $this->hasCustomStore()
            ? ($this->store)($file, $entity)
            : $this->defaultStore($file);

        $this->updateValue($return[$this->name]);

        return $return;
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

        if ((is_array($entity) && !isset($entity[$this->attribute]))
            || (!is_array($entity) && !isset($entity->{$this->attribute}))) {
            return $files;
        }

        $path = is_array($entity)
            ? $entity[$this->attribute]
            : $entity->{$this->attribute};

        if (!$path) {
            return $files;
        }

        $files[] = $this->makeFile($path);

        return $files;
    }

    /**
     * Returns all files based on current data
     *
     * @return array
     */
    public function currentFiles()
    {
        if (!$this->presentedIn($this->data)) {
            return [];
        }
        
        if ($this->isFile()) {
            return [];
        }

        $files = [];

        $path = $this->data[$this->name];

        if (!$path) {
            return $files;
        }

        $files[] = $this->makeFile($path);

        return $files;
    }

    /**
     * Preforms default file storing process
     *
     * @param UploadedFile $file
     * @return array
     */
    protected function defaultStore(UploadedFile $file)
    {
        $path = $file->storeAs(
            $this->storeFolder(),
            $file->hashName(),
            ['disk' => $this->storeDisk()]
        );

        $return = [
            $this->name => $path
        ];

        if ($this->storeSize) {
            $return[$this->storeSize] = $file->getClientSize();
        }

        if ($this->storeMime) {
            $return[$this->storeMime] = $file->getClientMimeType();
        }

        if ($this->storeExtension) {
            $return[$this->storeExtension] = $file->guessClientExtension();
        }

        if ($this->storeOriginalName) {
            $return[$this->storeOriginalName] = $file->getClientOriginalName();
        }

        return $return;
    }

    /**
     * Returns store folder
     *
     * @return string
     */
    protected function storeFolder()
    {
        return $this->folder ?: config('laraform.store.folder');
    }

    /**
     * Returns store disk
     *
     * @return string
     */
    protected function storeDisk()
    {
        return $this->disk
            ? $this->disk
            : config('laraform.store.disk');
    }

    /**
     * Determines if the field has custom storing method
     *
     * @return boolean
     */
    protected function hasCustomStore()
    {
        return (boolean) $this->store;
    }

    /**
     * Determines is the value is an actual file
     *
     * @return boolean
     */
    protected function isFile()
    {
        if (!$this->presentedIn($this->data)) {
            return false;
        }

        $value = $this->data[$this->name];

        return is_object($value) && $value instanceof UploadedFile;
    }

    /**
     * Makes a new file instance
     *
     * @param string $filename
     * @return Laraform\Contracts\File\File
     */
    public function makeFile($filename)
    {
        return $this->fileFactory->make($filename, $this->fileOptions());
    }

    /**
     * Returns options for file to make
     *
     * @return array
     */
    public function fileOptions()
    {
        $options = [
            'prunable' => $this->prunable
        ];

        if ($this->delete) {
            $options['delete'] = $this->delete;
        }

        if ($this->folder) {
            $options['folder'] = $this->folder;
        }

        if ($this->disk) {
            $options['disk'] = $this->disk;
        }

        return $options;
    }

    /**
     * Initalize class properties
     *
     * @return void
     */
    protected function initProperties()
    {
        parent::initProperties();

        $this->disk = $this->schema['disk'] ?? $this->disk;
        $this->folder = $this->schema['folder'] ?? $this->folder;
        $this->store = $this->schema['store'] ?? $this->store;
        $this->delete = $this->schema['delete'] ?? $this->delete;
        $this->storeSize = $this->schema['storeSize'] ?? $this->storeSize;
        $this->storeMime = $this->schema['storeMime'] ?? $this->storeMime;
        $this->storeExtension = $this->schema['storeExtension'] ?? $this->storeExtension;
        $this->storeOriginalName = $this->schema['storeOriginalName'] ?? $this->storeOriginalName;
        $this->prunable = $this->schema['prunable'] ?? $this->prunable;

        $this->fileFactory = new FileFactory;
    }
    
    protected function getUrl() {
      return $this->schema['url'] ?? config("filesystems.disks.{$this->storeDisk()}.url");
    }
}