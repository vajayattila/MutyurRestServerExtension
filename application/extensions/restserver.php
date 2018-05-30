<?php

if (! defined ( 'mutyurphpmvc_inited' ))
	exit ( 'No direct script access allowed' );
	
/**
 *  @file restserver.php
 *  @brief RestFul server extension base class for MutyurPHPMVC. Project home: https://github.com/vajayattila/MutyurRestServer
 *  @author Vajay Attila (vajay.attila@gmail.com)
 *  @copyright MIT License (MIT)
 *  @date 2017.06.12-2018.05.29
 *  @version 1.0.0.0
 */

 /**
  *  @brief Mütyür RestFul server base class 
  */
class restserver extends helper{
	protected $method;
	protected $action;
	protected $registredActions=[];
	protected $requestArgs=[];
	protected $statusCode;
	protected $contentType;

	function __construct(){
		$this->statusCode=200;
		$this->contentType="application/json";
		// Dependency
		restserver::setup_dependencies(
			restserver::get_class_name(), restserver::get_version(), 'extension',
			array('helper'=>'1.0.0.1')
		);
		// for register acctions
		$this->registeractions();
	}

	protected function registeractions(){
		$this->registredActions=[
			'GET' => ['restsrv_getversion'],
			'POST' => ['restsrv_getversion'],
			'PUT' => [],
			'DELETE' => []
		];
	}        

	public function get_class_name(){
		return 'restserver';
	}

	public function get_version(){
		return '1.0.0.2';
	}

	protected function get_content_type(){
		return $_SERVER["CONTENT_TYPE"];
	}

	protected function getAction($method){
		$act=false;
		switch(strtoupper($method)){
			case 'GET':
				$this->requestArgs = $this->cleanInputs($_GET);
				break;
			case 'POST':
				$postdata=[];
				$this->log_message('debug', print_r($this->get_content_type(),true));
				if(
					(strpos(strtolower($this->get_content_type()),'text/plain')!==false) ||
					(strpos(strtolower($this->get_content_type()),'application/json')!==false)
				){
					$this->log_message('debug', print_r(json_decode(file_get_contents("php://input")),true));					
					$postdata=json_decode(file_get_contents("php://input"), true);
					if($postdata===NULL){
						$jsonerror=json_last_error_msg();
						echo "JSON decode status: $jsonerror";
						exit();
					}
				} else {
					$postdata=$_POST;
				}
				$this->requestArgs = $this->cleanInputs($postdata);
				$this->requestArgs = array_merge($this->requestArgs, $this->cleanInputs($_GET));
				break;
			case 'PUT':
			case 'DELETE':
				parse_str(file_get_contents("php://input"), $this->requestArgs);
				$this->requestArgs = $this->cleanInputs($this->requestArgs);                
				break;
			default:
				$this->response('Method Not Allowed',405);
		}
		$temp=isset($this->requestArgs['action'])?$this->requestArgs['action']:NULL;
		if($temp!==NULL){
			if(in_array($temp, $this->registredActions[$method])){
				$act=$temp;
			} else {
				$this->response('Unknown action', 400);    
			}   
		} else {
			$this->response('The action parameter is not set', 400);    
		}
		return $act;        
	}

	protected function cleanInputs($data){
		$clean_input = array();
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$clean_input[$k] = $this->cleanInputs($v);
			}
		} else {
			if(get_magic_quotes_gpc()) {
				$data = trim(stripslashes($data));
			}
			$data = strip_tags($data);
			$clean_input = trim($data);
		}
		return $clean_input;
	}     

	protected function get_status_message(){
		$status = array(
		200 => 'OK', 
		204 => 'No Content',  
		400 => 'Bad request',
		404 => 'Not Found',  
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		500 => 'Internal Server Error');
		$code=isset($status[$this->statusCode])?$this->statusCode:500;
		return $status[$code];
	}

	protected function set_headers() {
		header("HTTP/1.1 ".$this->statusCode." ".$this->get_status_message());
		header("Content-Type:".$this->contentType);
		header('Access-Control-Allow-Origin: *'); 
		header('Access-Control-Allow-Headers: *'); 
		header('Access-Control-Allow-Methods: *');
	}

	protected function response($data, $status = 200) {
	   $this->statusCode = $status;
	   $this->set_headers();
	   if (is_array($data)){
		$this->log_message('debug', "response.json=".json_encode($data, JSON_PRETTY_PRINT));
		  echo json_encode($data, JSON_PRETTY_PRINT);
	   }
	   else{
		  echo $status!=200?$data.' ('.$status.')':$data;
	   }
	   exit;
	}     

	public function execute(){
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->action = $this->getAction($this->method);
		$this->{$this->action}();    
	}

	protected function restsrv_getversion(){
		$vers=array(
			'name' => $this->get_class_name(),
			'version' => $this->get_version(),
		);
		$this->response($vers);
	}

	/**
	 * @brief Register a callable method
	 */
	public function addfunction($method/*'GET','POST','PUT','DELETE'*/, $name){
		$this->registredActions[$method][]=$name;
	}

	/**
	 * @brief Get a filed value by type and filters
	 * @param $name the name of filed name or array index
	 * @param $required the field is required or not
	 * @param $regexpattern only for 'field' type
	 * @param $node if $node is NULL then $node=$this->requestArgs
	 */
	public function getfieldvalue($nameorindex, $required=FALSE, $regexpattern=NULL, $node=NULL){
		$return=FALSE;
		if($node===NULL){
			$node=$this->requestArgs;
		}
		if($this->fieldisexist($nameorindex, $node)){
			// check field type
			$value=$node[$nameorindex];
			if(is_object($value)){
				$return=$value; // return the object
			} else if (is_array($value)){
				// is associative
				$return=$value; // return array
			} else {
				if($regexpattern!==NULL){
					$subject = "abcdef";
					$pattern = '/'.$regexpattern.'/';
					$regexret=preg_match($pattern, $value, $matches, PREG_OFFSET_CAPTURE);
					if($regexret===1){
						$return=$value;
					} else if($regexret===0){
						echo "Pattern not match in '$nameorindex' field. The value is '$value'. The pattern is '$pattern'\n";	
					} else {
						echo "Regex error. Parameter name is '$nameorindex'. The value is '$value'. The pattern is '$pattern'\n";
					}	
				} else {
					$return=$value;
				}
			}
		} else {
			if($required===TRUE){
				echo "Missing field: $nameorindex\n";
			}else{
				$return=NULL;
			}
		}
		return $return;
	}

	public function fieldisexist($name, $node=NULL){
		if($node===NULL){
			$node=$this->requestArgs;
		}
		return isset($node[$name]);		
	}

}

