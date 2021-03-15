<?php
namespace app\controllers;
use app\models\Wallets;
use app\models\Countries;
use app\models\Ipv6s;
use app\extensions\action\GoogleAuthenticator;
use lithium\util\Validator;
use app\extensions\action\Functions;
use \lithium\template\View;


class CodeController extends \lithium\action\Controller {

	public function say($code){
		$newcode = '';
		for($i=0;$i<=strlen($code);$i++){
			$newcode = $newcode . substr($code,$i,1).',,,,,';
		}
		
	$layout = false;
	$view  = new View(array(
		'paths' => array(
			'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
			'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
		)
		));
		echo $view->render(
		'all',
		compact('newcode'),
		array(
			'controller' => 'code',
			'template'=>'say',
			'type' => 'xml',
			'layout' =>'default'
		)
		);	
		return $this->render(array('layout' => false));
		
	}
 
 public function call($date, $time, $title, $speaker){
	$date = urldecode($date);
 $time = urldecode($time);
 $title = urldecode($title);
 $speaker = urldecode($speaker);
	$layout = false;
	$view  = new View(array(
		'paths' => array(
			'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
			'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
		)
		));
		echo $view->render(
		'all',
		compact('date','time', 'title', 'speaker'),
		array(
			'controller' => 'code',
			'template'=>'call',
			'type' => 'xml',
			'layout' =>'default'
		)
		);	
		return $this->render(array('layout' => false));
		
	}
 
 public function msg ($message){
 $layout = false; 
   $layout = false;
   $view  = new View(array(
    'paths' => array(
     'template' => '{:library}/views/{:controller}/{:template}.{:type}.php',
     'layout'   => '{:library}/views/layouts/{:layout}.{:type}.php',
    )
    ));
    echo $view->render(
    'all',
    compact('mobile','message'),
    array(
     'controller' => 'code',
     'template'=>'say',
     'type' => 'xml',
     'layout' =>'default'
    )
    );	
    return $this->render(array('layout' => false));
  }
 }
 
 
?>