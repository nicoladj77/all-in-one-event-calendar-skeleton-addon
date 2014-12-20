<?php

/**
 * This is an example controller which controls how to add additional meta boxes
 * to an event description and when to update events display.
 * It does extend `Ai1ec_Base` class which is a part of Core and provides access
 * to Core class loader.
 */
class Ai1ecsa_Skeleton_Controller extends Ai1ec_Base {

    /**
     * This is an internal constant defining the name of input field on event
     * edit page.
     */
    const SKELETON_META_CHECKBOX = 'ai1ecsa-skeleton-soldout';

	/**
	 * Checks if user has selected checkbox indicating that event is soldout.
     * If yes - call model to update the information.
	 */
	public function handle_save_event( Ai1ec_Event $event ) {
		$soldout = isset( $_POST[self::SKELETON_META_CHECKBOX] );
        /**
         * You may prefer to use model (read more about MVC pattern
         * [here](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller)
         * or [here](http://martinfowler.com/tags/model-view-controller.html)).
         * This model is [described in details here](../model/skeleton-soldout.md).
         */
		$this->_registry->get( 'model.skeleton-soldout' )
                        ->set_post_flag( $event, $soldout );
	}

	/**
	 * Generate HTML box to be rendered on event editing page.
	 */
	public function post_meta_box() {
		global $post;
        /**
         * This is a convenient method to check if All-in-One Event Calendar
         * event is being edited.
         */
		if ( ! $this->_registry->get( 'acl.aco' )->are_we_editing_our_post() ) {
			return null;
		}

		$checked = null;

		/**
         * Get Event by ID and check it's status using model (see above for
         * more details).
         */
		try {
			$event  = $this->_registry->get( 'model.event', $post->ID );
            $status = $this->_registry->get( 'model.skeleton-soldout' )
                                      ->get_post_flag( $event );
            if ( $status ) {
                $checked = 'checked="checked"';
            }
		} catch ( Ai1ec_Event_Not_Found_Exception $excpt ) {
            /**
             * This means that event is not yet saved.
             */
		}

		$args = array(
			'title'   => __( 'Event is Sold Out', AI1ECTI_PLUGIN_NAME ),
			'checked' => $checked,
            'id'      => self::SKELETON_META_CHECKBOX,
		);
        /**
         * This is a way to use Core templating system. It provides a few features,
         * security being amongst them, which make it more convenient than default
         * WordPress one.
         * --
         * You are ought to place templates in one of these folders in plugin
         * main directory:
         *     * `public/admin/twig/` - for templates used in admin side; you need
         *       to set third argument to `true` in that case, in the call bellow;
         *     * `public/themes-ai1ec/vortex/twig/` - for front-end views.
         * --
         * The `get_file` accepts file path starting at one of these directories.
         * In this case it is template `ai1ecsa-post-meta-box.twig` for admin side
         * view so the file will be `public/admin/twig/ai1ecsa-post-meta-box.twig`.
         * You may also use directories, i.e. create file
         * `public/admin/twig/skeleton/soldout/meta-box.twig` and then require it
         * by using `skeleton/soldout/meta-box.twig` value in the method bellow.
         * --
         * Second argument to the function `get_file` is a list of values to be
         * passed to template. It must be an array, where keys will become variable
         * names in the template. In this case `$args` contains key `title` with
         * value `'Event is Sold Out'` and `checked` with either `true` or `false`
         * as its value. In template `{{ title }}`, `{{ id }}` and `{{ checked }}`
         * variables will be made available.
         */
		$this->_registry->get( 'theme.loader' )->get_file(
			'ai1ecsa-post-meta-box.twig',
			$args,
			true
		)->render();
	}

}