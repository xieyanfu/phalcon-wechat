<?php

/**
 * Register application modules
 */
$application->registerModules(array(
        'home' => array(
            'className' => 'Wechat\Home\Module',
            'path' => __DIR__ . '/../apps/frontend/Module.php'
        ),
        'admin' => array(
            'className' => 'Wechat\Admin\Module',
            'path' => __DIR__ . '/../apps/backend/Module.php'
        )
    )
);
