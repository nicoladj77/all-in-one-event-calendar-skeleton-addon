<?php

/**
 * A model class allows you to interact with the database. It provides
 * some convenience when managing your code.
 */
class Ai1ecsa_SkeletonSoldout extends Ai1ec_Base {


	// Meta key indicating user choice.
	const SKELETON_POST_FLAG = '_ai1ecsa_is_sold_out';


	/**
     * =========================================================================
	 *                         CHECK EVENT STATUS
     * =========================================================================
	 *
     * Gets status whereas event is sold out.
     *
	 * @return bool True if event is "Sold Out", false otherwise.
	 */
	public function get_post_flag( Ai1ec_Event $event ) {
        /**
         * Bellow `model.meta-post` class, provided by Core, is being used.
         * It is a class which allows easier, faster interaction with WP post
         * meta wrapping methods like
         * [`get_post_meta`](http://codex.wordpress.org/Function_Reference/get_post_meta)
         * and [`update_post_meta`](http://codex.wordpress.org/Function_Reference/update_post_meta)
         * using simplier interfaces and names like `get` as is seen bellow
         * and `update` (see next method).
         */
		$flag      = $this->_registry->get( 'model.meta-post' )->get(
			$event->get( 'post_id' ),
			self::SKELETON_POST_FLAG,
			false
		);
		return (bool)$flag;
	}

	/**
     * =========================================================================
	 *                         CHANGE EVENT STATUS
     * =========================================================================
     *
	 * Sets Sold Out status for an event.
	 *
	 * @return bool Success.
	 */
	public function set_post_flag( Ai1ec_Event $event, $status ) {
		return $this->_registry->get( 'model.meta-post' )->update(
			$event->get( 'post_id' ),
			self::SKELETON_POST_FLAG,
			(bool)$status
		);
	}

}