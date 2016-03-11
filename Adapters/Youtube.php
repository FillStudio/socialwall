<?php
/**
 *
 * Youtube Class that implements iAdapter
 * 
 * @author Aldo Zorzi - www.fillstudio.com
 * 
 * Get public stream this current Social Network
 * 
 */


// LIB USAGE: https://developers.google.com/api-client-library/php/
require_once(__DIR__.'/../libs/youtube/autoload.php');

class SocialWall_Youtube_Adapter implements SocialWall_iAdapter
{
	protected $_params = array('media_size'=>'high');
	protected $_elements = array();
	
	public function __construct(){
	}
	
    
    /**
    *   @params:
    *       'limit'
    *       'api_key'
    *       'media_size' (optional)
    */ 
	public function load($params = null)
	{
		if(!empty($params)) {
            		$this->_params = array_merge($this->_params, $params);
        	}

        	if(!isset($this->_params['id']) || empty($this->_params['id'])){
            		return array();
        	}
		
		$client = new Google_Client();
		$client->setApplicationName("Abarth Social Wall Youtube Adapter");
		$client->setDeveloperKey($this->_params['api_key']);
		
		$youtube = new Google_Service_YouTube($client);

	        $videos = $youtube->search->listSearch('snippet',array(
	            'channelId'=>$this->_params['id'],
	            'order'=>'date',
	            'maxResults'=>$this->_params['limit']
	        ))->getItems();

		foreach($videos as $video)
		{
			$description = $video->getSnippet()->getDescription();
			$this->_elements[] = array(
				'id'=>$video->getId()->getVideoId(),
				'date'=>strtotime($video->getSnippet()->getPublishedAt()),
				'formatted_date'=>date('Y-m-d H:i:s',strtotime($video->getSnippet()->getPublishedAt())),
				'text' => $video->getSnippet()->getTitle().(!empty($description) ? ' - ' : '').$video->getSnippet()->getDescription(),
				'media'=> $video->getSnippet()->getThumbnails()[$this->_params['media_size']]['url'],
				'permalink' => 'https://youtu.be/'.$video->getId()->getVideoId()
			);
		}
		
		return $this->_elements;
	}
}
?>
