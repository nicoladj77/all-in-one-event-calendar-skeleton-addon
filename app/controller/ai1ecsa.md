Skeleton add-on: example front controller.

This is a base class, which extends upon features provided in the Core.
It might be possible to avoid using one, but it's just easier to

The base class here is `Ai1ec_Base_License_Controller` but you may
choose to use `Ai1ec_Base_Extension_Controller` which is a bit simplier
and mostly useful if used outside of Timely bundles.
Indicate the minimum version of Core that you are willing to use.
There are new features, especially under the hood, added with each
release of the Core, so it is better to check what was the first version
to introduce the feature you are using and indicate that as your minimum
required version. Otherwise users will experience errors and see your
add-on being disabled.
```php
class Ai1ec_Controller_Ai1ecsa extends Ai1ec_Base_License_Controller {public function minimum_core_required() {
```
Provide a user-readable name of your plugin here. It will be used
throughout a UI to identify parts specific to your add-on.
```php
public function get_name() {
```
Provide a machine-readable name of your plugin. It should contain letters
and may contain numbers as well as underscore (`_`) characters. It is
used in JavaScript and PHP to reference code blocks specific to your
add-on.
```php
public function get_machine_name() {
```
This method is explicitly provided by Licence extension class. It is
used in combination with Timely licensing server. For more details on
selling add-ons though Timely Marketplace please get in touch with us.
```php
public function get_license_label() {
```
Return your current version. It will be used to inform the user if some
generic information needs to be communicated. Usually it's enough to use
the constant which you defined in the base plugin file.
```php
public function get_version() {
```
Please use the constant from base plugin file here. It is used internally
to diagnose (detect) add-on related actions.
```php
public function get_file() {
```

Defining Settings
=================
If you are going to provide some settings for the user to choose from you
shall keep them in a dedicated tab in the settings. That way it will be
easier to explain choices to the user and keep the UI tidy.
Please replace tab identifier (`'skeleton'`) and title (`'Skeleton'`) in
the code bellow to the values of your choice.
```php
public function add_tabs( array $tabs ) {
```
Register custom settings used by the add-on. This allows you to define
the settings that you need and do not worry about implementing the UI
to choose them. Code bellow displays two types of settings you are likely
to use.
```php
protected function _get_settings() {
```
This is a name of the setting. Please note that it must start with
a unique prefix - for example your plugin name. It must contain
only letters, underscores (`_`) and numbers and start with a letter.
```php
'skeleton_sold_out_message' => array(
```
Renderer element describes how element will be present
to user for selecting a value.
```php
'renderer' => array(
```
Tab indicates a primary location on settings page. You
are likely to always choose `'extensions'` here.
```php
'tab'   => 'extensions',
```
Label is a text visible to user next to a form element
in a few words explaining the meaning of a choice.
```php
'label' => __( 'Message to add to sold out events:', AI1ECSA_PLUGIN_NAME ),
```
This is `'input'` class specific option. You may choose from
`'normal'` - just text, `'date'` - date selections, `'email'`
for email input and `'append'` if you wish to display this as
a HTML snippet explaining options instead of a real option.
```php
'type'  => 'normal',
				),
```
This is a default value that user will see before he makes his
choice.
```php
'value'  => 'Sold out!',
			),
```
Action performed during activation. Please keep all actions that you want
to run only once, during your add-on activation, in this method. Then the
Core will make sure it is executed correctly.
```php
public function on_activation( Ai1ec_Registry $ai1ec_registry ) {
	}
```
Register actions handlers. This is a method provided by the Core which
allows to hook to actions or filters in a sensible manner. Here sensible
means that Core will take care to optimise memory usage for you as much
as possible.
```php
protected function _register_actions( Ai1ec_Event_Dispatcher $dispatcher ) {
```
Here is shown the interaction with settings model. Remember the
[Defining settings][#defining-settings] section above where the
registration takes place. Here we call the settings model. Then
the model is asked to retrieve value for particular setting. If
the value is not defined - the default will be `false`.
```php
$disabled = $this->_registry->get( 'model.settings' )
```
See how message is added to event description in
[view file](/app/view/skeleton/message.md).
Please note the line above - we are checking if user haven't
disabled the messages entirely before adding filter in order
to increaase performance.
```php
$dispatcher->register_filter( 'the_title', array( 'view.skeleton.message', 'filter_title' ), 10, 2 );
        }
```