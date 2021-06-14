<?php

namespace App\Routing;

// From Pimple
use Pimple\Container;

// From PSR-7
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

// From 'charcoal-translator'
use Charcoal\Translator\TranslatorAwareTrait;

// From 'charcoal-app'
use Charcoal\App\Route\TemplateRoute;

use App\Model\Page;

/**
 *
 */
class HomeRoute extends TemplateRoute
{
    use TranslatorAwareTrait;

    /**
     * @param  Container         $container A service locator.
     * @param  RequestInterface  $request   A PSR-7 compatible Request instance.
     * @param  ResponseInterface $response  A PSR-7 compatible Response instance.
     * @return ResponseInterface
     */
    public function __invoke(
        Container $container,
        RequestInterface $request,
        ResponseInterface $response
    ) {
        $path = $request->getRequestTarget();

        if ($path === '/') {
            $this->setTranslator($container['translator']);
            $page = $container['model/factory']->create(Page::class)->loadFrom('template_ident', 'home');
            $redirection = $this->parseRedirect((string)$page->url(), $request);
            return $response->withRedirect($redirection, 303);
        }

        return parent::__invoke($container, $request, $response);
    }

    /**
     * Determine if the URI path resolves to an object.
     *
     * @param  Container $container Service locator.
     * @return boolean
     */
    public function pathResolvable(Container $container)
    {
        $this->setDependencies($container);

        $object = $this->getObjectRouteFromPath();
        if (!$object->id()) {
            return false;
        }

        $contextObject = $this->getContextObject();

        if (!$contextObject || !$contextObject->id()) {
            return false;
        }

        return (!!$contextObject->active() && !!$contextObject->isActiveRoute());
    }
}
