<?php

use Flarum\Database\Migration;

return Migration::addColumns('discussions', [
    'instatus_id' => 'text'
]);