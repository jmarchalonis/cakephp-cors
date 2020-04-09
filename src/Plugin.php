<?php
namespace Cors;

use Cake\Core\BasePlugin;
use Cake\Http\MiddlewareQueue;
use Cors\Routing\Middleware\CorsMiddleware;

class Plugin extends BasePlugin
{
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue->add(new CorsMiddleware());

        return $middlewareQueue;
    }
}
