<?php
/**
 * Created by PhpStorm.
 * User: Stefan
 * Date: 12/9/2018
 * Time: 1:33 PM
 */
namespace Neoan3\Core;
use Neoan3\Apps\Ops;
use Neoan3\Frame\Frame;
class Unicore {
    public $unicore;
    function uni($frame=''){
        if($frame!=''){
            include_once(path . '/frame/' . $frame . '/' . $frame . '.php');
            $this->unicore = new Frame();
        } else {
            $this->unicore = new Serve();
        }

        $track = debug_backtrace();

        $this->get_files($track[0]['file']);
        return $this->unicore;
    }

    function get_files($file){
        $folder = substr($file,0,strrpos($file,DIRECTORY_SEPARATOR));
        $files = scandir($folder);
        if(!empty($files)){
            foreach ($files as $include){
                $buffer ='';
                if($include!='.'&&$include!='..'&&!is_dir($folder . DIRECTORY_SEPARATOR . $include)){
                    $buffer = file_get_contents($folder . DIRECTORY_SEPARATOR . $include);
                }
                switch(substr($include,-8)){
                    case 'tyle.css':
                        $this->unicore->addStylesheet($buffer);
                        break;
                    case '.ctrl.js':
                        $this->unicore->js .= Ops::embrace($buffer,array('base'=>base));
                        break;
                }
            }
        }
    }
}