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


class MyController extends \lithium\action\Controller {

	 protected function _init() {
  parent::_init();
  $this->_render['layout'] = 'default';
 }
	public function index(){
		return $this->render(array('json' => array("success"=>"Yes")));		
	}
	
	public function sendotp(){
		if($this->request->data){
		
		$mobile = $this->request->data['mobile'];
			if(substr($mobile,0,1)!="+"){
				return $this->render(array('json' => array("success"=>"No")));		
			}
	 $user = Users::find('first',array(
   'conditions'=>array(
				'mobile'=>(string)$mobile,
				)
		));
		if(count($user)==1){
			$mobile = $this->request->data['mobile'];
			
			$ga = new GoogleAuthenticator();
			$otp = $ga->getCode($ga->createSecret(64));	
			$data = array(
				'otp' => $otp,
				);
			$conditions = array("mobile"=>(string)$this->request->data['mobile']);
			
			Users::update($data,$conditions);
			$function = new Functions();
			$msg = "". $otp . " is the OTP for myBitMine to register in the app";
			$returncall = $function->twilio($mobile,$msg,$otp);	 // Testing if it works 
			$returnsms = $function->sendSms($mobile,$msg);	 // Testing if it works 
			$user = Users::find('first',array(
   'conditions'=>$conditions
			));
				return $this->render(array('json' => array("success"=>"Yes","otp"=>$otp,'user'=>$user)));		
		}else{
				$conditions = array("mobile"=>(string)$this->request->data['mobile']);
				$ga = new GoogleAuthenticator();
				$otp = $ga->getCode($ga->createSecret(64));	
				
				$data = array(
					'mobile' => $this->request->data['mobile'],
					'otp' => $otp,
					'DateTime'=>new \MongoDate,
				);
				Users::create()->save($data);
				$function = new Functions();
				$msg = "". $otp . " is the OTP for myBitMine to register in the app";
				$returncall = $function->twilio($mobile,$msg,$otp);	 // Testing if it works 
				$returnsms = $function->sendSms($mobile,$msg);	 // Testing if it works 
				$user = Users::find('first',array(
					'conditions'=>$conditions
					));
				return $this->render(array('json' => array("success"=>"Yes","otp"=>$otp,'user'=>$user)));		
				
		}
	}
	return $this->render(array('json' => array("success"=>"No")));		
	}
	
	public function verifyotp(){ 
		if($this->request->data){
		
			$mobile = $this->request->data['mobile'];
			$otp = $this->request->data['otp'];
			$conditions = array("mobile"=>(string)$this->request->data['mobile'],'otp'=>(string)$this->request->data['otp']);
		
			$user = Users::find('first',array(
   'conditions'=>$conditions,
//			'fields'=>array('name','email','company','role','addresses'),
		));
		
		if(count($user)==1){
			return $this->render(array('json' => array("success"=>"Yes","otp"=>$otp,'user'=>$user,'addresses'=>count($user['addresses']))));		
		}else{
			return $this->render(array('json' => array("success"=>"No")));		
		}
		return $this->render(array('json' => array("success"=>"No")));		
	}
		return $this->render(array('json' => array("success"=>"No Request")));		
}

public function getinfo(){
		if($this->request->data){
			$mobile = $this->request->data['mobile'];
			
			$conditions = array("mobile"=>(string)$this->request->data['mobile']);
			$user = Users::find('first',array(
				'conditions'=>$conditions
			));
	if(count($user)==1){
			return $this->render(array('json' => array("success"=>"Yes",'user'=>$user)));		
		}else{
			return $this->render(array('json' => array("success"=>"No")));		
		}
		return $this->render(array('json' => array("success"=>"No")));		

		}
}

public function adduser(){
	if($this->request->data){
				$data = array(
									'mobile' => $this->request->data['mobile'],
									'email' => strtolower($this->request->data['email']),
									'name' => ucwords($this->request->data['name']),
									'DateJoin' => new \MongoDate(),
									'refer' => $this->request->data['refer_mobile'],
									'role'=>$this->request->data['role'],
									'company'=>$this->request->data['company'],
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
						return $this->render(array('json' => array("success"=>"Yes",'user'=>$user)));		
					}else{
						return $this->render(array('json' => array("success"=>"No")));		
					}
				}else{
						return $this->render(array('json' => array("success"=>"No")));		
				}
	}
}

