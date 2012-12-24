<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/



/**
 * Client Interface
 *
 * All clients must implement this interface
 *
 * The 4 http functions just need to return the raw data from the API
 */
interface Instagram_Net_ClientInterface {

    function get( $url, array $data = null );
    function post( $url, array $data = null );
    function put( $url, array $data = null );
    function delete( $url, array $data = null );

}
?>