<?php

namespace Laraform\Translator;

use Laraform\Contracts\Translator\Translator;

class Dimsav implements Translator
{
    /**
     * Fill translation to target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @param array $data
     * @param string $name - key of current on data
     * @return void
     */
    public function load($target, $attribute)
    {
        $data = [];

        foreach ($target->translations as $translation) {
            $data[$translation['locale']] = $translation[$attribute];
        }

        return $data;
    }

    /**
     * Load translated value from target
     *
     * @param mixed $target
     * @param string $attribute - key of current on target
     * @return void
     */
    public function fill($target, $attribute, $data, $name)
    {
        foreach ($data[$name] as $iso => $value) {
            $target->fill([$attribute.':'.$iso => $value]);
        }
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
        // @todo: to be implemented
    }
}