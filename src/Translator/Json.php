<?php

namespace Laraform\Translator;

use Laraform\Contracts\Translator\Translator;

class Json implements Translator
{
    /**
     * Load translated value from target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @return void
     */
    public function load($target, $attribute)
    {
        return json_decode($target->$attribute, true);
    }

    /**
     * Fill translation to target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @param array $data
     * @param string $name - key of current on data
     * @return void
     */
    public function fill($target, $attribute, $data, $name)
    {
        $target->$attribute = json_encode($data[$name], true);
    }

    /**
     * Remove translation value target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @return void
     */
    public function empty($target, $attribute)
    {
        $target->$attribute = null;
    }
}