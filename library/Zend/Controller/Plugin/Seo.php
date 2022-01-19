<?php
class Zend_Controller_Plugin_Seo extends Zend_Controller_Plugin_Abstract
{

	public function __construct($ctrl)
	{
		$request = new Zend_Controller_Request_Http();
		
		$url_helper = explode("?",$request->getRequestUri());
		$url = '/' . trim($url_helper[0],'/');
		$extension = end(explode(".", $url));
		//$url_parts = explode("/",$url_helper[0]);
		
		if( $extension != 'php' && $extension != 'html') $url .= '/';
	}

}
?>