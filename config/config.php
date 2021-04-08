<?php

/**
 * Load Application Configuration
 *
 * Bootstrap the given application.
 */

/** The application's absolute root path */
$this['base_path'] = dirname(__DIR__).'/';

/** Import core settings */
$this->addFile(__DIR__.'/app/framework.json');
$this->addFile(__DIR__.'/app/attachments.json');
$this->addFile(__DIR__.'/app/templates.json');
$this->addFile(__DIR__.'/app/routing.json');

/** Import local settings */
$this->addFile(__DIR__.'/config.local.json');
