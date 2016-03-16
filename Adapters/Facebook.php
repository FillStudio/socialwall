<?php 
/**
 *
 * Facebook Class that implements iAdapter
 * 
 * @author Aldo Zorzi - www.fillstudio.com
 * 
 * Get public stream this current Social Network
 * 
 */


// LIB USAGE: https://github.com/facebook/facebook-php-sdk-v4/
require_once(__DIR__.'/../libs/facebook/autoload.php');

class SocialWall_Facebook_Adapter implements SocialWall_iAdapter
{
	protected $_params = array();
	protected $_elements = array();
	
	public function __construct(){
		
	}
	

    /**
    *   @params:
    *       'limit'
    *       'api_key'
    *       'api_secret'
    *       'app_token'
    */ 
	public function load($params = null)
	{
		
		if(!empty($params)) {
			$this->_params = $params;
		}
		if(!isset($this->_params['id']) || empty($this->_params['id'])){
			return array();
		}
                
		$fb = new Facebook\Facebook([
		  'app_id' => $this->_params['api_key'],
		  'app_secret' => $this->_params['api_secret'],
		  'default_graph_version' => 'v2.5',
		  'default_access_token' => $this->_params['app_token']
		  ]);
                
	        $fields = array(
	            'id',
	            'created_time',
	            'message',
	            'caption',
	            'icon',
	            'link',
	            'object_id',
	            'picture',
	            'full_picture',
	            'source',
	            'type',
	            'properties'
	        );
                
		$posts = $fb->get($this->_params['id'].'/posts/?fields='.implode(',', $fields).'&limit='.$this->_params['limit'])->getDecodedBody()['data'];
		
		foreach($posts as $post){
	            $this->_elements[] = array(
	                'id'=>$post['id'],
	                'date'=>strtotime($post['created_time']),
	                'formatted_date'=>date('Y-m-d H:i:s',strtotime($post['created_time'])),
	                'text' => isset($post['message']) ? $post['message'] : '',
	                'media'=> isset($post['full_picture']) ? $post['full_picture'] : (isset($post['picture']) ? $post['picture'] : ''),
	                'permalink' => isset($post['link']) ? $post['link'] : '',
	                'type'=>$post['type'],
	                'icon'=> isset($post['icon']) ? $post['icon'] : '',
	            );
		}
		
		return $this->_elements;
	}
}
?>
