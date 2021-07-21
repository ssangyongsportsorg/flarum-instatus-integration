<?php

use Flarum\Database\Migration;

return Migration::addColumns('posts', [
    'instatus_id' => ['text']
]);