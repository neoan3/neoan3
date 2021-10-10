<?php

/* Generated by neoan3-cli */

namespace Neoan3\Component\Endpoint;

use Neoan3\Core\Serve;
use Neoan3\Provider\Model\InitModel;

/**
 * Class Endpoint
 * @package Neoan3\Components
 */
class EndpointController extends Serve
{

    /**
     * Get to know how API endpoints work!
     *
     * The following function accounts for the endpoints:
     *
     * GET /api.v1/endpoint
     * GET /api.v1/endpoint?any-key=any-value
     * GET /api.v1/endpoint/{userType}
     * GET /api.v1/endpoint/{userType}?any-key=any=value
     * GET /api.v1/endpoint/{userType}/{id}
     * GET /api.v1/endpoint/{userType}/{id}?any-key=any-value
     *
     * try them out by visiting these combinations in your browser
     *
     * @param string|null $userType
     * @param null $id
     * @param array $params
     * @return array
     */
    function getEndpoint(?string $userType = 'self', $id = null, array $params = ['new' => 'tu']): array
    {
        return array_merge([
            'type' => $userType,
            'id' => $id,
        ], $params);
    }

    /**
     * In this simple example, a payload is required. The JSON payload will be translated into an associative array.
     * @param array $body
     * @return array
     */
    function postEndpoint(array $body): array
    {
        $body['id'] = '123';
        return $body;
    }

    /**
     * As this route is independent of a frame, we load dependencies directly.
     * Please avoid such a pattern in your application and rather set up your dependencies in a frame.
     */
    #[InitModel()]
    function init()
    {
        $this->renderer->includeJs(__DIR__ . '/endpoint.ctrl.js',['endpoint'=>base . 'api.v1/']);
        $this->renderer->includeJs('https://cdn.jsdelivr.net/npm/pretty-print-json@1.1/dist/pretty-print-json.min.js');
        $this->renderer->includeStylesheet('https://cdn.jsdelivr.net/npm/gaudiamus-css@latest/css/gaudiamus.min.css');
        $this->renderer->includeJs('https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.0/dist/alpine.min.js');
        $this->renderer->includeStylesheet('https://cdn.jsdelivr.net/npm/pretty-print-json@1.1/dist/pretty-print-json.css');
        $this->renderer->includeStylesheet(base . 'frame/Demo/demo.css');
        $this->hook('main', 'endpoint')
            ->hook('header','nav')
            ->output();
    }
}
