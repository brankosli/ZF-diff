<?php

use slack\Http\SlackResponseFactory;
use slack\Http\CurlInteractor;
use slack\Core\Commander;


class Zend_Controller_Plugin_ProfilerCli extends Zend_Controller_Plugin_Abstract
{	
  public function postDispatch()
  {
	
    global $db;
    $request = new Zend_Controller_Request_Http();

    $profiler = $db->getProfiler();

    // we set the filter
    $profiler->setFilterQueryType(
                        Zend_Db_Profiler::SELECT |
                        Zend_Db_Profiler::INSERT |
                        Zend_Db_Profiler::UPDATE |
                        Zend_Db_Profiler::DELETE);

    $profile = $profiler->getQueryProfiles(
                        Zend_Db_Profiler::SELECT |
                        Zend_Db_Profiler::INSERT |
                        Zend_Db_Profiler::UPDATE |
                        Zend_Db_Profiler::DELETE);    

    //$logs = new Logs_Model_DbTable_Log();

     /* if the filter has no content the is null 
         otherwise it will be an array and we have
         to prevent if to get no error here */
     if(is_array($profile))
     {
          //echo '<pre>';
          foreach($profile as $row)
          {
              if( $row->getElapsedSecs() > 5 )
              {
              	$auth = Zend_Auth::getInstance();
				$user = $auth->getIdentity();

                $content =  "URL: https://" . __jbcnf('vhosts.stitchindustries.domain') . $request->getRequestUri() . "\n" .
                			"Worker name: ". $user->real_name . "\n" .
                			"IP: ". $user->ip_address . "\n" .
                            "Execution time: " . round($row->getElapsedSecs(),4)  . "\n\n" . 
                            "Query: \n" . $row->getQuery(). "\n\n".
                            "Extra params: \n". implode(', ', $row->getQueryParams());

                $interactor = new CurlInteractor;
                $interactor->setResponseFactory(new SlackResponseFactory);
                $commander = new Commander(__jbcnf('services.slack.token'), $interactor);
                
                $response = $commander->execute('files.upload', [
                    'channels' => __jbcnf('notifications.slow_queries.slack.channel'),
                    'content' => $content,
                    'filetype' => 'text',
                    'title' => 'Slow Query on page'
                ]);

                //$logs->insert($data);
                //var_dump($data);
            }
          }
     }
  }
}
?>