<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Collection/MediaCollection.php');


/**
 * Liked Media Collection
 *
 * Holds a collection of liked media
 */
class Instagram_Collection_LikedMediaCollection extends Instagram_Collection_MediaCollection {

    /**
     * Get the next max like ID
     * 
     * @return string
     * @access public
     */
    public function getNextMaxLikeId() {
        return isset( $this->pagination->next_max_like_id ) ? $this->pagination->next_max_like_id : null;
    }

    /**
     * Get the next max like ID
     * 
     * @return string
     * @access public
     */
    public function getNext() {
        return $this->getNextMaxLikeId();
    }

}
?>