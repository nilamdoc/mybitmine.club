<?php
namespace app\controllers;
use lithium\storage\Session;
use \lithium\template\View;
use app\extensions\action\Functions;
use app\extensions\action\GoogleAuthenticator;
use app\models\Users;
use app\models\Methods;
use app\models\Trades;
use app\models\Commissions;


class XController extends \lithium\action\Controller {
 
 public function add($refer=null){
  if($this->request->data){
				$data = array(
									'mobile' => $this->request->data['mobile'],
									'DateJoin' => new \MongoDate(),
									'refer' => $this->request->data['refer'],
        );
				$conditions = array("mobile"=>(string)$this->request->data['mobile']);
				$user = Users::find('first',array(
					'conditions'=>$conditions
				));
				
				if(count($user)==0){
					if($this->addUserJoin($data)==true){
						$conditions = array("mobile"=>(string)$this->request->data['mobile']);
						$user = Users::find('first',array(
							'conditions'=>$conditions
						));
						return compact('data');
					}else{
						return compact('data');
					}
				}else{
						return compact('data');
				}
    
  }else{
   return compact('refer');
  }
}
 
}