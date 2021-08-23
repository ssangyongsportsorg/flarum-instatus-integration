<?php

namespace Maicol07\Instatus;

use Flarum\Frontend\Content\Meta;
use Maicol07\Instatus\Partials\Component;
use Maicol07\Instatus\Partials\ComponentUpdate;
use Maicol07\Instatus\Partials\Incident;
use Maicol07\Instatus\Partials\Page;

/**
 * @method Meta meta()
 * @method Page page()
 * @method Incident incident() Available only if it's an incident or maintenance
 * @method Component component() Available only if it's a component update (like automated status updates)
 * @method ComponentUpdate component_update() Available only f it's a component update (like automated status updates)
 */
class Instatus extends Base
{
    public bool $isMaintenance = false;

    public function __construct(object $body)
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
