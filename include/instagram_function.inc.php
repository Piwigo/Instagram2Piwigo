<?php 

  $auth_config = array(
      'client_id'     => $conf['Instagram2Piwigo']['api_key'],
      'client_secret' => $conf['Instagram2Piwigo']['secret_key'],
	  'begin_sync_date' => $conf['Instagram2Piwigo']['begin_sync_date'],
	  'scope' => 'user_profile,user_media',
	  'redirect_uri'  => get_absolute_root_url().'instagram2piwigo-callback-url',
	  'authUrl' => 'https://api.instagram.com/oauth/authorize',
	  'tokenUrl' => 'https://api.instagram.com/oauth/access_token',
	  'graphApiUrl' => 'https://graph.instagram.com/'
	  );	

/* *** *** ****** *** *** */
/* Authentification insta */
/* *** *** ****** *** *** */
function instaToPiwigo_Authentifier($auth_config)
{
	header(
		sprintf(
			'Location:'.$auth_config['authUrl'].'?client_id=%s&redirect_uri=%s&response_type=code&scope=%s',
			$auth_config['client_id'],
			$auth_config['redirect_uri'],
			$auth_config['scope']
		)
	);
}

/* *** *** *** *** *** */
/* Get API token insta */
/* *** *** *** *** *** */
function instaToPiwigo_GetToken($auth_config, $authCode)
{
	$curlPostVar = array(
	  'client_id'     => $auth_config['client_id'],
	  'client_secret' => $auth_config['client_secret'],
	  'grant_type' => "authorization_code",
	  'redirect_uri'  => $auth_config['redirect_uri'],
	  'code' => $authCode
	  );
	
	
	$ch = curl_init();
	try {
		curl_setopt($ch, CURLOPT_URL, $auth_config['tokenUrl']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //A supprimer - ne devrait pas etre nécessaire sur un serveur configuré correctement
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPostVar );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$instaGetTokenResponse = curl_exec($ch);		
	
	    if (curl_errno($ch)) {
			echo curl_error($ch);
			die();
		}
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($http_code == intval(200)){
			$instaGetTokenResponseArray = json_decode($instaGetTokenResponse, true);
			
			$_SESSION['instagram_access_token'] = $instaGetTokenResponseArray['access_token'];
			$_SESSION['user_id'] = $instaGetTokenResponseArray['user_id'];		
		}
		else{
			echo "raw: ".$instaGetTokenResponse;
			echo '<br>';
			echo "Ressource introuvable : " . $http_code;
		}
	} catch (\Throwable $th) {
		throw $th;
	} finally {
		curl_close($ch);
	}	
}


/* *** *** *** *** *** */
/* Récupère user Insta */
/* *** *** *** *** *** */
function instaToPiwigo_GetUser($auth_config, $instagram_access_token)
{
	$curlGetVar = array(
	  'fields'     => 'id,username',
	  'access_token' => $instagram_access_token
	  );
		
	$ch = curl_init();
	try {
		curl_setopt($ch, CURLOPT_URL, $auth_config['graphApiUrl']."me?".http_build_query($curlGetVar));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //A supprimer - ne devrait pas etre nécessaire sur un serveur configuré correctement
	
		$instaGetUserResponse = curl_exec($ch);		
	
	    if (curl_errno($ch)) {
			echo curl_error($ch);
			die();
		}
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($http_code == intval(200)){
			$instaGetUserResponseArray = json_decode($instaGetUserResponse, true);
			$_SESSION['insta_userid'] = $instaGetUserResponseArray['id'];
			$_SESSION['insta_username'] = $instaGetUserResponseArray['username'];
		}
		else{
			$error_m = json_decode($instaGetUserResponse, JSON_OBJECT_AS_ARRAY);
			if( $http_code == intval('400') && $error_m['error']['code']== '190')
			{
				unset($_SESSION['instagram_access_token']);
				$_SESSION['page_infos'][] = l10n('Session expired');
				redirect(INSTAG_ADMIN . '-import');
			}
			elseif( $http_code == intval('403') && $error_m['error']['code']== '4')
			{
				$_SESSION['page_infos'][] = l10n('Application request limit reached');
			}
			else
			{
				echo "raw: ".$instaGetUserResponse;
				echo '<br>';
				echo "Ressource introuvable : " . $http_code;				
			}
		}
	} catch (\Throwable $th) {
		throw $th;
	} finally {
		curl_close($ch);
	}	
}


