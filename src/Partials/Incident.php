<?php

namespace Maicol07\Instatus\Partials;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maicol07\Instatus\Base;

/**
 * @method bool backfilled()
 * @method int duration()
 * @method Carbon created_at()
 * @method string impact()
 * @method string name()
 * @method Carbon|null resolved_at()
 * @method string status()
 * @method Carbon updated_at()
 * @method string id()
 * @method Collection updates()
 * @method Collection affectedComponents()
 */
class Incident extends Base
{
    public function preTransform(string &$prop, mixed &$value): void
    {
        if (in_array($prop, ['incidentUpdates', 'maintenanceUpdates'])) {
            $prop = 'updates';
        }
    }
}