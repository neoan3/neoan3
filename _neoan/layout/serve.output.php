<?php
/**
 * Created by PhpStorm.
 * User: sroehrl
 * Date: 10/18/2018
 * Time: 1:57 PM
 */
namespace Neoan3\Core;
use Neoan3\Apps\Ops;
use Pug\Pug;
class Serve {
    private $html='';
    public  $head='';
    public  $header='';
    public  $main='';
    public  $footer='';
    public  $style='';
    public  $importedStyles='';
    public  $scripts='';
    public  $importedScripts='';
    public  $modules='';
    public  $js='';
    public  $pug;
    public  $passOn;
    public  $methods=[];
    public  $ctrl=[];

    function __construct(){
        $this->pug = new Pug();
        $this->html = '';

        $this->initFrame();
        $this->startHtml();
    }
    function constants(){return [];}
    function startHtml(){
        $this->html .= '<!doctype html><html><head>{{head}}</head><body>';
        $this->html .= '<style>{{importedStyles}}{{style}}</style>';
        $this->html .= '<header>{{header}}</header><neoan-root></neoan-root>{{main}}<footer>{{footer}}</footer>';
        $this->html .= '{{importedScripts}}{{scripts}}<script>{{js}}</script>{{modules}}</body></html>';
    }
    function addHead($what,$obj){
        switch($what){
            case 'link':
                $this->head .= '<link ';
                foreach($obj as $key => $val){
                    $this->head .= ' ' . $key . '="' . $val . '"';
                }
                $this->head .= '/>';
                break;
            case 'base':
                $this->head .= '<base href="' .$obj.'">';
                break;
            case 'title':
                $this->head .=  '<title>'.$obj.'</title>';
                break;
            case 'meta':
                $this->head .= '<meta ';
                foreach($obj as $key => $val){
                    $this->head .= ' ' . $key . '="' . $val . '"';
                }
                $this->head .= '/>';
                break;
            default:
                break;
        }
        return $this;
    }
    function addStylesheet($style){
        if(strpos($style,base)!==false){
            $file = file_get_contents($style);
            $this->style .= Ops::embrace($file,['base'=>base]);
        } else {
            $this->importedStyles  .= ' @import url(' . $style . '); ';
        }
    }
    function includeJsModule($module){
        $this->modules .= '<script type="module">'.$module.'</script>';
        return $this;
    }
    function includeJs($src,$data=[],$type='text/javascript'){

        if(empty($data)){
            $this->scripts .= "\n". '<script type="'.$type.'" src="' . $src . '"></script>';
        } else {
            $cont = Ops::embrace(file_get_contents($src),$data);
            $btr = explode(DIRECTORY_SEPARATOR,$src);
            if($type!=='text/javascript'){
                $this->scripts .= "\n". '<script type="'.$type.'">';
                $this->scripts .= $this->annotate(end($btr));
                $this->scripts .= $cont;
                $this->scripts .= '</script>';
            } else{
                $this->js .= $this->annotate(end($btr)). $cont;
            }

        }

    }
    private function annotate($name){
        return "\n/* include(" .$name .') */' ."\n";
    }

    private function initFrame(){

        foreach($this->constants() as $type => $includes){
            foreach($includes as $include){
                switch ($type){
                    case 'link':
                        $this->addHead('link',$include);
                        break;
                    case 'base': $this->addHead('base',$include);
                        break;
                    case 'stylesheet': $this->addStylesheet($include);
                        break;
                    case 'meta': $this->addHead('meta',$include);
                        break;
                    case 'js':
                        isset($include['data'])?$data = $include['data']:$data=[];
                        isset($include['type'])?$jsType = $include['type']:$jsType='text/javascript';
                        $this->includeJs($include['src'],$data,$jsType);
                       break;
                }
            }
        }

    }
    function hook($hook,$view,$params=[]){
        if(!isset($params['base'])){
            $params['base'] = base;
        }
        $this->$hook .= Ops::embrace(
            $this->pug->renderFile(path.'/component/'.$view.'/'.$view.'.view.pug',$params),
            $params
        );
        return $this;
    }

    function addMethod($context,$function){
        $this->passOn[$function] = [$context,$function];
        $this->methods[$function] = function(){
            $args = func_get_args();
            $pass = array_shift($args);
            return $this->passOn[$pass][0]->{$this->passOn[$pass][1]}($this,$args);
        };
        return $this;
    }
    function addController($name){
        require_once(path.'/component/'.$name.'/'.$name.'.ctrl.php');
        $this->ctrl[$name.'Ctrl'] = new $name;
        return $this;
    }
    function __call($name, $arguments) {
        if(!method_exists($this,$name)){
            foreach ($this->methods as $key=>$function){
                if($key==$name){
                    $this->methods[$name]($key,$arguments[0]);
                    return $this;
                }
            }
            die('Unknown method: '.$name);
        }
    }
    private function snake2Camel($name){
        $nameParts = explode('-',$name);
        $res = '';
        foreach ($nameParts as $namePart){
            $res .= ucwords($namePart);
        }
        return $res;
    }
    function includeElement($element,$params=[]){
        $pName = $this->snake2Camel($element);
        $path = path.'/component/'.$pName.'/'.$pName.'.ce.';
        if(file_exists($path.'pug')){
            $this->footer .= '<template id="'.$element.'">'.$this->pug->renderFile($path.'pug',$params).'</template>';
        }
        if(file_exists($path.'js')){
            $this->js .= file_get_contents($path.'js');
        }

        return $this;
    }

    function callback($context,$function){
        $context->$function($this);
        return $this;
    }
    function output(){

        echo Ops::embrace($this->html,[
            'head'=>$this->head,
            'style'=>$this->style,
            'importedStyles'=>$this->importedStyles,
            'header'=>$this->header,
            'main'=>$this->main,
            'scripts'=>$this->scripts,
            'js'=>$this->js,
            'importedScripts'=>$this->importedScripts,
            'footer'=>$this->footer,
            'modules'=>$this->modules
        ]);
    }
}