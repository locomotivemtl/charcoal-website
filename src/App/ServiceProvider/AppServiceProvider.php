<?php

namespace App\ServiceProvider;

use App\Model;
use App\Service\Container as ServiceContainer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Charcoal\Email\ServiceProvider\EmailServiceProvider;
use Charcoal\Model\ServiceProvider\ModelServiceProvider;
use Charcoal\Support\Model\Repository\CachedCollectionLoader;

/**
 * App Service Provider
 */
class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * The array of model class aliases.
     *
     * @var array
     */
    public $modelAliases = [
        'contact/notification' => Model\Contact\Notification::class,
    ];

    /**
     * @param  Container $container Pimple DI Container.
     * @return void
     */
    public function register(Container $container)
    {
        $container->register(new EmailServiceProvider());
        $container->register(new ModelServiceProvider());

        $this->registerModels($container);
    }

    /**
     * @param  Container $container The service locator.
     * @return void
     */
    private function registerModels(Container $container)
    {
        /**
         * Application's collection loader container.
         */
        $container['app/collection-loaders'] = function (Container $container) {
            $repositories = new ServiceContainer();

            $filters = [
                [
                    'property' => 'active',
                    'value'    => true,
                ],
            ];
            $orders  = [
                [
                    'property'  => 'position',
                    'direction' => 'asc',
                ],
            ];

            foreach ($this->modelAliases as $key => $class) {
                if (!isset($repositories[$key])) {
                    $repositories[$key] = function () use ($container, $class, $filters, $orders) {
                        return new CachedCollectionLoader([
                            'cache'           => $container['cache'],
                            'logger'          => $container['logger'],
                            'factory'         => $container['model/factory'],
                            'collection'      => 'array',
                            'model'           => $class,
                            'default_filters' => $filters,
                            'default_orders'  => $orders,
                        ]);
                    };
                }

                $repositories->alias($key, $class);
                $repositories->alias($key, $class::objType());
            }

            return $repositories;
        };
    }
}
