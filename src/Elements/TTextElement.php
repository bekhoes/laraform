<?php

namespace Laraform\Elements;

class TTextElement extends Element
{
    /**
     * Defines how the element behaves on a transaction level
     *
     * @var string
     */
    public $behaveAs = 'multilingual';
    
    /**
     * Translator class
     *
     * @var string
     */
    public $translator = \Laraform\Translator\Json::class;

    /**
     * Return rules for side
     *
     * @param string $side
     * @return void
     */
    public function getRules($side = 'backend')
    {
        if (!$this->hasRules($side)) {
            return;
        }

        $rules = $this->rules;

        if ($this->rulesHas($side)) {
            $rules = $rules[$side];
        }

        if (!$this->areRulesByLanguage($rules)) {
            $rules = $this->getRulesByLanguages($rules);
        }

        return $rules;
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

        unset($schema['translator']);

        return $schema;
    }

    /**
     * Return the same rules for each language
     *
     * @param mixed $base - base rules to apply for each language
     * @return array
     */
    protected function getRulesByLanguages($base)
    {
        $rules = [];

        foreach ($this->getLanguages() as $language) {
            $rules[$language['code']] = $base;
        }

        return $rules;
    }

    /**
     * Determine if rules contains rules by langauge
     *
     * @param mixed $rules
     * @return bool
     */
    protected function areRulesByLanguage($rules)
    {
        foreach ($this->getLanguages() as $language) {
            if ($this->rulesHas($language['code'], $rules)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Initalize class properties
     *
     * @return void
     */
    protected function initProperties()
    {
        parent::initProperties();

        if (isset($this->schema['translator'])) {
          $this->translator = $this->schema['translator'];
        }
        elseif(config('laraform.translator')) {
          $this->translator = config('laraform.translator');
        }
    }
}