<?php

namespace Laraform\Elements;

class DateElement extends Element
{
    protected function initProperties()
    {
        parent::initProperties();

        if (isset($this->schema['mode']) && $this->schema['mode'] == 'range') {
            $this->validateAs = 'multivalue';
        }
    }
}