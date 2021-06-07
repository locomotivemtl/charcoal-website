<?php

namespace App\Routing;

use Charcoal\App\Route\TemplateRoute;
use Charcoal\Model\ModelFactoryTrait;
use Pimple\Container;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use App\Model\Page;

/**
 * Class HomeRoute
 */
class HomeRoute extends TemplateRoute
{
    use ModelFactoryTrait;

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
    )
    {
        $this->setModelFactory($container['model/factory']);
        return parent::__invoke($container, $request, $response);
    }

    /**
     * Override from templateRoute to give a context object
     *
     * @param  Container        $container A DI (Pimple) container.
     * @param  RequestInterface $request   The request to intialize the template with.
     * @return string
     */
    protected function createTemplate(Container $container, RequestInterface $request)
    {
        $template = parent::createTemplate($container, $request);

        $contextObject             = $this->modelFactory()->create(Page::class)->loadFrom('template_ident', 'home');
        $template['contextObject'] = $contextObject;

        // Set data from context object.
        $template->setData($contextObject['templateOptions']);

        return $template;
    }
}
