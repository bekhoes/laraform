<?php

namespace Laraform\File;

use Illuminate\Support\Facades\Storage;
use Laraform\Contracts\File\File as FileContract;

class File implements FileContract
{
    /**
     * Path to file
     *
     * @var string
     */
    public $path;

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
     * Custom file delete method
     *
     * @var Callable
     */
    protected $delete;

    /**
     * Whether the file should be removed on data removal
     *
     * @var boolean
     */
    protected $prunable;

    /**
     * Initalizes File
     *
     * @param string $path
     * @param array $options
     */
    public function __construct($path, $options = [])
    {
        $this->path = $path;

        $this->setOptions($options);
    }

    /**
     * Sets option properties from options
     *
     * @param array $options
     * @return void
     */
    protected function setOptions($options) {
        $this->disk = $options['disk'] ?? config('laraform.store.disk');
        $this->delete = $options['delete'] ?? null;
        $this->folder = $options['folder'] ?? null;
        $this->prunable = $options['prunable'];
    }

    /**
     * Permorms physical file delete
     *
     * @param mixed $entity
     * @return void
     */
    public function delete($entity)
    {
        if (!$this->prunable) {
            return;
        }

        if ($this->delete) {
            ($this->delete)($this->path, $entity);
            return;
        }

        Storage::disk($this->disk)
            ->delete($this->path);
    }
}