<?php

namespace Laraform\Contracts\File;

interface File
{
    /**
     * Physically deletes file
     *
     * @param mixed $entity
     * @return void
     */
    public function delete($entity);
}