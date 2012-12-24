<?php

/**
* Instagram PHP
* @author Galen Grover <galenjr@gmail.com>
* @license http://opensource.org/licenses/mit-license.php The MIT License
*/


include_once(INSTAGRAM_ROOT.'/Core/BaseObjectAbstract.php');
include_once(INSTAGRAM_ROOT.'/User.php');


/**
 * Comment class
 *
 * @see Instagram_CurrentUser::addMediaComment()
 * @see Instagram_CurrentUser::deleteMediaComment()
 * @see Instagram_Media::getCaption()
 * @see Instagram_Media::getComments()
 */
class Instagram_Comment extends Instagram_Core_BaseObjectAbstract {

    /**
     * Cached user
     * 
     * @var Instagram_User
     */
    protected $user = null;

    /**
     * Get comment creation time
     *
     * @param $format Time format {@link http://php.net/manual/en/function.date.php}
     * @return string Returns the creation time with optional formatting
     * @access public
     */
    public function getCreatedTime( $format = null ) {
        if ( $format ) {
            $date = date( $format, $this->data->created_time );
        }
        else {
            $date = $this->data->created_time;
        }
        return $date;
    }

    /**
     * Get the comment text
     *
     * @access public
     * @return string
     */
    public function getText() {
        return $this->data->text;
    }

    /**
     * Get the comment's user
     *
     * @access public
     * @return Instagram_User
     */
    public function getUser() {
        if ( !$this->user ) {
            $this->user = new Instagram_User( $this->data->from, $this->proxy );
        }
        return $this->user;
    }

    /**
     * Magic toString method
     *
     * Return the comment text
     *
     * @access public
     * @return string
     */
    public function __toString() {
        return $this->getText();
    }

}
?>