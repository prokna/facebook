<?php

/***********************************************************************************************************************************
	Author - Prokna ( http://github.com/prokna )
	PHP class for facecook Graph API
***********************************************************************************************************************************/


class fbgraph{

	var $app_id ;
    var $app_secret ;
    var $callbackUrl;
	var $access_token;
	
		
	/*

	 * Constructor: Creates the Forsquare class object.
	
	 * @param string $app_id (Required)
	
	 * @param string $app_secret (Required)

	 * @param string $callbackUrl (Required)

	 */

		function __construct($app_id,$app_secret,$callbackUrl){
			   
			   $this->app_id =$app_id;
			   $this->app_secret = $app_secret;
			   $this->callbackUrl = $callbackUrl;

        }


	
		/* If you have a access token ,this function will set */
		function setToken($tok){
		
				$this->access_token = $tok;

		}
	


	
		/*
		 * User Authorize your application
		 * your app needs more than this basic information to function, you must request specific permissions 
		 */		
		function login($perms){

				$code = $_REQUEST["code"];
			
					if(empty($code)) { 
											 
						$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
							. $this->app_id . "&redirect_uri=" . urlencode($this->callbackUrl)."&scope=".$perms;  
						
						
						//Send it to fb
						echo("<script> top.location.href='" . $dialog_url . "'</script>");
					}

					$token_url = "https://graph.facebook.com/oauth/access_token?client_id="
						. $this->app_id . "&redirect_uri=" . urlencode($this->callbackUrl) . "&client_secret="
						. $this->app_secret . "&code=" . $code;

					$this->access_token = "?".file_get_contents($token_url);
					return $this->access_token;
		}



		/*
		 * This function create a graph url to access the user's data using access token.
		 * Then json_decode function return the content of graph urls's.
		 */

		function me(){

			
				 $graph_url = "https://graph.facebook.com/me" . $this->access_token;
				 $user = json_decode(file_get_contents($graph_url));
				 $usr_info = $this->object2array($user);   //return json object to array
				 //echo '<pre>';print_r($usr_info);
				 return $usr_info;

		}



		/* This function return the user's firnds list.with thier ids */
		function friends(){
			
				$frnd_json = @file_get_contents("https://graph.facebook.com/me/friends/".$this->access_token);
				$frnd_data=$this->json2array($frnd_json);
				$friends=$frnd_data['data'];
				return $friends;

		}

	

		/*This function provide the complete informention about user friend's whose id is given */
		function friend($frnd_id){

				$info_json = @file_get_contents("https://graph.facebook.com/".$frnd_id."/".$this->access_token); 
				$frnd_info = $this->json2array($info_json);
				return $frnd_info;

		}
	
	
		/*
		 * This function return the posts .
		 * By default this function show the posts of user.
		 * To see post of particular friend define his id
		 */	
		function feed($user_id='me'){
					
				$feed_json = @file_get_contents("https://graph.facebook.com/".$user_id."/feed/".$this->access_token); //user's feed
				$usr_feed = $this->json2array($feed_json);
				$feed = $usr_feed['data'];
				return $feed;

		}



		/*This function ables you to like any post,by its id.*/
		
		function like($obj_id){
		
				$url = "https://graph.facebook.com/".$obj_id."/likes";
				
				list($raw,$mix_str)=explode("=",$this->access_token);
				list($access_token_topost,$raw)=explode("&",$mix_str);

				$post_data = array ("access_token" => $access_token_topost);
				
				$out_put = $this->do_post($url, $post_data);
				
				return $out_put;
	
		}



		/*
		 * This function ables you to post on wall.
		 * You can post link or msg.
		 */

		function post_on_wall($access_token='',$msg='',$link='',$id='me'){
					
				//to post on user's wall.

				if($access_token!='') $this->access_token = $access_token;

				$url = "https://graph.facebook.com/".$id."/feed";
				
				list($raw,$mix_str)=explode("=",$this->access_token);  //this covert the access token in our required form
				list($access_token_topost,$raw)=explode("&",$mix_str);

				$post_data = array ("access_token" => $access_token_topost,
									"message" => $msg,
									"link" => $link);
				$out_put = $this->do_post($url, $post_data);

				$out_arr = $this->json2array($out_put);
				return $out_arr;

		}
		
		
		function do_post($url, $post_data){
				 
				
				$ch = curl_init();   //initialisation of the cURL

				curl_setopt($ch, CURLOPT_URL, $url);    

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				// we are doing a POST request
				curl_setopt($ch, CURLOPT_POST, 1);
				// adding the post variables to the request
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

				$output = curl_exec($ch);

				curl_close($ch);

				return $output;
			
		}


		/* function convert json in array*/	
		function json2array($json){
				
				// this function convert json to array 

				$data_obj = json_decode($json);
				$data_arr=$this->object2array($data_obj);
				
				return $data_arr;
		}
	
		
		function object2array($object){
				$return = NULL;
				  
				if(is_array($object))
				{
					foreach($object as $key => $value)
						$return[$key] = $this->object2array($value);
				}
				else
				{
					$var = get_object_vars($object);
					  
					if($var)
					{
						foreach($var as $key => $value)
							$return[$key] = ($key && !$value) ? NULL : $this->object2array($value);
					}
					else return $object;
				}

				return $return;
			} 
	
}


?>