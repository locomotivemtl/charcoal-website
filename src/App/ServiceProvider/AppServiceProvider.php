<?php

namespace App\ServiceProvider;

use App\Model;
use App\Transformer;
use App\Service;
use App\Service\Container as ServiceContainer;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Charcoal\Email\ServiceProvider\EmailServiceProvider;
use Charcoal\Model\ServiceProvider\ModelServiceProvider;
use Charcoal\ReCaptcha\CaptchaServiceProvider;
use Charcoal\Support\Model\Repository\CachedCollectionLoader;
use Charcoal\Translator\ServiceProvider\TranslatorServiceProvider;

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
        'case-study' => Model\CaseStudy::class,
        'feature' => Model\Feature::class,
    ];

    /**
     * @param  Container $container Pimple DI Container.
     * @return void
     */
    public function register(Container $container)
    {
        $container->register(new CaptchaServiceProvider());
        $container->register(new EmailServiceProvider());
        $container->register(new ModelServiceProvider());
        $container->register(new TranslatorServiceProvider());

        $this->registerModels($container);
        $this->registerTransformers($container);
        $this->registerServices($container);
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

    /**
     * @param  Container $container A service locator.
     * @return void
     */
    private function registerServices(Container $container)
    {
        /**
         * @param Container $container
         * @return \App\Service\AttachmentPresenter
         */
        $container['app/services/attachment-presenter'] = function (Container $container) {
            return new Service\AttachmentPresenter([
                'container'    => $container,
                'transformers' => [
                    'app/model/attachment/text'         => 'text',
                ],
            ]);
        };

        /**
         * @param Container $container
         * @return \App\Service\FilePresenter
         */
        $container['app/services/file-presenter'] = function (Container $container) {
            return new Service\FilePresenter([
                'baseUrl' => $container['base-url'],
                'logger'  => $container['logger'],
            ]);
        };
    }

    /**
     * @param  Container $container A service locator.
     * @return void
     */
    private function registerTransformers(Container $container)
    {
        $container['app/transformers'] = function (Container $container) {
            $transformers = new Container();

            $transformers['case-study'] = function (Container $transformers) use ($container) {
                return new Transformer\CaseStudy([
                    'filePresenter' => $container['app/services/file-presenter'],
                    'translator' => $container['translator']
                ]);
            };

            $transformers['feature'] = function (Container $transformers) use ($container) {
                return new Transformer\Feature([
                    'filePresenter' => $container['app/services/file-presenter'],
                    'attachmentTransformers' => $container['app/attachment/transformers'],
                    'baseUrl' => $container['base-url']
                ]);
            };

            return $transformers;
        };

        $container['app/attachment/transformers'] = function (Container $container) {
            $transformers = new Container();

            $transformers['text'] = function (Container $transformers) use ($container) {
                return new Transformer\Attachment\Text([
                    'baseUrl' => $container['base-url'],
                ]);
            };
            $transformers['rail'] = function (Container $transformers) use ($container) {
                return new Transformer\Attachment\Rail([
                    'baseUrl' => $container['base-url'],
                ]);
            };
            $transformers['text-visual'] = function (Container $transformers) use ($container) {
                return new Transformer\Attachment\TextVisual([
                    'filePresenter' => $container['app/services/file-presenter'],
                    'baseUrl' => $container['base-url'],
                ]);
            };
            return $transformers;
        };
    }
}
