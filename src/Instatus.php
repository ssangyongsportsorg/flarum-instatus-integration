<?php

namespace Maicol07\Instatus;

use Flarum\Frontend\Content\Meta;
use Maicol07\Instatus\Partials\Incident;
use Maicol07\Instatus\Partials\Page;

/**
 * @method Meta meta()
 * @method Page page()
 * @method Incident incident()
 */
class Instatus extends Base
{
    public bool $isMaintenance = false;

    public function __construct(array|object $body)
    {
        if (isset($body->maintenance)) {
            $this->isMaintenance = true;
        }

        parent::__construct($body);
    }

    public function preTransform(string &$prop, mixed &$value): void
    {
        if ($prop === 'maintenance') {
            $prop = 'incident';
            $value = new Incident($value);
        }
    }
}