<?php

namespace Laraform\Database\Strategies;

class MultilingualStrategy extends DefaultStrategy
{
    /**
     * Translator instance
     *
     * @var \Laraform\Contracts\Translator\Translator
     */
    private $translator;

    /**
     * Load value to target
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @return void
     */
    public function load($target)
    {
        return [
            $this->name() => $this->translator()->load($target, $this->attribute())
        ];
    }

    /**
     * Fill value to target from data
     *
     * @param Illuminate\Database\Eloquent\Model $target
     * @param array $data
     * @param boolean $emptyOnNull
     * @return void
     */
    public function fill($target, array $data, $emptyOnNull = true)
    {
        $this->translator()->fill($target, $this->attribute(), $data, $this->name());
    }

    /**
     * Empty value on target
     *
     * @param object $target
     * @return void
     */
    public function empty($target)
    {
        $this->translator()->empty($target, $this->attribute());
    }

    /**
     * Return element translator instance
     *
     * @return \Laraform\Contracts\Translator\Translator
     */
    protected function translator()
    {
        if (!$this->translator) {
            $this->translator = app($this->element->translator);
        }

        return $this->translator;
    }
}