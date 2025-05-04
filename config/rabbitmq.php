<?php

return [
    'host'=>env('RABBIT_HOST', 'localhost'),
    'port'=>env('RABBIT_PORT', 5672),
    'user'=>env('RABBIT_USER', 'guest'),
    'pass'=>env('RABBIT_PASS', 'guest')
];
