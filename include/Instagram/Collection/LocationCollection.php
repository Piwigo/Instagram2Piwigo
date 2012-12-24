<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Collection/CollectionAbstract.php');
include_once(INSTAGRAM_ROOT.'/Location.php');


/**
 * Comment Collection
 *
 * Holds a collection of comments
 */
class Instagram_Collection_LocationCollection extends Instagram_Collection_CollectionAbstract {

    /**
     * Set the collection data
     *
     * @param StdClass $raw_data
     * @access public
     */
    public function setData( $raw_data ) {
        $this->data = $raw_data->data;
        $this->convertData( 'Instagram_Location' );
    }

}
?>