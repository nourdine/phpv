<?php

declare(strict_types=1);

namespace Phpv\Validator\Leaf\Native\Trait;

use Phpv\Input\KeyValue;

trait ValidationTrait
{
    protected function checkString(KeyValue $kv)
    {
        if (!is_string($kv->getValue())) {
            throw new \InvalidArgumentException("The provided value must be a string");
        }
    }
}
