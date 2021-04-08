<?php

/**
 * Load Admin Module Configuration
 *
 * Bootstrap the given application.
 */

// From 'charcoal-admin'
use Charcoal\Admin\Config as AdminConfig;
use Charcoal\Admin\Routing\TemplateRoute as AdminTemplateRoute;

/** Import vendor configuration */
$this->addFile(dirname(__DIR__) . '/vendor/locomotivemtl/charcoal-attachment/config/admin.json');

/**
 * Eagerly instantiate the Admin configuration
 * for improved merging.
 */
if (!($this['admin'] instanceof AdminConfig)) {
    $this['admin'] = new AdminConfig($this['admin']);
}

/** Import project configuration */
$this['admin']->addFile(__DIR__ . '/admin/interface.json');
$this['admin']->addFile(__DIR__ . '/admin/elfinder.json');
$this['admin']->addFile(__DIR__ . '/admin/routing.json');
$this['admin']->addFile(__DIR__ . '/admin/security.json');

/**
 * Load environment settings; such as database credentials
 * or credentials for 3rd party services.
 */
$env  = getenv('APPLICATION_ENV') ?: 'local';
$conf = __DIR__.'/admin.'.$env.'.json';
if (file_exists($conf)) {
    $this['admin']->addFile($conf);
}