/* *** *** ******** *** *** */
/* Récupère mes media Insta */
/* *** *** ******** *** *** */
function instaToPiwigo_GetUserMedia($auth_config, $instagram_access_token, $user_id, $user_name, $before = null, $after = null)
{
	$instaImages=array();
	$prev = "";
	$next = "";
	
	$curlGetVar = array(
	  'fields'     => 'id,media_type,media_url,timestamp,caption,children',
	  'access_token' => $instagram_access_token,
	  'limit' => 40
	  );
	  
	if(isset($before))
	{$curlGetVar['before']=$before;}
	else if(isset($after))
	{$curlGetVar['after']=$after;}
	
	
	$ch = curl_init();
	try {
		curl_setopt($ch, CURLOPT_URL, $auth_config['graphApiUrl'].$user_id."/media?".http_build_query($curlGetVar));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //A supprimer - ne devrait pas etre nécessaire sur un serveur configuré correctement
	
		$instaGetMediaResponse = curl_exec($ch);		
	
	    if (curl_errno($ch)) {
			echo curl_error($ch);
			die();
		}
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($http_code == intval(200)){

			$instaGetMediaResponseArray = json_decode($instaGetMediaResponse, true);
			$dateLimitReached = false;
			
			foreach ($instaGetMediaResponseArray['data'] as $key => $instaMedia)
			{				
				if(is_array($instaMedia))
				{
					//date formatée du média courant
					$instaMediaDate = date_format(date_create_from_format("Y-m-d\TH:i:s",substr($instaMedia['timestamp'],0,19)),"Y-m-d");
		
					
					if(is_null($auth_config['begin_sync_date']) || (!is_null($auth_config['begin_sync_date']) && $instaMediaDate > $auth_config['begin_sync_date'] ))
					{
						if($instaMedia['media_type'] == "IMAGE")
						{
							$instaMedia['filename'] = "intagram-".$user_name.'-'.$instaMedia['id'].".jpg";
							array_push($instaImages, $instaMedia);
						}
						else if( $instaMedia['media_type'] =="CAROUSEL_ALBUM")
						{
							foreach ($instaMedia['children']['data'] as $key => $instaChildrenMedia)
							{
								$instaChildrenMediaDetails = instaToPiwigo_GetCarouselMediaData($auth_config, $instaChildrenMedia['id'], $user_name);
								if(is_array($instaChildrenMediaDetails)){
									if($instaChildrenMediaDetails['media_type'] == "IMAGE")
									{
									// on applique le libellé du CAROUSEL
									$instaChildrenMediaDetails['caption']=$instaMedia['caption'];
									array_push($instaImages, $instaChildrenMediaDetails);
									}									
								}
							}					
						}
					}
					elseif(!is_null($auth_config['begin_sync_date']) && $instaMediaDate < $auth_config['begin_sync_date'] )
					{
						$dateLimitReached = true;
						break;
					}
				}					
			}			
			
		}
		else{
			$error_m = json_decode($instaGetMediaResponse, JSON_OBJECT_AS_ARRAY);
			if( $http_code == intval('400') && $error_m['error']['code']== '190')
			{
				unset($_SESSION['instagram_access_token']);
				$_SESSION['page_infos'][] = l10n('Session expired');
				redirect(INSTAG_ADMIN . '-import');
			}
			elseif( $http_code == intval('403') && $error_m['error']['code']== '4')
			{
				$_SESSION['page_infos'][] = l10n('Application request limit reached');
			}			
			else
			{
				echo "raw: ".$instaGetMediaResponse;							
				echo '<br>';
				echo "Ressource introuvable : " . $http_code;			
			}
		}
	} catch (\Throwable $th) {
		throw $th;
	} finally {
		curl_close($ch);
		
		if(isset($instaGetMediaResponseArray['paging']['next']) && !$dateLimitReached)
		{$next = $instaGetMediaResponseArray['paging']['cursors']['after'];}
	
		if(isset($instaGetMediaResponseArray['paging']['previous']))
		{$prev = $instaGetMediaResponseArray['paging']['cursors']['before'];}
		
		return array('pics' => $instaImages, 'before' =>$prev, 'after' => $next, 'limit' => $dateLimitReached );		
	}	
}

/* *** *** *************************************** *** *** */
/* Récupère le détail d'une image Insta depuis un carousel */
/* *** *** *************************************** *** *** */
function instaToPiwigo_GetCarouselMediaData($auth_config, $instaMediaId, $user_name)
{	
	$curlGetVar = array(
	  'fields'     => 'id,media_type,media_url,timestamp',
	  'access_token' => $_SESSION['instagram_access_token']
	  );
	
	
	$ch = curl_init();
	try {
		curl_setopt($ch, CURLOPT_URL, $auth_config['graphApiUrl'].$instaMediaId."?".http_build_query($curlGetVar));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //A supprimer - ne devrait pas etre nécessaire sur un serveur configuré correctement
	
		$instaGetMediaDetailResponse = curl_exec($ch);		
	
	    if (curl_errno($ch)) {
			echo curl_error($ch);
			die();
		}
		
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if($http_code == intval(200)){
			$instaGetMediaDetailResponseArray = json_decode($instaGetMediaDetailResponse, true);
			curl_close($ch);
			//on crée le nom du fichier
			$instaGetMediaDetailResponseArray['filename'] = "intagram-".$user_name.'-'.$instaGetMediaDetailResponseArray['id'].".jpg" ;					
			return $instaGetMediaDetailResponseArray;
			
		}
		else{
			$error_m = json_decode($instaGetMediaDetailResponse, JSON_OBJECT_AS_ARRAY);
			if( $http_code == intval('400') && $error_m['error']['code']== '190')
			{
				unset($_SESSION['instagram_access_token']);
				$_SESSION['page_infos'][] = l10n('Session expired');
				redirect(INSTAG_ADMIN . '-import');
			}
			elseif( $http_code == intval('403') && $error_m['error']['code']== '4')
			{
				$_SESSION['page_infos'][] = l10n('Application request limit reached');
			}	
			else
			{
			echo "raw: ".$instaGetMediaDetailResponse;
			echo '<br>';
			echo "Ressource introuvable : " . $http_code;		
			}

			curl_close($ch);
			return false;
		}
	} catch (\Throwable $th) {
		throw $th;
	} 	
}

?>