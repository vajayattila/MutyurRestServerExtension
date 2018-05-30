<?php

/**
 *  @file songcontestwebserver.php
 *  @brief Songcontestweb RestFul server. Project home: https://github.com/vajayattila/MutyurRestServer
 *	@author Vajay Attila (vajay.attila@gmail.com)
 *  @copyright MIT License (MIT)
 *  @date 2017.06.12-2017.06.15
 *  @version 1.0.0.0
 */

require_once ('restserver.php');

class demorestserver extends restserver{

    function __construct(){
        parent::__construct();
        // dependency
	    demorestserver::setup_dependencies(
            demorestserver::get_class_name(), 
            demorestserver::get_version(), 
            'extension',
			array(
				'restserver'=>'1.0.0.2',
			)
		);
        // Register methods
        $this->addfunction('GET', 'echo');
        $this->addfunction('POST', 'echo');
    }

    public function get_class_name(){
	return 'demorestserver';
    }

    public function get_version(){
	return '1.0.0.0';
    }
    
    /*
        GET: http://127.0.0.1:8001/service?action=echo&message=helloworld
        POST: curl -X POST -i 'http://127.0.0.1:8001/service' --data '{"action": "echo","message": "Hello World!"}'
    */    
    protected function echo(){
        $return=TRUE;        
        $echo=$this->getfieldvalue('message', TRUE, '^.{5,20}$'); // Min length is 5, max length is 20, required
        if(FALSE!==$echo){
            $ret=array(
                'echo' => $echo
            );
            if($this->fieldisexist('partners')){
                $partnerecho=array();
                $partnerarr=$this->getfieldvalue('partners'); // Get partners array
                foreach($partnerarr as $key => $partner){
                    $partner_name=$this->getfieldvalue('name', TRUE, '^.{10,30}$', $partner); // min10, max30
                    if (false!==$partner_name) {
                        $partnerecho[]=array(
                            "name" => $partner_name
                        );
                    } else {
                        $return=FALSE;
                        // in case of an error getparamvalue write error messages to output
                        echo "in partners array on index: $key\n"; // print additional info
                    }
                }
                if ($return!==false) {
                    $ret["partner"]=$partnerecho;
                }
            }
            if ($return!==false) {
                $this->response($ret); // Response
            }
        } else {
            // in case of an error getparamvalue write error messages to output
            $return=FALSE;
        }
        return $return;
    }

}
