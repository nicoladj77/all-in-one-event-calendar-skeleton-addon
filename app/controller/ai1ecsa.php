<?php

/**
 * Skeleton add-on: example front controller.
 *
 * This is a base class, which extends upon features provided in the Core.
 * It might be possible to avoid using one, but it's just easier to 
 *
 * The base class here is `Ai1ec_Base_License_Controller` but you may
 * choose to use `Ai1ec_Base_Extension_Controller` which is a bit simplier
 * and mostly useful if used outside of Timely bundles.
 */
class Ai1ec_Controller_Ai1ecsa extends Ai1ec_Base_License_Controller {

	/**
	 * Indicate the minimum version of Core that you are willing to use.
     * There are new features, especially under the hood, added with each
     * release of the Core, so it is better to check what was the first version
     * to introduce the feature you are using and indicate that as your minimum
     * required version. Otherwise users will experience errors and see your
     * add-on being disabled.
	 */
	public function minimum_core_required() {
		return '2.1.9';
	}

	/**
	 * Provide a user-readable name of your plugin here. It will be used
     * throughout a UI to identify parts specific to your add-on.
     */
	public function get_name() {
		return 'Skeleton';
	}

	/**
	 * Provide a machine-readable name of your plugin. It should contain letters
     * and may contain numbers as well as underscore (`_`) characters. It is
     * used in JavaScript and PHP to reference code blocks specific to your
     * add-on.
     */
	public function get_machine_name() {
		return 'skeleton';
	}

	/**
	 * This method is explicitly provided by Licence extension class. It is
     * used in combination with Timely licensing server. For more details on
     * selling add-ons though Timely Marketplace please get in touch with us.
     */
	public function get_license_label() {
		return 'Skeleton License Key';
	}

	/**
	 * Return your current version. It will be used to inform the user if some
     * generic information needs to be communicated. Usually it's enough to use
     * the constant which you defined in the base plugin file.
     */
	public function get_version() {
		return AI1ECTI_VERSION;
	}

	/**
	 * Please use the constant from base plugin file here. It is used internally
     * to diagnose (detect) add-on related actions.
     */
	public function get_file() {
		return AI1ECTI_FILE;
	}

	/**
     * =========================================================================
     *                          DEFINING SETTINGS
     * =========================================================================
	 * If you are going to provide some settings for the user to choose from you
     * shall keep them in a dedicated tab in the settings. That way it will be
     * easier to explain choices to the user and keep the UI tidy.
     * Please replace tab identifier (`'skeleton'`) and title (`'Skeleton'`) in
     * the code bellow to the values of your choice.
     */
	public function add_tabs( array $tabs ) {
		$tabs = parent::add_tabs( $tabs );
		$tabs['extensions']['items']['skeleton'] = __(
			'Skeleton',
			AI1ECSA_PLUGIN_NAME
		);
		return $tabs;
	}

