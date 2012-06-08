<?php

    call_user_func(function()
    {
        $loader = require __DIR__ . '/../../vendor/autoload.php';

        $loader->add(null, __DIR__);
    });
