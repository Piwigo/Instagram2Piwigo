<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Core/Proxy.php');
include_once(INSTAGRAM_ROOT.'/Collection/MediaCollection.php');


/**
 * Location class
 *
 * Some media has a location associated to it. This location will have an ID and a name.
 * Some media has no location associated, but has a lat/lng. These location objects will return null or '' for certain method calls
 *
 * @see Instagram->getCurrentUser()
 * {@link https://github.com/galen/PHP-Instagram-API/blob/master/Examples/location.php}
 * {@link http://galengrover.com/projects/PHP-Instagram-API/Examples/?example=location.php}
 */
class Instagram_Location extends Instagram_Core_BaseObjectAbstract {

    /**
     * Get location media
     *
     * Retrieve the recent media posted to a given location
     *
     * This can be paginated with the next_max_id param obtained from Instagram_Collection_MediaCollection->getNext()
     *
     * @param array $params Optional params to pass to the endpoint
     * @return Instagram_Collection_MediaCollection
     * @access public
     */
    public function getMedia( array $params = null ) {
        return new Instagram_Collection_MediaCollection( $this->Instagram_Core_Proxy->getLocationMedia( $this->getApiId(), $params ), $this->Instagram_Core_Proxy );
    }

    /**
     * Get location ID
     *
     * @return string|null
     * @access public
     */
    public function getId() {
        return isset( $this->data->id ) ? $this->data->id : null;
    }

    /**
     * Get location name
     *
     * @return string|null
     * @access public
     */
    public function getName() {
        return isset( $this->data->name ) ? $this->data->name : null;
    }

    /**
     * Get location longitude
     *
     * Get the longitude of the location
     *
     * @return string|null
     * @access public
     */
    public function getLat() {
        return isset( $this->data->latitude ) && is_float( $this->data->latitude ) ? $this->data->latitude : null;
    }

    /**
     * Get location latitude
     *
     * Get the latitude of the location
     *
     * @return string|null
     * @access public
     */
    public function getLng() {
        return isset( $this->data->longitude ) && is_float( $this->data->longitude ) ? $this->data->longitude : null;
    }

    /**
     * Magic toString method
     *
     * Returns the location's name
     *
     * @return string
     * @access public
     */
    public function __toString() {
        return $this->getName() ? $this->getName() : '';
    }

}
?>