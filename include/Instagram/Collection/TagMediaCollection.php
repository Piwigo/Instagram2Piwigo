<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Collection/MediaCollection.php');


/**
 * Tag Media Collection
 *
 * Holds a collection of media associated with a tag
 */
class Instagram_Collection_TagMediaCollection extends Instagram_Collection_MediaCollection {

    /**
     * Get next max tag id
     *
     * Get the next max tag id for use in pagination
     *
     * @return string Returns the next max tag id
     * @access public
     */
    public function getNextMaxTagId() {
        return isset( $this->pagination->next_max_tag_id ) ? $this->pagination->next_max_tag_id : null;
    }

    /**
     * Get next max tag id
     *
     * Get the next max tag id for use in pagination
     *
     * @return string Returns the next max tag id
     * @access public
     */
    public function getNext() {
        return $this->getNextMaxTagId();
    }

}
?>