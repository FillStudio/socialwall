<?php 
/**
 *
 * Twitter Class that implements iAdapter
 * 
 * @author Emanuele Tortolone - www.fillstudio.com
 * 
 * Get public stream of this current Social Network
 * 
 */


// LIB USAGE: https://twitteroauth.com/
require_once(__DIR__.'/../libs/twitter/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;


class SocialWall_Twitter_Adapter implements SocialWall_iAdapter
{
    protected $_params = array(
        "access_token"          => "",
        "access_token_secret"   => "",
        "consumer_key"          => "",
        "consumer_secret"       => "",
        "media_size"            => "small"
    );

    protected $_elements = array();
    protected $_connection;

    //  Public
    public function __construct(){}

    
    /**
    *   @params:
    *       'limit'
    *       'media_size'
    *       'access_token'
    *       'access_token_secret'
    *       'consumer_key'
    *       'consumer_secret'
    */ 
    public function load($params = null)
    {
        //   Merge default params
        if(!empty($params)) {
            $this->_params = array_merge($this->_params, $params);
        }
        
        if(!isset($this->_params['id']) || empty($this->_params['id'])){
            return array();
        }

        // Set connection with Twitter
        $this->_connection = new TwitterOAuth(
            $this->_params['consumer_key'], 
            $this->_params['consumer_secret'], 
            $this->_params['access_token'], 
            $this->_params['access_token_secret']
        );

        // Get User Timeline
        $results = json_decode(json_encode($this->_getUserTimeline()), true);
		//debug($results);
        //  Parse results
        $this->_elements = $this->_parseResults($results);

        return $this->_elements;
    }

    /* ***************************************
    *
    *   Protected Functions
    *
    *   *********************************** */
    protected function _getUserTimeline(){
        return (
            $this->_connection->get(
                "statuses/user_timeline", 
                [
                    "screen_name" => $this->_params['id'],
                    "count" => $this->_params['limit'], 
                    "exclude_replies" => true
                ]
            )
        );
    }

    protected function _parseResults($results){
        $parsed_results = [];
        $c = count($results);
        $media_size = (isset($this->_params["media_size"]) ? (":".$this->_params["media_size"]) : "");
		
        if(!empty($results['errors'])){
            throw(new Exception($results['errors'][0]['message']));
            return [];
        }
        for ($i = 0; $i < $c; $i++):
            $r = $results[$i];
            $parsed_results[$i] = array(
                "id" => $r['id'],
                "date" => strtotime($r['created_at']),
                'formatted_date'=>date('Y-m-d H:i:s',strtotime($r['created_at'])),
                "text" => $r['text'],
                "media" => isset($r['entities']['media'][0]['media_url']) ? ($r['entities']['media'][0]['media_url'].$media_size) : '',
                "permalink" => "https://twitter.com/".$this->_params['id']."/status/".$r['id']
            );
        endfor;

        return $parsed_results;
    }
}
?>