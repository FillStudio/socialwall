<?php

/**
 *
 * SocialWall Class 
 * 
 * @author Aldo Zorzi & Emanuele Tortolone - www.fillstudio.com
 * 
 * Retrieve public stream from Social Network and store into an array
 * 
 */

class SocialWall
{
	protected $_socials = array();
	protected $_elements = array();
    	protected $_email = '';

    
	public function __construct()
	{
		require_once('Adapters/iAdapter.php');
	}
	
    /** 
     * @return Array
     * 
     * @params $socials
     * Array with the Adatper params (one for each social to retrieve)
     */
	public function load($socials = null)
	{
		if(!empty($socials)) $this->_socials = $socials;
		foreach($socials as $socialName => $socialParams)
		{
			$socialName = ucfirst($socialName);
			require_once('Adapters/'.$socialName.'.php');
			$adapter = 'SocialWall_'.$socialName.'_Adapter';
			$socialLoader = new $adapter();
			$socialResult = array();
			try {
				$socialResult = $socialLoader->load($socialParams);
				if(empty($socialResult))
				{
                    $this->_sendLogEmail($socialName,"Empty response\nParams:\n".print_r($socialParams,true));
				}
			} catch (Exception $e) {
                $this->_sendLogEmail($socialName,$e->getMessage()."\nParams:\n".print_r($socialParams,true));
			}
			foreach($socialResult as $result)
			{
                $result['social'] = $socialName;
                $this->_elements[] = $result;
			}
		}
		$this->_orderElements();
		return $this->_elements;
	}
        
	public function setLogEmail($email = ''){
		if(!empty($email)){
            		$this->_email = $email;
		}
	}

    /* ***************************************
    *
    *   Protected Functions
    *
    *   *********************************** */
    
    /** 
     * Order the elements by 'date' value
     */
	protected function _orderElements()
	{
	        usort($this->_elements,function($a,$b){
	            if(empty($a['date']) || empty($b['date'])) return 0;
	            return date('YmdHis',$a['date']) < date('YmdHis',$b['date']);
	        });
	}
        
    /** 
     * If one single service will fail, an email will be sent to the admin, in order to fix the problem
     *
     * @param string $social - Social network code name
     * @param string $message - Message to write in content email
     */
	protected function _sendLogEmail($social,$message){
		if(!empty($this->_email)){
	            $to      = $this->_email;
	            $subject = 'Social Wall Error on '.$_SERVER['HTTP_HOST'];
	            $content = 'New error on '.$_SERVER['HTTP_HOST'].' for social with code: '.$social."\n";
	            $content .= !empty($message) ? $message : '';
	            $headers = 'From: '.$this->_email . "\r\n" .
	                    'Reply-To: '.$this->_email . "\r\n" .
	                    'X-Mailer: PHP/' . phpversion();
	            mail($to, $subject, $content, $headers);
		}
	}
}
?>