<?php

namespace Neoan3\Core;

use Neoan3\Apps\Template;

/**
 * Class Serve
 * @package Neoan3\Core
 */
class Serve
{
    /**
     * @var string
     */
    private $html = '';
    /**
     * @var string
     */
    public $head = '';
    /**
     * @var string
     */
    public $header = '';
    /**
     * @var string
     */
    public $main = '';
    /**
     * @var string
     */
    public $footer = '';
    /**
     * @var string
     */
    public $style = '';
    /**
     * @var string
     */
    public $importedStyles = '';
    /**
     * @var string
     */
    public $scripts = '';
    /**
     * @var string
     */
    public $importedScripts = '';
    /**
     * @var string
     */
    public $modules = '';
    /**
     * @var string
     */
    public $js = '';
    /**
     * @var string
     */
    public $viewExt = 'html';
    /**
     * @var string
     */
    public $styleExt = 'css';
    /**
     * @var string
     */
    public $feExt = 'js';
    /**
     * @var
     */
    public $runComponent;
    /**
     * @var
     */
    public $passOn;
    /**
     * @var array
     */
    public $methods = [];
    /**
     * @var array
     */
    public $ctrl = [];

    /**
     * Serve constructor.
     */
    function __construct()
    {
        $this->html = '';
        $this->secureCustomElementDefine();
        $this->initFrame();
        $this->startHtml();

    }

    /**
     * @param array $params
     * @return $this
     */
    function assume($params = [])
    {
        $params = $this->ensureBase($params);
        $test = is_array($this->runComponent) ? $this->runComponent[0] . DIRECTORY_SEPARATOR . $this->runComponent[1] : 'no-a-path';
        if (file_exists($test . '.style.css')) {
            $this->style .= Template::embrace(file_get_contents($test . '.style.css'), $params);
        }
        if (file_exists($test . '.ctrl.js')) {
            $this->js .= Template::embrace(file_get_contents($test . '.ctrl.js'), $params);
        }
        return $this;
    }


    /**
     * @return array
     */
    function constants()
    {
        return [];
    }

    /**
     *
     */
    function startHtml()
    {
        $this->html .= '<!doctype html><html><head>{{head}}</head><body>';
        $this->html .= '<style>{{importedStyles}}{{style}}</style>';
        $this->html .= '<header>{{header}}</header><neoan-root></neoan-root>{{main}}<footer>{{footer}}</footer>';
        $this->html .= '{{importedScripts}}{{scripts}}<script>{{js}}</script>{{modules}}</body></html>';
    }

