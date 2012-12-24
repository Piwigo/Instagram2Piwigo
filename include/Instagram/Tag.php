<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Core/Proxy.php');
include_once(INSTAGRAM_ROOT.'/Core/BaseObjectAbstract.php');
include_once(INSTAGRAM_ROOT.'/Collection/TagMediaCollection.php');


/**
 * Tag class
 *
 * @see Instagram->getTag()
 * {@link https://github.com/galen/PHP-Instagram-API/blob/master/Examples/tag.php}
 * {@link http://galengrover.com/projects/PHP-Instagram-API/Examples/?example=tag.php}
 */
class Instagram_Tag extends Instagram_Core_BaseObjectAbstract {

    /**
     * Get tag media
     *
     * Retrieve the recent media posted with this tag
     *
     * This can be paginated with the next_max_id param obtained from Instagram_Collection_MediaCollection->getNext()
     *
     * @param array $params Optional params to pass to the endpoint
     * @return Instagram_Collection_MediaCollection
     * @access public
     */
    public function getMedia( array $params = null ) {
        return new Instagram_Collection_TagMediaCollection( $this->proxy->getTagMedia( $this->getApiId(), $params ), $this->proxy );
    }

    /**
     * Get media count
     *
     * @return int
     * @access public
     */
    public function getMediaCount() {
        return (int)$this->data->media_count;
    }

    /**
     * Get tag name
     *
     * @return string
     * @access public
     */
    public function getName() {
        return $this->data->name;
    }

    /**
     * Get ID
     *
     * The ID for a tag is it's name, so return the name
     *
     * @return string
     * @access public
     */
    public function getId() {
        return $this->getName();
    }

    /**
     * Magic toString method
     *
     * Return the tag name
     *
     * @return string
     * @access public
     */
    public function __toString() {
        return $this->getName();
    }

}
?>