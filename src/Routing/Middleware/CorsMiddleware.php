<?php

namespace Cors\Routing\Middleware;

use Cake\Core\Configure;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Http\Message\Builder\ResponseBuild;
use Cake\Http\Client\Response;
use Exception;
use Zend\Diactoros\Response as ZendResponse;

class CorsMiddleware implements MiddlewareInterface
{

    /**
     * PHPCS docblock fix needed!
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getHeader('Origin')) {
            if (strtoupper($request->getMethod()) === 'OPTIONS') {
                $response = new ZendResponse();
                $response = $response
                    ->withHeader('Access-Control-Allow-Headers', $this->_allowHeaders($request))
                    ->withHeader('Access-Control-Allow-Methods', $this->_allowMethods());;
                return $response;
            } else {
                $response = $handler->handle($request);
            }
        } else {
            $response = $handler->handle($request);
        }

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $this->_allowOrigin($request))
            ->withHeader('Access-Control-Allow-Credentials', $this->_allowCredentials())
            ->withHeader('Access-Control-Max-Age', $this->_maxAge())
            ->withHeader('Access-Control-Expose-Headers', $this->_exposeHeaders());

        return $response;
    }

    /**
     * PHPCS docblock fix needed!
     */
    private function _allowOrigin($request)
    {
        $allowOrigin = Configure::read('Cors.AllowOrigin');
        $origin = $request->getHeader('Origin');

        if ($allowOrigin === true || $allowOrigin === '*') {
            return $origin;
        }

        if (is_array($allowOrigin)) {
            $origin = (array)$origin;

            foreach ($origin as $o) {
                if (in_array($o, $allowOrigin)) {
                    return $origin;
                }
            }

            return '';
        }

        return (string)$allowOrigin;
    }

    /**
     * PHPCS docblock fix needed!
     */
    private function _allowCredentials()
    {
        return (Configure::read('Cors.AllowCredentials')) ? 'true' : 'false';
    }

    /**
     * PHPCS docblock fix needed!
     */
    private function _allowMethods()
    {
        return implode(', ', (array)Configure::read('Cors.AllowMethods'));
    }

    /**
     * PHPCS docblock fix needed!
     */
    private function _allowHeaders($request)
    {
        $allowHeaders = Configure::read('Cors.AllowHeaders');

        if ($allowHeaders === true) {
            return $request->getHeader('Access-Control-Request-Headers');
        }

        return implode(', ', (array)$allowHeaders);
    }

    /**
     * PHPCS docblock fix needed!
     */
    private function _exposeHeaders()
    {
        $exposeHeaders = Configure::read('Cors.ExposeHeaders');

        if (is_string($exposeHeaders) || is_array($exposeHeaders)) {
            return implode(', ', (array)$exposeHeaders);
        }

        return '';
    }

    /**
     * PHPCS docblock fix needed!
     */
    private function _maxAge()
    {
        $maxAge = (string)Configure::read('Cors.MaxAge');

        return ($maxAge) ?: '0';
    }
}
