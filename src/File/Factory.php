<?php

namespace Laraform\File;

use Laraform\File\File;

class Factory
{
    /**
     * Returns new File instance
     * 
     * @param string $path
     * @param array $options
     * @return Laraform\Contracts\File\File
     */
    public function make($path, $options)
    {
        return app()->makeWith(File::class, compact('path', 'options'));
    }
}