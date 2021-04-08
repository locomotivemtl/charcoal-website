<?php

namespace App\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Charcoal\Email\ServiceProvider\EmailServiceProvider;
use Charcoal\Model\ServiceProvider\ModelServiceProvider;

/**
 * App Service Provider
 */
class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * @param  Container $container Pimple DI Container.
     * @return void
     */
    public function register(Container $container)
    {
        $container->register(new EmailServiceProvider());
        $container->register(new ModelServiceProvider());
    }
}
