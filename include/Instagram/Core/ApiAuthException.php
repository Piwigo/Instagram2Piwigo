<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Core/ApiException.php');


/**
 * API Auth Exception
 *
 * This exception type will be thrown if the access token you are using is no longer valid.
 *
 * {@link https://github.com/galen/PHP-Instagram-API/blob/master/Examples/index.php#L39}
 */
class Instagram_Core_ApiAuthException extends Instagram_Core_ApiException {}

?>