<?php

namespace Laraform\Contracts\Translator;

interface Translator
{
    /**
     * Load translated value from target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @return void
     */
    public function load($target, $attribute);

    /**
     * Fill translation to target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @param array $data
     * @param string $name - key of current on data
     * @return void
     */
    public function fill($target, $attribute, $data, $name);

    /**
     * Remove translation value target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @return void
     */
    public function empty($target, $attribute);
}