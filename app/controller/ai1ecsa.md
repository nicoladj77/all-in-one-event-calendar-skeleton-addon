Skeleton add-on: example front controller.

@author     Time.ly Network Inc.
@since      1.0

@package    AI1ECSA
@subpackage AI1ECSA.Controller
@see Ai1ec_Base_Extension_Controller::minimum_core_required()
```php
class Ai1ec_Controller_Ai1ecsa extends Ai1ec_Base_Extension_Controller {public function minimum_core_required() {
```
Initializes the extension.

@param Ai1ec_Registry_Object $registry
```php
public function init( Ai1ec_Registry_Object $registry ) {
```
Generate HTML box to be rendered on event editing page

@return void Method does not return
```php
public function post_meta_box() {
```
Cron callback processing (retrieving and sending) pending messages

@return int Number of messages posted to Twitter
```php
public function send_twitter_messages() {
```
Action performed during activation.

@param Ai1ec_Registry $ai1ec_registry Registry object.

@return void Method does not return.
```php
public function on_activation( Ai1ec_Registry $ai1ec_registry ) {
```
Handles event save for Twitter purposes.

@param Ai1ec_Event $event Event object.

@return void Method does not return.
```php
public function handle_save_event( Ai1ec_Event $event ) {
```
Retrieves a list of events matching Twitter notification time interval

@return array List of Ai1ec_Event objects
```php
protected function _get_pending_twitter_events() {
```
Checks and sends message to Twitter.

Upon successfully sending message - updates meta to reflect status change.

@param Ai1ec_Event                    $event    Event object.
@param Ai1ecti_Oauth_Provider_Twitter $provider Twitter Oauth provider.
@param array                          $token    Auth token.

@return bool Success.

@throws Ai1ecti_Oauth_Exception In case of some error.
```php
protected function _send_twitter_message( $event, $provider, $token ) {
```
Extract hashtags based on event taxonomy.

@param Ai1ec_Event $event Instance of event object.

@return array List of unique hash-tags to use (with '#' symbol).
```php
protected function _get_hashtags( Ai1ec_Event $event ) {
```
Gets OAuth token.

@return string OAuth token.

@throws Ai1ecti_Oauth_Exception
```php
protected function _get_token() {
```
Register custom settings used by the extension to ai1ec general settings
framework

@return void
```php
protected function _get_settings() {
```
Register actions handlers

@return void
```php
protected function _register_actions( Ai1ec_Event_Dispatcher $dispatcher ) {
```
Register commands handlers

@return void
```php
protected function _register_commands() {
```
Register cron handlers.

@return void
```php
protected function _register_cron() {
```