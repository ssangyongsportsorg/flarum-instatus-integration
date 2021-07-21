<?php

namespace Maicol07\Instatus\Partials;

use Maicol07\Instatus\Base;

/**
 * @method string id()
 * @method bool status()
 * @method string statusDescription()
 */
class Page extends Base {
    public function postTransform(string &$prop, mixed &$value): void
    {
        if ($prop === 'statusIndicator') {
            $prop = 'status';
            $value = ($value === 'UP');
        }
    }
}