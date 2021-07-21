<?php

/*
 * This file is part of maicol07/instatus-integration.
 *
 * Copyright (c) 2021 maicol07.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Maicol07\Instatus;

use Flarum\Extend;

return [
    (new Extend\Routes('forum'))
        ->post('/integrations/instatus', 'integrations.instatus', InstatusController::class)
];
