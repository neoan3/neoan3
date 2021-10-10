<?php

namespace Neoan3\Component\Demo;

use Neoan3\Apps\Template;
use Neoan3\Core\Unicore;
use Neoan3\Frame\Demo;

/**
 * Class Demo
 * @package Neoan3\Components
 */
class DemoController extends Unicore
{
    /**
     * Route call (Singleton style)
     */
    function init()
    {
        $info = json_decode(file_get_contents(path . '/composer.json'), true);
        $info['installation'] = path;
        $info['base'] = base;
        $this
            ->uni('demo')
            ->setTitle('neoan3')
            ->callback(function(Demo $serve){
                $serve->renderer
                    ->includeJs('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.2.0/build/highlight.min.js')
                    ->includeStylesheet('https://cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.2.0/build/styles/a11y-dark.min.css');
            })
            ->addRenderParameter('tabs', function () use ($info) {
                $renderParams = [];
                foreach (['system', 'introduction', 'quickstart', 'changes'] as $tab) {
                    $renderParams[$tab] = Template::embraceFromFile('/component/Demo/' . $tab . '.tab.html', $info);
                }
                return $renderParams;
            })
            ->hook('main', 'demo', $info)
            ->output();
    }
}
