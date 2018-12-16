<?php
namespace Neoan3\Components;
use Neoan3\Core\Unicore;
class error_404 extends Unicore
{
	function init()
	{
        error_reporting(E_ALL ^E_NOTICE);
        ini_set('display_errors',1);
	    header("HTTP/1.0 404 Not Found");
		$this->uni()->addHead('title','Not found')->callback($this,'action')->output();

	}
	function action($uni,$args=[])
	{
		$uni->main = '
			<h3>404 - Nothing can be found here</h3>
			<h5>Possible reasons:</h5>
			<p>Your mistake: wrong link, typo in your browser-bar etc.</p>
			<p>My mistake: not built yet, wrong link provided etc.</p>
			<p>ANYWAY: ' . a(base,'TAKE ME HOME') . '</p>';
	}
}