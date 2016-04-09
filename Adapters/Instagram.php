<?php
/**
 *
 * Instagram Class that implements iAdapter
 * 
 * @author Emanuele Tortolone - www.fillstudio.com
 * 
 * Get public stream of this current Social Network
 * 
 */

class SocialWall_Instagram_Adapter implements SocialWall_iAdapter
{
    protected $_params = array(
        "client_id"          => "",
        "client_secret"      => "",
        "media_size"         => "standard"
    );    
        
    protected $_elements = array();

    public function __construct(){

    }
    
    
    /**
    *   @params:
    *       'limit'
    *       'client_id'
    *       'client_secret' 
    *       'media_size'(optional)
    */  
    public function load($params = null)
    {
        //   Merge default params
        if(!empty($params)) {
            $this->_params = array_merge($this->_params, $params);
        }
        
        //  get recent media
        $results = json_decode($this->_getRecentMedia(), true);
        
        //  Parse results
        $this->_elements = $this->_parseResults($results);
        
        return $this->_elements;
    }


    /* ***************************************
    *
    *   Protected Functions
    *
    *   *********************************** */
    protected function _getRecentMedia(){
        if(empty($this->_params['client_id']))
        {
            throw(new Exception("params[client_id]"));
            return json_encode([]);
        }
        $json_link="https://api.instagram.com/v1/users/". (!empty($this->_params["id"]) ? $this->_params["id"] : 'null')."/media/recent/?";
        $json_link.="client_id=".$this->_params["client_id"]."&count=".$this->_params["limit"];
        
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $json_link); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);      
		
        return $output;
    }
    
    
    protected function _parseResults($results){
        if(empty($results['data'])) return array();
        foreach ($results['data'] as $r):
            $parsed_results[] = array(
                "id" => $r['id'],
                "date" => $r['created_time'],
                'formatted_date'=>gmdate('Y-m-d H:i:s',$r['created_time']),
                "text" => (!empty($r['caption']['text'])) ? $r['caption']['text'] : '',
                "media" => (isset($this->_params["media_size"]) && $this->_params["media_size"] === 'standard') ? ($r['images']['standard_resolution']['url']) : ($r['images']['low_resolution']['url']),
                "permalink" => $r['link']
            );
        endforeach;
        return $parsed_results;
    }
}
?>