function addUserJoin($data){
	if($data){
			if($data['mobile']!="" && $data["name"]!=""){
					$refer = Users::first(array(
						'conditions'=>array('mobile'=>(string)$data['refer'])
					));
				if(count($refer)>0){
						$refer_ancestors = $refer['ancestors'];
							$ancestors = array();
							if(count($refer_ancestors)>0){
								foreach ($refer_ancestors as $ra){
									array_push($ancestors, $ra);
								}
							}
					$refer_mobile = (string) $refer['mobile'];

					array_push($ancestors,$refer_mobile);
					
					$refer_left = (integer)$refer['left'];
					$refer_left_inc = (integer)$refer['left'];
					
					Users::update(
						array(
							'$inc' => array('right' => (integer)2)
						),
						array('right' => array('>'=>(integer)$refer_left_inc)),
						array('multi' => true)
					);
					Users::update(
						array(
							'$inc' => array('left' => (integer)2)
						),
						array('left' => array('>'=>(integer)$refer_left_inc)),
						array('multi' => true)
					);
					
					$newData = array(
							'mobile' => (string)$data['mobile'],
							'email' => strtolower($data['email']),
							'name' => ucwords($data['name']),
							'DateJoin' => $data['DateJoin'],
							'refer' => (string)$data['refer'],
							'role'=>$data['role'],
							'company'=>$data['company'],
							'left'=>(integer)($refer_left+1),
							'right'=>(integer)($refer_left+2),
							'ancestors'=> $ancestors,
					);
					
					Users::create()->save($newData);
					return true;
				}else{
					return false;
				}
			}
	}	
}

public function findaddresses(){
	if($this->request->data){
		$mobile = $this->request->data['mobile'];
		$user = Users::find('first',array(
			'conditions'=>array('mobile'=>(string)$mobile)
		));
		$addresses = array();
			foreach($user['addresses'] as $a){
					array_push($addresses, array(
						'address'=>(string)$a['address'],
				));
			}
		
		return $this->render(array('json' => array("success"=>"Yes",'addresses'=>$addresses)));		
	}
	return $this->render(array('json' => array("success"=>"No")));		
}

public function savedata(){
	$data = array(
		'name'=>$this->request->data['name'],
		'email'=>$this->request->data['email'],
		'company'=>$this->request->data['company'],
	);
	$conditions = array(
		'mobile'=>(string)$this->request->data['mobile'],
	);
	Users::update($data,$conditions);
	$user = Users::find('first',array(
		'conditions'=> $conditions,
		'fields'=>array('name','email','company','role'),
	));
	return $this->render(array('json' => array("success"=>"Yes",'user'=>$user)));		
}

public function getaddress(){
	if($this->request->data){
		$conditions = array(
		'mobile'=>(string)$this->request->data['mobile'],
	);
	Users::update($data,$conditions);
	$user = Users::find('first',array(
		'conditions'=> $conditions,
		'fields'=>array('name','email','company','role','addresses'),
	));
	
		return $this->render(array('json' => array("success"=>"Yes",'user'=>$user,'addresses'=>count($user['addresses']))));		
	}
	return $this->render(array('json' => array("success"=>"No")));		
}

public function getlastaddress(){
	if($this->request->data){
		$conditions = array(
		'mobile'=>(string)$this->request->data['mobile'],
		);
		$user = Users::find('first',array(
		'conditions'=> $conditions,
		'fields'=>array('addresses'),
	));
		foreach($user['addresses'] as $a){
						$address=(string)$a['address'];
		}
	return $this->render(array('json' => array("success"=>"Yes",'address'=>$address)));		
	}
	return $this->render(array('json' => array("success"=>"No")));		
}

public function saveaddress(){
		if($this->request->data){
			$conditions = array(
			'mobile'=>(string)$this->request->data['mobile'],
			);
			$user = Users::find('first',array(
				'conditions'=> $conditions,
				'fields'=>array('name','email','company','role','addresses'),
			));
			$addresses = array();
			foreach($user['addresses'] as $a){
					array_push($addresses, array(
						'address'=>(string)$a['address'],
						'sec'=>(string)$a['sec'],
					));
	
			}
			
			array_push($addresses, 
				array(
					'address'=>(string)$this->request->data['addr'],
					'sec'=>(string)$this->request->data['sec'],
				));
			$data = array(
				'addresses'=>$addresses
			);
			//print_r($data);
			Users::update($data,$conditions);
			$user = Users::find('first',array(
				'conditions'=> $conditions,
				'fields'=>array('name','email','company','role','addresses'),
			));
			return $this->render(array('json' => array("success"=>"Yes",'user'=>$user,'addresses'=>count($user['addresses']))));		
		}
		return $this->render(array('json' => array("success"=>"No")));		
	}
	
