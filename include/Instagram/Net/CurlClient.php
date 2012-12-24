<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Core/ApiException.php');
include_once(INSTAGRAM_ROOT.'/Net/ClientInterface.php');


/**
 * Curl Client
 *
 * Uses curl to access the API
 */
class Instagram_Net_CurlClient implements Instagram_Net_ClientInterface {

    /**
     * Curl Resource
     *
     * @var curl resource
     */
    protected $curl = null;

    /**
     * Constructor
     *
     * Initializes the curl object
     */
    function __construct(){
        $this->initializeCurl();
    }

    /**
     * GET
     *
     * @param string $url URL to send get request to
     * @param array $data GET data
     * @return Instagram_Net_Response
     * @access public
     */
    public function get( $url, array $data = null ){
        curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $this->curl, CURLOPT_URL, sprintf( "%s?%s", $url, http_build_query( $data ) ) );
        return $this->fetch();
    }

    /**
     * POST
     *
     * @param string $url URL to send post request to
     * @param array $data POST data
     * @return Instagram_Net_Response
     * @access public
     */
    public function post( $url, array $data = null ) {
        curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'POST' );
        curl_setopt( $this->curl, CURLOPT_URL, $url );
        curl_setopt( $this->curl, CURLOPT_POSTFIELDS, http_build_query( $data ) );
        return $this->fetch();
    }

    /**
     * PUT
     *
     * @param string $url URL to send put request to
     * @param array $data PUT data
     * @return Instagram_Net_Response
     * @access public
     */
    public function put( $url, array $data = null  ){
        curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'PUT' );
    }

    /**
     * DELETE
     *
     * @param string $url URL to send delete request to
     * @param array $data DELETE data
     * @return Instagram_Net_Response
     * @access public
     */
    public function delete( $url, array $data = null  ){
        curl_setopt( $this->curl, CURLOPT_URL, sprintf( "%s?%s", $url, http_build_query( $data ) ) );
        curl_setopt( $this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE' );
        return $this->fetch();
    }

    /**
     * Initialize curl
     *
     * Sets initial parameters on the curl object
     *
     * @access protected
     */
    protected function initializeCurl() {
        $this->curl = curl_init();
        curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $this->curl, CURLOPT_SSL_VERIFYPEER, false );
    }

    /**
     * Fetch
     *
     * Execute the curl object
     *
     * @return StdClass
     * @access protected
     * @throws Instagram_Core_ApiException
     */
    protected function fetch() {
        $raw_response = curl_exec( $this->curl );
        $error = curl_error( $this->curl );
        if ( $error ) {
            throw new Instagram_Core_ApiException( $error, 666, 'CurlError' );
        }
        return $raw_response;
    }
    
}
?>