<?php 
class Zend_Controller_Plugin_BackboneAnalytics extends Zend_Controller_Plugin_Abstract
{
  protected $_startTime;
  
  public function __construct()
  {
  	$this->_startTime = date('Y-m-d H:i:s');
  }
  
  public function postDispatch()
  {
    global $db;
    $request = new Zend_Controller_Request_Http();

   
    $auth = Zend_Auth::getInstance();
	$user = $auth->getIdentity();
	
	$url_helper = explode("?",$request->getRequestUri());
	$url = '/' . trim($url_helper[0],'/');
	if($user->id && $request->getRequestUri())
		$db->query("INSERT INTO `backbone_activity_log`(`fk_user_id`, `user_name`, `request_uri`, `clean_url`,start_time) VALUES (?,?,?,?,?)",array($user->id,$user->real_name,$request->getRequestUri(),$url,$this->_startTime));
  }
}

?>