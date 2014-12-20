<?php

/**
 * This class conditionally adds user selected message to the
 * begining of event title.
 */
class Ai1ecsa_View_Skeleton_Message extends Ai1ec_Base {

    /**
     * This is a user selected message.
     */
    protected $_message = '';

    public function __construct( Ai1ec_Registry_Object $registry ) {
        parent::__construct( $registry );
        $this->_message = $registry->get( 'model.settings' )
                                   ->get( 'skeleton_sold_out_message' );
    }

    /**
     * This method is filtering result in response to
     * [`the_title`](http://codex.wordpress.org/Plugin_API/Filter_Reference/the_title)
     * WordPress filter.
     * Based on `$post_id` passed it attempts to find corresponding event
     * and check if user has indicated it as sold-out. If that is the case
     * - adds the prefix of user choice.
     */
    public function filter_title( $title, $post_id ) {
        try {
            $event  = $this->_registry->get( 'model.event', $post_id );
            $status = $this->_registry->get( 'model.skeleton-soldout' )
                                      ->get_post_flag( $event );
            if ( $status ) {
                $title = sprintf(
                    '[%s] %s',
                    $this->_message,
                    $title
                );
            }
        } catch ( Ai1ec_Event_Not_Found_Exception $excpt ) {
        }
        return $title;
    }

}