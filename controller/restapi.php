<?php

require_once(__DIR__.'/rest.inc.php');

class Restapi extends Rest{

	function __construct(){
		parent::__construct();
	}

	private function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}

	public function processApi(){

		$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
		if((int)method_exists($this,$func) > 0)
			$this->$func();
		else
			$this->response('Bad Request',404);
	}

	private function test(){
		
		if($this->get_request_method() == "GET"){
		 		$this->response('test works',200);
		}
	}
}

$restApi = new Restapi();
$restApi->processApi();

?>