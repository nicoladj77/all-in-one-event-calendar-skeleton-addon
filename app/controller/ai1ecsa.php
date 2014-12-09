<?php

/**
 * Skeleton add-on: example front controller.
 *
 * @author     Time.ly Network Inc.
 * @since      1.0
 *
 * @package    AI1ECSA
 * @subpackage AI1ECSA.Controller
 */
class Ai1ec_Controller_Ai1ecsa extends Ai1ec_Base_Extension_Controller {

	/**
	 * @see Ai1ec_Base_Extension_Controller::minimum_core_required()
	 */
	public function minimum_core_required() {
		return '2.1.8';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Base_Extension_Controller::get_name()
	*/
	public function get_name() {
		return 'Skeleton';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Base_Extension_Controller::get_machine_name()
	*/
	public function get_machine_name() {
		return 'twitter_integration';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Base_Extension_Controller::get_version()
	*/
	public function get_version() {
		return AI1ECTI_VERSION;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Licence_Controller::get_file()
	*/
	public function get_file() {
		return AI1ECTI_FILE;
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Base_License_Controller::get_license_label()
	*/
	public function get_license_label() {
		return 'Twitter License Key';
	}

	/* (non-PHPdoc)
	 * @see Ai1ec_Base_License_Controller::add_tabs()
	*/
	public function add_tabs( array $tabs ) {
		$tabs = parent::add_tabs( $tabs );
		$tabs['extensions']['items']['twitter'] = __(
			'Twitter',
			AI1ECTI_PLUGIN_NAME
		);
		return $tabs;
	}

	/**
	 * Initializes the extension.
	 *
	 * @param Ai1ec_Registry_Object $registry
	 */
	public function init( Ai1ec_Registry_Object $registry ) {
		parent::init( $registry );
		$this->_request  = $registry->get( 'http.request.parser' );
		$this->_settings = $registry->get( 'model.settings' );
		$this->_register_commands();
		$this->_register_cron();
	}

	/**
	 * Generate HTML box to be rendered on event editing page
	 *
	 * @return void Method does not return
	 */
	public function post_meta_box() {
		global $post;
		if ( ! $this->_registry->get( 'acl.aco' )->are_we_editing_our_post() ) {
			return NULL;
		}

		// Get Event by ID
		try {
			$event = $this->_registry->get( 'model.event', $post->ID );
		} catch (Ai1ec_Event_Not_Found_Exception $e) {
			$event = null;
		}

		$status        = false;
		$checked       = null;
		$twitter_setup = $this->_registry->get( 'oauth.oauth-provider-twitter' )
			->is_configured();
		if ( ! $twitter_setup ) {
			return;
		}

		if (
			$twitter_setup &&
			! $this->_get_token()
		) {
			$this->_registry->get( 'notification.twitter' )
				->send_token_notification();
			return;
		}

		if ( $event !== null ) {
			$status    = $this->_registry->get( 'model.twitter.property' )
				->get_post_flag( $event );
		}
		if ( true === $status ) {
			$checked   = 'checked="checked"';
		}
		$args = array(
			'title'    => __( 'Post event to Twitter', AI1ECTI_PLUGIN_NAME ),
			'checked'  => $checked,
		);
		$this->_registry->get( 'theme.loader' )->get_file(
			'ai1ecti-post-meta-box.twig',
			$args,
			true
		)->render();
	}

	/**
	 * Cron callback processing (retrieving and sending) pending messages
	 *
	 * @return int Number of messages posted to Twitter
	 */
	public function send_twitter_messages() {
		// instance of oauth twitter adapter
		$provider     = $this->_registry->get( 'oauth.oauth-provider-twitter' );
		$token        = $this->_get_token();
		$notification = $this->_registry->get( 'notification.twitter' );
		$pending      = $this->_get_pending_twitter_events();
		$successful   = 0;

		if ( 0 === count( $pending ) ) {
			return false;
		}

		if ( ! $provider->is_configured() ) {
			return 0;
		}
		if ( ! $token ) {
			$notification->send_token_notification();
			return 0;
		}


		foreach ( $pending as $event ) {
			try {
				if (
					$this->_send_twitter_message( $event, $provider, $token )
				) {
					++$successful;
				}
			} catch ( Exception $e ) {
				$notification->add_notification( $e->getMessage() );
			}
		}
		$notification->store_all();
		return $successful;
	}

	public function on_deactivation() {
		$this->_registry->get( 'scheduling.utility' )
			->delete( self::CRON_HOOK_NAME );
		parent::on_deactivation();
	}

	/**
	 * Action performed during activation.
	 *
	 * @param Ai1ec_Registry $ai1ec_registry Registry object.
	 *
	 * @return void Method does not return.
	 */
	public function on_activation( Ai1ec_Registry $ai1ec_registry ) {
		$ai1ec_registry->get( 'scheduling.utility' )->schedule(
			self::CRON_HOOK_NAME,
			'hourly'
		);
	}

	/**
	 * Handles event save for Twitter purposes.
	 *
	 * @param Ai1ec_Event $event Event object.
	 *
	 * @return void Method does not return.
	 */
	public function handle_save_event( Ai1ec_Event $event ) {
		$post_to_twitter = isset( $_POST[self::TWITTER_FIELD] );
		$twitter_props   = $this->_registry->get( 'model.twitter.property' );

		$twitter_props->set_post_flag( $event, $post_to_twitter );
		$status = $twitter_props->get_status( $event );

		if (
			! $status ||
			'pending' === $status
		) {
			if ( $post_to_twitter ) {
				$twitter_props->set_status( $event, 'pending' );
			} else {
				$twitter_props->delete_pending( $event );
			}
		}
	}

	/**
	 * Retrieves a list of events matching Twitter notification time interval
	 *
	 * @return array List of Ai1ec_Event objects
	 */
	protected function _get_pending_twitter_events() {
		$parser   = $this->_registry->get( 'parser.frequency' );
		$search   = $this->_registry->get( 'model.search' );

		// Parse time interval
		$parser->parse( $this->_settings->get( 'twitter_notice_interval' ) );

		$interval = (int) $parser->to_seconds();
		$start    = $this->_registry->get( 'date.time', time() + $interval - 600 );
		$end      = $this->_registry->get( 'date.time', time() + $interval + 6600 );
		$events   = $search->get_events_between( $start, $end );

		return $events;
	}

	/**
	 * Checks and sends message to Twitter.
	 *
	 * Upon successfully sending message - updates meta to reflect status change.
	 *
	 * @param Ai1ec_Event                    $event    Event object.
	 * @param Ai1ecti_Oauth_Provider_Twitter $provider Twitter Oauth provider.
	 * @param array                          $token    Auth token.
	 *
	 * @return bool Success.
	 *
	 * @throws Ai1ecti_Oauth_Exception In case of some error.
	 */
	protected function _send_twitter_message( $event, $provider, $token ) {
		$twitter_prop = $this->_registry->get( 'model.twitter.property' );
		$status       = $twitter_prop->get_status( $event );
		$submit_flag  = $twitter_prop->get_post_flag( $event );

		if ( is_array( $status ) ) {
			$status = (string)current( $status );
		}
		if (
			'pending' !== $status ||
			! $submit_flag
		) {
			return false;
		}
		$tokens   = array(
			'title'    => $event->get( 'post' )->post_title,
			'date'     => $this->_registry->get( 'view.event.time' )->get_short_date(
				$event->get( 'start' )
			),
			'venue'    => $event->get( 'venue' ),
			'link'     => add_query_arg(
				'instance_id',
				$event->get( 'instance_id' ),
				get_permalink( $event->get( 'post' ) )
			),
			'hashtags' => $this->_get_hashtags( $event ),
		);
		$message = $this->_registry->get( 'twitter.formatter', $tokens )->format();

		$response = $provider->send_message(
			$token,
			$message,
			array(
				'longitude' => $event->get( 'longitude' ),
				'latitude'  => $event->get( 'latitude' ),
				'has_geo'   => $event->has_geoinformation(),
			)
		);

		if ( $response->is_error() ) {
			$this->_registry->get( 'notification.twitter' )
				->add_submission_notification( $event, $response );
			$state        = 'failed';
			$message_text = $response->get_string_error();
		} else {
			$state        = 'sent';
			$oembed       = $provider->get_oembed(
				$token,
				$response->get( 'id_str' )
			);
			$message_text = null;
			if ( ! $oembed->is_error() ) {
				$message_text = $oembed->get( 'html' );
			}
			$this->_registry->get( 'notification.twitter' )
				->add_submission_message( $event, $oembed );
		}
		$twitter_prop->set_status(
			$event,
			array(
				'success'           => ! $response->is_error(),
				'status'            => $state,
				'twitter_status_id' => $response->get( 'id_str' ),
				'message'           => $message_text,
			)
		);
		$twitter_prop->touch( $event );
		return ( ! $response->is_error() );
	}

	/**
	 * Extract hashtags based on event taxonomy.
	 *
	 * @param Ai1ec_Event $event Instance of event object.
	 *
	 * @return array List of unique hash-tags to use (with '#' symbol).
	 */
	protected function _get_hashtags( Ai1ec_Event $event ) {
		$terms    = array_merge(
			wp_get_post_terms(
				$event->get( 'post_id' ),
				'events_categories'
			),
			wp_get_post_terms(
				$event->get( 'post_id' ),
				'events_tags'
			)
		);
		$hashtags = array();
		foreach ( $terms as $term ) {
			$hashtags[] = '#' . implode( '_', explode( ' ', $term->name ) );
		}
		return array_unique( $hashtags );
	}

	/**
	 * Gets OAuth token.
	 *
	 * @return string OAuth token.
	 *
	 * @throws Ai1ecti_Oauth_Exception
	 */
	protected function _get_token() {
		$option = $this->_registry->get( 'model.option' );
		$token  = $option->get( 'ai1ec_oauth_tokens' );
		if ( ! isset( $token ) ) {
			return false;
		}
		return $token;
	}

	/**
	 * Register custom settings used by the extension to ai1ec general settings
	 * framework
	 *
	 * @return void
	 */
	protected function _get_settings() {
		$twitter_authorize_url = site_url( '?ai1ec_oauth=twitter' );

		return array(
			'oauth_twitter_id' => array(
				'type' => 'string',
				'renderer' => array(
					'class' => 'input',
					'tab'   => 'extensions',
					'item'  => 'twitter',
					'label' => __( 'Application Consumer Key:', AI1ECTI_PLUGIN_NAME ),
					'type'  => 'normal',
				),
				'value'  => '',
			),
			'oauth_twitter_pass' => array(
				'type' => 'string',
				'renderer' => array(
					'class' => 'oauth_secret',
					'tab'   => 'extensions',
					'item'  => 'twitter',
					'label' => __( 'Application Consumer Secret:', AI1ECTI_PLUGIN_NAME ),
					'type'  => 'normal',
					'help'  => sprintf( __(
							'Use "<em>%s</em>" URL for <strong>Callback URL</strong> when configuring your <a href="https://dev.twitter.com/apps/new">Twitter application</a>. After creating the application, change the permissions required to <strong>Read and Write</strong> on the <strong>Permissions</strong> tab in Twitter.',
							AI1ECTI_PLUGIN_NAME
						),
						$twitter_authorize_url
					),
					'oauth_url' => $twitter_authorize_url,
				),
				'value'  => '',
			),
			'twitter_notice_interval' => array(
				'type' => 'string',
				'renderer' => array(
					'class' => 'input-small',
					'tab'   => 'extensions',
					'item'  => 'twitter',
					'label' => __( 'Time to notification before event start:', AI1ECTI_PLUGIN_NAME ),
					'type'  => 'normal',
					'help'  => __(
						'Announcements will be posted to Twitter this long before start of event. Enter time in seconds (default behavior), or use suffixes, for example: <strong>3h</strong> = <em>3 hours</em>; <strong>1d</strong> = <em>1 day</em>.',
						AI1ECTI_PLUGIN_NAME
					),
				),
				'value'  => '',
			),
		);
	}

	/**
	 * Register actions handlers
	 *
	 * @return void
	 */
	protected function _register_actions( Ai1ec_Event_Dispatcher $dispatcher ) {
		$dispatcher->register_action(
			'post_submitbox_misc_actions',
			array( 'controller.ai1ecti', 'post_meta_box' )
		);
		// Add the "Post to Twitter" functionality.
		$dispatcher->register_action(
			'ai1ec_save_post',
			array( 'controller.ai1ecti', 'handle_save_event' )
		);
		$dispatcher->register_action(
			self::CRON_HOOK_NAME,
			array( 'controller.ai1ecti', 'send_twitter_messages' )
		);
	}

	/**
	 * Register commands handlers
	 *
	 * @return void
	 */
	protected function _register_commands() {
		$this->_registry->get( 'command.resolver', $this->_request )
			->add_command(
				$this->_registry->get(
					'command.twitter-oauth',
					$this->_request
				)
			);
	}

	/**
	 * Register cron handlers.
	 *
	 * @return void
	 */
	protected function _register_cron() {
		$this->_registry->get( 'scheduling.utility' )->reschedule(
			self::CRON_HOOK_NAME,
			'hourly'
		);
	}

}