public function methods(){
	$methods = Methods::find('all',array(
		'order'=>array('name'=>'ASC')
	));
	return $this->render(array('json' => array("success"=>"Yes",'methods'=>$methods)));		
}
	
	
public function posttrade(){
		if($this->request->data){
			
		$data = array(
			'mobile'=>$this->request->data['mobile'],
			'post'=>$this->request->data['post'],
			'transfer'=>$this->request->data['transfer'],
			'currency'=>$this->request->data['currency'],
			'margin'=>$this->request->data['margin'],
			'minimum'=>$this->request->data['minimum'],
			'maximum'=>$this->request->data['maximum'],
			'tradeTerms'=>$this->request->data['tradeTerms'],
			'DateTime'=>new \MongoDate,
		);	
		$conditions = array(
		'mobile'=>$this->request->data['mobile'],
		);
		Trades::create()->save($data);
			return $this->render(array('json' => array("success"=>"Yes")));		
		}
	return $this->render(array('json' => array("success"=>"No")));		
	
}
public function posttradeedit(){
		if($this->request->data){
			
		$data = array(
			'mobile'=>$this->request->data['mobile'],
			'post'=>$this->request->data['post'],
			'transfer'=>$this->request->data['transfer'],
			'currency'=>$this->request->data['currency'],
			'margin'=>$this->request->data['margin'],
			'minimum'=>$this->request->data['minimum'],
			'maximum'=>$this->request->data['maximum'],
			'tradeTerms'=>$this->request->data['tradeTerms'],
			'DateTime'=>new \MongoDate,
		);
		$conditions = array(
		'mobile'=>$this->request->data['mobile'],
		'_id'=>$this->request->data['_id'],
		);
		Trades::update($data,$conditions);
			return $this->render(array('json' => array("success"=>"Yes")));		
		}
	return $this->render(array('json' => array("success"=>"No")));		
	
}
	
public function gettrades(){
	if($this->request->data){
		$trades = Trades::find('all',array(
			'conditions' =>array('post'=>(string)$this->request->data['post']),
			'order'=>array('margin'=>'ASC')
		));
		
		return $this->render(array('json' => array("success"=>"Yes",'trades'=>$trades)));		
	}
	return $this->render(array('json' => array("success"=>"No")));		
}
public function gettrade(){
	if($this->request->data){
		$conditions = array(
				'_id'=>(string)$this->request->data['_id'],
				'mobile'=>(string)$this->request->data['mobile'],
			);
		$trade = Trades::find('first',array(
			'conditions' =>$conditions
		));
		return $this->render(array('json' => array("success"=>"Yes",'trade'=>$trade)));		
	}
	return $this->render(array('json' => array("success"=>"No")));		
}
public function deletetrade(){
	if($this->request->data){
			$conditions = array(
			'mobile'=>(string)$this->request->data['mobile'],
			'_id'=>(string)$this->request->data['_id']
			);
		Trades::remove($conditions);
		return $this->render(array('json' => array("success"=>"Yes")));		
	}
	return $this->render(array('json' => array("success"=>"No")));		
}
	
public function savebank(){
	if($this->request->data){
		$conditions = array(
			'mobile'=>(string)$this->request->data['mobile'],
		);
		$data = array(
			'bankName'=>(string)$this->request->data['bankName'],
			'bankBankName'=>(string)$this->request->data['bankBankName'],
			'bankAccount'=>(string)$this->request->data['bankAccount'],
			'bankType'=>(string)$this->request->data['bankType'],
			'bankIFSC'=>(string)$this->request->data['bankIFSC'],
			'bankBranchCity'=>(string)$this->request->data['bankBranchCity'],
			'bankBranchName'=>(string)$this->request->data['bankBranchName'],
			'bankUPI'=>(string)$this->request->data['bankUPI'],
		);
		Users::update($data,$conditions);
			return $this->render(array('json' => array("success"=>"Yes")));		
		}
	return $this->render(array('json' => array("success"=>"No")));		
	
}
	
public function getbank(){
	if($this->request->data){
		$conditions = array(
			'mobile'=>(string)$this->request->data['mobile'],
		);
		$user = Users::find('first',array(
			'conditions'=>$conditions
		));
	}
	return $this->render(array('json' => array("success"=>"No","user"=>$user)));		
}
	
	
public function commission(){
	$commission = Commissions::find('first');
	return $this->render(array('json' => array("success"=>"Yes",'commission'=>$commission)));		
}
	
}
?>