	/**
	 * Register custom settings used by the add-on. This allows you to define
     * the settings that you need and do not worry about implementing the UI
     * to choose them. Code bellow displays two types of settings you are likely
     * to use.
	 */
	protected function _get_settings() {
		return array(
            /**
             * This is a name of the setting. Please note that it must start with
             * a unique prefix - for example your plugin name. It must contain
             * only letters, underscores (`_`) and numbers and start with a letter.
             */
			'skeleton_sold_out_message' => array(
                /**
                 * Type indicates how the setting will be treated after user
                 * selects a value. Common types are `'string'` for any text,
                 * `'html'` for rich text input, `'array'` for a list of
                 * options and `'bool`' for any yes/no choice (checkbox).
                 */
				'type' => 'string',
                /**
                 * Renderer element describes how element will be present
                 * to user for selecting a value.
                 */
				'renderer' => array(
                    /**
                     * Class corresponds to some of the types that Core had
                     * implemented. Currently you may choose from `'input'`
                     * (a simple HTML input), a `'checkbox'` for yes/no values,
                     * a `'select`' for one or many selections from a list
                     * and a `'textarea'` for a multiline text (or HTML) input.
                     */
					'class' => 'input',
                    /**
                     * Tab indicates a primary location on settings page. You
                     * are likely to always choose `'extensions'` here.
                     */
					'tab'   => 'extensions',
                    /**
                     * Tab indicates a particular location within a tab on
                     * settings page. Again - it's likely you will always use
                     * your plugin name here, like `'skeleton'`.
                     */
					'item'  => 'skeleton',
                    /**
                     * Label is a text visible to user next to a form element
                     * in a few words explaining the meaning of a choice.
                     */
					'label' => __( 'Message to add to sold out events:', AI1ECSA_PLUGIN_NAME ),
                    /**
                     * Help is an additional text which user will see beneath
                     * your option. It is optional, but if provided it is intended
                     * to explain the purpose of the option.
                     */
					'help'  => __( 'Remember to make changes to an event once it is sold out.', AI1ECSA_PLUGIN_NAME ),
                    /**
                     * This is `'input'` class specific option. You may choose from
                     * `'normal'` - just text, `'date'` - date selections, `'email'`
                     * for email input and `'append'` if you wish to display this as
                     * a HTML snippet explaining options instead of a real option.
                     */
					'type'  => 'normal',
				),
                /**
                 * This is a default value that user will see before he makes his
                 * choice.
                 */
				'value'  => 'Sold out!',
			),
			'skeleton_message_disabled' => array(
				'type'     => 'bool',
				'renderer' => array(
					'class'  => 'checkbox',
					'tab'    => 'extensions',
					'item'   => 'skeleton',
					'label'  => __(
						'Disable messages everywhere?',
                        AI1ECSA_PLUGIN_NAME
					),
					'help'  => __(
						'Please use this checkbox if you want to disable all "sold out" messages',
                        AI1ECSA_PLUGIN_NAME
					),
				),
				'value'  => false,
			),
		);
	}

	/**
	 * Action performed during activation. Please keep all actions that you want
     * to run only once, during your add-on activation, in this method. Then the
     * Core will make sure it is executed correctly.
	 */
	public function on_activation( Ai1ec_Registry $ai1ec_registry ) {
	}

	/**
	 * Register actions handlers. This is a method provided by the Core which
     * allows to hook to actions or filters in a sensible manner. Here sensible
     * means that Core will take care to optimise memory usage for you as much
     * as possible.
	 */
	protected function _register_actions( Ai1ec_Event_Dispatcher $dispatcher ) {

        /**
         * =====================================================================
         *                          USING DISPATCHER
         * =====================================================================
         * The $dispatcher (class name `Ai1ec_Event_Dispatcher`) is provided by
         * the Core to make it easier to bind to actions.
         * It has methods `register_action` which corresponds to WordPress native
         * [`add_action`](http://codex.wordpress.org/Function_Reference/add_action)
         * and `register_filter` (corresponds to 
         * [`add_filter`](http://codex.wordpress.org/Function_Reference/add_filter)).
         * The difference lies in the second argument. As you see bellow - it's an
         * array with two elements. First being path name (remember *Class Loading*
         * section from the introduction) to your class and the second - method name
         * in that class. So, the example bellow
         * `array( 'controller.skeleton', 'post_meta_box' )` means that when this
         * action (`post_submitbox_misc_actions`) will work the Core will find your
         * controller (here it will be a file in `./app/controller/skeleton.php`),
         * initiate it and run the method (again, in this case it will be method
         * `Ai1ecsa_Skeleton_Controller::post_meta_box`).
         * More information about dedicated controllers is found in [Skeleton
         * controller description](skeleton.md).
         */
		$dispatcher->register_action( 'post_submitbox_misc_actions', array( 'controller.skeleton', 'post_meta_box' ) );
		$dispatcher->register_action(
			'ai1ec_save_post',
			array( 'controller.skeleton', 'handle_save_event' )
		);
        /**
         * Here is shown the interaction with settings model. Remember the
         * [Defining settings](#defining-settings) section above where the
         * registration takes place. Here we call the settings model. Then
         * the model is asked to retrieve value for particular setting. If
         * the value is not defined - the default will be `false`.
         */
        $disabled = $this->_registry->get( 'model.settings' )
                                    ->get( 'skeleton_message_disabled', false );
        if ( ! $disabled ) {
            /**
             * See how message is added to event description in
             * [view file](/app/view/skeleton/message.md).
             * Please note the line above - we are checking if user haven't
             * disabled the messages entirely before adding filter in order
             * to increaase performance.
             */
            $dispatcher->register_filter( 'the_title', array( 'view.skeleton.message', 'filter_title' ), 10, 2 );
        }
	}

}