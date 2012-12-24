<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Core/Proxy.php');
include_once(INSTAGRAM_ROOT.'/Core/BaseObjectAbstract.php');
include_once(INSTAGRAM_ROOT.'/Collection/MediaCollection.php');
include_once(INSTAGRAM_ROOT.'/Collection/UserCollection.php');


/**
 * User class
 *
 * @see Instagram->getUser()
 * {@link https://github.com/galen/PHP-Instagram-API/blob/master/Examples/user.php}
 * {@link http://galengrover.com/projects/PHP-Instagram-API/Examples/?example=user.php}
 */
class Instagram_User extends Instagram_Core_BaseObjectAbstract {

    /**
     * Get the user's username
     *
     * @return string
     * @access public
     */
    public function getUserName() {
        return $this->data->username;
    }

    /**
     * Get the user's full name
     *
     * @return string|null
     * @access public
     */
    public function getFullName( $forced = false ) {
        return isset( $this->data->full_name ) ? $this->data->full_name : ( $forced ? $this->data->username : null );
    }

    /**
     * Get the user's profile picture
     *
     * @return string
     * @access public
     */
    public function getProfilePicture() {
        return $this->data->profile_picture;
    }

    /**
     * Get the user's biography
     *
     * @return string
     * @access public
     */
    public function getBio() {
        return $this->data->bio;
    }

    /**
     * Get the user's website
     *
     * @return string
     * @access public
     */
    public function getWebsite() {
        return $this->data->website;
    }

    /**
     * Get the user's counts
     *
     * @return StdClass|null
     * @access public
     */
    public function getCounts() {
        if ( !$this->isCompleteUser() ) {
            $this->updateData();
        }
        return isset( $this->data->counts ) ? $this->data->counts : null;
    }

    /**
     * Get the user's following count
     *
     * @return int
     * @access public
     */
    public function getFollowsCount() {
        return (int)$this->getCounts()->follows;
    }

    /**
     * Get the user's followers count
     *
     * @return int
     * @access public
     */
    public function getFollowersCount() {
        return (int)$this->getCounts()->followed_by;
    }

    /**
     * Get the user's media count
     *
     * @return int
     * @access public
     */
    public function getMediaCount() {
        return (int)$this->getCounts()->media;
    }

    /**
     * Update user data
     *
     * Sometimes user object are incomplete. For instance when getting a media object's comments
     * the users associated with the comments won't have all their data
     *
     * @access public
     */
    public function updateData() {
        $this->setData( $this->proxy->getUser( $this->getApiId() ) );
    }

    /**
     * Return if the user is complete
     *
     * @see Instagram_User::updateData()
     *
     * @return bool
     * @access protected
     */
    protected function isCompleteUser() {
        return isset( $this->data->counts );
    }

    /**
     * Get the user's media
     *
     * This can be paginated with the next_max_id param obtained from Instagram_Collection_MediaCollection->getNext()
     *
     * @return Instagram_Collection_MediaCollection
     * @access public
     */
    public function getMedia( array $params = null ) {
        return new Instagram_Collection_MediaCollection( $this->proxy->getUserMedia( $this->getApiId(), $params ), $this->proxy );
    }

    /**
     * Get the users that the user follows
     *
     * This can be paginated with the next_cursor param obtained from Instagram_Collection_UserCollection->getNext()
     *
     * @return Instagram_Collection_UserCollection
     * @access public
     */
    public function getFollows( array $params = null ) {
        return new Instagram_Collection_UserCollection( $this->proxy->getUserFollows( $this->getApiId(), $params ), $this->proxy );
    }

    /**
     * Get the user's that follow this user
     *
     * This can be paginated with the next_cursor param obtained from Instagram_Collection_UserCollection->getNext()
     *
     * @return Instagram_Collection_UserCollection
     * @access public
     */
    public function getFollowers( array $params = null ) {
        return new Instagram_Collection_UserCollection( $this->proxy->getUserFollowers( $this->getApiId(), $params ), $this->proxy );
    }

    /**
     * Magic toString method
     * 
     * Get the user's username
     *
     * @return Instagram_Collection_Collection
     * @access public
     */
    public function __toString() {
        return $this->getUserName();
    }

}
?>