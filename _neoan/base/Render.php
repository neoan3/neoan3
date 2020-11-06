<?php


namespace Neoan3\Core;


use Neoan3\Apps\Template;

/**
 * Class Render
 * @package Neoan3\Core
 */
class Render implements Renderer
{

    private string $js = '';

    private string $scripts = '';

    private string $head = '';

    private string $style = '';

    private string $importedStyles = '';

    private string $importedScripts = '';

    private string $modules = '';

    private array $hooks;

    private string $lang = 'en';

    private array $constants;

    private string $html = '';

    private array $viewParams = [];

    private ?string $componentName = null;

    private ?string $title = null;

    private bool $usesCustomElements = false;

    public function __construct($constants = [])
    {
        $this->constants = $constants;
        $this->hooks = [
            'header' => '',
            'main' => '',
            'footer' => ''
        ];
        $this->secureCustomElementDefine();

    }

    /**
     * @param array $afterHooks
     */
    public function output($afterHooks = []): void
    {
        $this->writeOutConstants();
        $this->startHtml();
        $this->assume($afterHooks);
        echo Template::embrace($this->html, [
            'head' => $this->head,
            'lang' => $this->lang,
            'title' => $this->title,
            'style' => $this->style,
            'importedStyles' => $this->importedStyles,
            'header' => $this->hooks['header'],
            'main' => $this->hooks['main'],
            'scripts' => $this->scripts,
            'js' => $this->js,
            'importedScripts' => $this->importedScripts,
            'footer' => $this->hooks['footer'],
            'modules' => $this->modules
        ]);
        Event::dispatch('Core\\Renderer::answered', $afterHooks);
    }

    /**
     * Add parameters for the view(s)
     *
     * @param $params
     *
     */
    public function attachParameters($params = []) :void
    {
        $this->viewParams = array_merge($this->viewParams, $params);
        if (!isset($this->viewParam['base'])) {
            $this->viewParams['base'] = base;
        }

    }


    /*
     *  SETTERS & GETTERS
     *
     * */

    /**
     * Returns current component
     * @return string
     */
    public function getComponentName(): string
    {
        return $this->componentName;
    }

    /**
     * Sets current component
     * @param $componentName
     */
    public function setComponentName($componentName): void
    {
        $this->componentName = $componentName;
    }

    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function assignToHook($hookName, $view, $params =[]): void
    {
        $this->attachParameters($params);
        $this->hooks[$hookName] = Template::embraceFromFile(
            '/component/' . ucfirst($view) . '/' . lcfirst($view) . '.view.html',
            $this->viewParams
        );
    }


    public function addToHead($what,$declaration){
        $this->constants[$what][] = $declaration;
    }

    /**
     * Assigning constants to the
     */
    public function writeOutConstants()
    {
        foreach ($this->constants as $type => $includes) {
            foreach ($includes as $include) {
                switch ($type) {
                    case 'link':
                        $this->head .= '<link ';
                        foreach ($include as $key => $val) {
                            $this->head .= ' ' . $key . '="' . $val . '"';
                        }
                        $this->head .= '/>';
                        break;
                    case 'base':
                        $this->head .= '<base href="' . $include . '">';
                        break;
                    case 'title':
                        if(!$this->title){
                            $this->title = $include;
                        }
                        break;
                    case 'lang':
                        if(!$this->lang){
                            $this->lang = $include;
                        }
                        break;
                    case 'stylesheet':
                        $this->includeStylesheet($include);
                        break;
                    case 'meta':
                        $this->head .= '<meta ';
                        foreach ($include as $key => $val) {
                            $this->head .= ' ' . $key . '="' . $val . '"';
                        }
                        $this->head .= '/>';
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
     * @param array $params
     * @return $this
     */
    private function assume($params = [])
    {
        $this->attachParameters($params);
        if($this->componentName){
            preg_match('/\\\Component\\\([a-z0-9]+)/i', $this->componentName, $matches);
            $folder = path . DIRECTORY_SEPARATOR . 'component' . DIRECTORY_SEPARATOR . $matches[1] . DIRECTORY_SEPARATOR;
            foreach (['style' => '.style.css','js' => '.ctrl.js'] as $type => $assumable){
                $potential = $folder . lcfirst($matches[1]) . $assumable;
                if (file_exists($potential)) {
                    $this->$type .= Template::embrace(
                        file_get_contents($potential),
                        $this->viewParams
                    );
                }
            }
        }

        return $this;
    }
    private function startHtml()
    {

        $this->html .= '<!doctype html><html lang="{{lang}}"><head>{{head}}<title>{{title}}</title></head><body>';
        $this->html .= '<style>{{importedStyles}}{{style}}</style>';
        $this->html .= '<header>{{header}}</header><neoan-root></neoan-root>{{main}}<footer>{{footer}}</footer>';
        $this->html .= '{{importedScripts}}{{scripts}}<script>{{js}}</script>{{modules}}</body></html>';
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
                $this->js .= $this->annotate(end($btr)) . $cont . "\n";
            }

        }
        return $this;
    }
    /**
     * @param $module
     */
    function includeJsModule($module)
    {
        $this->secureCustomElementDefine();
        $this->modules .= '<script type="module" src="' . base . 'node_modules/' . $module . '"></script>';
    }
    /**
     * @param $style
     */
    function includeStylesheet($style)
    {
        if (strpos($style, base) !== false) {
            $file = file_get_contents(path . '/' . substr($style, strlen(base)));
            $this->style .= Template::embrace($file, ['base' => base]);
        } else {
            $this->importedStyles .= ' @import url(' . $style . '); ';
        }
    }



    /**
     * @param $element
     * @param array $params
     * @return $this
     */
    function includeElement($element, $params = [])
    {
        $this->secureCustomElementDefine();
        $this->attachParameters($params);
        $path = '/component/' . ucfirst($element) . '/' . lcfirst($element) . '.ce';
        if (file_exists(path . $path . '.html')) {
            $this->hooks['footer'] .= '<template id="' . $element . '">' .
                Template::embraceFromFile($path . '.html' , $this->viewParams) .
                '</template>';
        }
        if (file_exists(path . $path . '.js')) {
            $getString = '';
            foreach ($this->viewParams as $key => $value) {
                $getString .= (strlen($getString) > 0 ? '&' : '') . $key . '=' . $value;
            }
            if (strlen($getString) > 0) {
                $getString = '?' . $getString;
            }
            $this->modules .= '<script type="module" src="' . base . '/serve.file/' . lcfirst($element) . '/ce' . $getString . '"></script>';
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
     * Prevents redefining custom elements
     */
    private function secureCustomElementDefine()
    {
        if(!$this->usesCustomElements){
            $this->js .= file_get_contents(dirname(__DIR__) . '/layout/protectors.js');
            $this->usesCustomElements = true;
        }

    }

}