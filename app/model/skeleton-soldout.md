A model class allows you to interact with the database. It provides
some convenience when managing your code.

Check Event Status
==================

Gets status whereas event is sold out.

@return bool True if event is "Sold Out", false otherwise.
```php
class Ai1ecsa_SkeletonSoldout extends Ai1ec_Base {public function get_post_flag( Ai1ec_Event $event ) {
```

Change Event Status
===================

Sets Sold Out status for an event.

@return bool Success.
```php
public function set_post_flag( Ai1ec_Event $event, $status ) {
```