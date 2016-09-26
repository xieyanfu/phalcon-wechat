<?php

/**
 * Register application modules
 */
$application->registerModules(array(
        'frontend' => array(
            'className' => 'Wechat\Frontend\Module',
            'path' => __DIR__ . '/../apps/frontend/Module.php'
        ),
        'admin' => array(
            'className' => 'Wechat\Admin\Module',
            'path' => __DIR__ . '/../apps/admin/Module.php'
        )
    )
);