    /**
     * @param $what
     * @param $obj
     * @return $this
     */
    function addHead($what, $obj)
    {
        switch ($what) {
            case 'link':
                $this->head .= '<link ';
                foreach ($obj as $key => $val) {
                    $this->head .= ' ' . $key . '="' . $val . '"';
                }
                $this->head .= '/>';
                break;
            case 'base':
                $this->head .= '<base href="' . $obj . '">';
                break;
            case 'title':
                $this->head .= '<title>' . $obj . '</title>';
                break;
            case 'meta':
                $this->head .= '<meta ';
                foreach ($obj as $key => $val) {
                    $this->head .= ' ' . $key . '="' . $val . '"';
                }
                $this->head .= '/>';
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * @param $style
     */
    function addStylesheet($style)
    {
        if (strpos($style, base) !== false) {
            $file = file_get_contents(path . '/' . substr($style, strlen(base)));
            $this->style .= Template::embrace($file, ['base' => base]);
        } else {
            $this->importedStyles .= ' @import url(' . $style . '); ';
        }
    }

    /**
     * @param $module
     * @return $this
     */
    function includeJsModule($module)
    {
        $this->modules .= '<script type="module" src="' . base . 'node_modules/' . $module . '"></script>';
        return $this;
    }

    /**
     * @param $src
     * @param array $data
     * @param string $type
     * @return $this
     */
    function includeJs($src, $data = [], $type = 'text/javascript')
    {

        if (empty($data)) {
            $this->scripts .= "\n" . '<script type="' . $type . '" src="' . $src . '"></script>';
        } else {
            $cont = Template::embrace(file_get_contents($src), $data);
            $btr = explode(DIRECTORY_SEPARATOR, $src);
            if ($type !== 'text/javascript') {
                $this->scripts .= "\n" . '<script type="' . $type . '">';
                $this->scripts .= $this->annotate(end($btr));
                $this->scripts .= $cont;
                $this->scripts .= '</script>';
            } else {
                $this->js .= $this->annotate(end($btr)) . $cont;
            }

        }
        return $this;
    }

    /**
     * @param $name
     * @return string
     */
    private function annotate($name)
    {
        return "\n/* include(" . $name . ') */' . "\n";
    }

    /**
     *
     */
    private function initFrame()
    {

        foreach ($this->constants() as $type => $includes) {
            foreach ($includes as $include) {
                switch ($type) {
                    case 'link':
                        $this->addHead('link', $include);
                        break;
                    case 'base':
                        $this->addHead('base', $include);
                        break;
                    case 'stylesheet':
                        $this->addStylesheet($include);
                        break;
                    case 'meta':
                        $this->addHead('meta', $include);
                        break;
                    case 'js':
                        isset($include['data']) ? $data = $include['data'] : $data = [];
                        isset($include['type']) ? $jsType = $include['type'] : $jsType = 'text/javascript';
                        $this->includeJs($include['src'], $data, $jsType);
                        break;
                }
            }
        }

    }

    /**
     * @param $hook
     * @param $view
     * @param array $params
     * @return $this
     */
    function hook($hook, $view, $params = [])
    {
        $params = $this->ensureBase($params);
        $this->$hook .= Template::embrace(
            $this->fileContent(path . '/component/' . $view . '/' . $view . '.view.' . $this->viewExt, $params),
            $params
        );
        return $this;
    }

    /**
     * @param $context
     * @param $function
     * @return $this
     */
    function addMethod($context, $function)
    {
        $this->passOn[$function] = [$context, $function];
        $this->methods[$function] = function () {
            $args = func_get_args();
            $pass = array_shift($args);
            return $this->passOn[$pass][0]->{$this->passOn[$pass][1]}($this, $args);
        };
        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    function addController($name)
    {
        $ctrl = '\\Neoan3\\Components\\' . $name;
        $this->ctrl[$name] = new $ctrl;
        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    function __call($name, $arguments)
    {
        $pass = null;
        if (isset($arguments[0])) {
            $pass = $arguments[0];
        }
        if (!method_exists($this, $name)) {
            foreach ($this->methods as $key => $function) {
                if ($key == $name) {
                    $this->methods[$name]($key, $pass);
                    return $this;
                }
            }
            die('Unknown method: ' . $name);
        }
    }

    /**
     * @param $name
     * @return string
     */
    private function snake2Camel($name)
    {
        $nameParts = explode('-', $name);
        $res = '';
        foreach ($nameParts as $i => $namePart) {
            $res .= $i === 0 ? strtolower($namePart) : ucwords($namePart);
        }
        return $res;
    }

    /**
     * Add base-property to params
     *
     * @param $params
     *
     * @return mixed
     */
    private function ensureBase($params)
    {
        if (!isset($params['base'])) {
            $params['base'] = base;
        }
        return $params;
    }

    /**
     * Prevents redefining custom elements
     */
    private function secureCustomElementDefine()
    {
        $this->js .= file_get_contents(__DIR__ . '/protectors.js');
    }

    /**
     * @param $element
     * @param array $params
     * @return $this
     */
    function includeElement($element, $params = [])
    {
        $params = $this->ensureBase($params);
        $pName = $this->snake2Camel($element);
        $path = path . '/component/' . $pName . '/' . $pName . '.ce.';
        if (file_exists($path . $this->viewExt)) {
            $this->footer .= '<template id="' . $element . '">' .
                $this->fileContent($path . $this->viewExt, $params) .
                '</template>';
        }
        if (file_exists($path . 'js')) {
            $getString = '';
            foreach ($params as $key => $value) {
                $getString .= (strlen($getString) > 0 ? '&' : '') . $key . '=' . $value;
            }
            if (strlen($getString) > 0) {
                $getString = '?' . $getString;
            }
            $this->modules .= '<script type="module" src="' . base . '/serve.file/' . $pName . '/ce' . $getString . '"></script>';
        }

        return $this;
    }

    /**
     * @param $context
     * @param $function
     * @return $this
     */
    function callback($context, $function)
    {
        $context->$function($this);
        return $this;
    }

    /**
     * @param $filePath
     * @param array $params
     * @return false|string
     */
    function fileContent($filePath, $params = [])
    {
        return Template::embrace(file_get_contents($filePath), $params);
    }


    /**
     * echos DOM
     * @param array $params optional
     */
    function output($params = [])
    {
        $this->assume($params);
        echo Template::embrace($this->html, [
            'head' => $this->head,
            'style' => $this->style,
            'importedStyles' => $this->importedStyles,
            'header' => $this->header,
            'main' => $this->main,
            'scripts' => $this->scripts,
            'js' => $this->js,
            'importedScripts' => $this->importedScripts,
            'footer' => $this->footer,
            'modules' => $this->modules
        ]);
    }
}
