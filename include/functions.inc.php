<?php
defined('INSTAG_PATH') or die('Hacking attempt!');

/**
 * download a remote file
 *  - needs cURL or allow_url_fopen
 *  - take care of SSL urls
 *
 * @param: string source url
 * @param: mixed destination file (if true, file content is returned)
 */
if (!function_exists('download_remote_file'))
{
  function download_remote_file($src, $dest)
  {
    if (empty($src))
    {
      return false;
    }
    
    $return = ($dest === true) ? true : false;
    
    /* curl */
    if (function_exists('curl_init'))
    {
      if (!$return)
      {
        $newf = fopen($dest, "wb");
      }
      $ch = curl_init();
      
      curl_setopt($ch, CURLOPT_URL, $src);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //A supprimer - ne devrait pas etre nécessaire sur un serveur configuré correctement
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept-language: en"));
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)');
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      if (!ini_get('safe_mode'))
      {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
      }
      if (strpos($src, 'https://') !== false)
      {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      }
      if (!$return)
      {
        curl_setopt($ch, CURLOPT_FILE, $newf);
      }
      else
      {
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      }
      
      $out = curl_exec($ch);
      curl_close($ch);
      
      if ($out === false)
      {
        return 'file_error';
      }
      else if (!$return)
      {
        fclose($newf);
        return true;
      }
      else
      {
        return $out;
      }
    }
    /* file get content */
    else if (ini_get('allow_url_fopen'))
    {
      if (strpos($src, 'https://') !== false and !extension_loaded('openssl'))
      {
        return false;
      }
      
      $opts = array(
        'http' => array(
          'method' => "GET",
          'user_agent' => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)',
          'header' => "Accept-language: en",
        )
      );

      $context = stream_context_create($opts);
      
      if (($file = file_get_contents($src, false, $context)) === false)
      {
        return 'file_error';
      }
      
      if (!$return)
      {
        file_put_contents($dest, $file);
        return true;
      }
      else
      {
        return $file;
      }
    }
    
    return false;
  }
}



/**
 * Remove emoji characters in the param string
 *
 * @param: string 
 */
if (!function_exists('remove_emoji'))
{
	function remove_emoji($string)
	{
		// Match Enclosed Alphanumeric Supplement
		$regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
		$clear_string = preg_replace($regex_alphanumeric, '', $string);

		// Match Miscellaneous Symbols and Pictographs
		$regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
		$clear_string = preg_replace($regex_symbols, '', $clear_string);

		// Match Emoticons
		$regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
		$clear_string = preg_replace($regex_emoticons, '', $clear_string);

		// Match Transport And Map Symbols
		$regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
		$clear_string = preg_replace($regex_transport, '', $clear_string);
		
		// Match Supplemental Symbols and Pictographs
		$regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
		$clear_string = preg_replace($regex_supplemental, '', $clear_string);

		// Match Miscellaneous Symbols
		$regex_misc = '/[\x{2600}-\x{26FF}]/u';
		$clear_string = preg_replace($regex_misc, '', $clear_string);

		// Match Dingbats
		$regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
		$clear_string = preg_replace($regex_dingbats, '', $clear_string);

		return $clear_string;
	}
}