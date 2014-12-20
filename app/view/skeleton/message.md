This class conditionally adds user selected message to the
begining of event title.
This is a user selected message.
This method is filtering result in response to
[`the_title`](http://codex.wordpress.org/Plugin_API/Filter_Reference/the_title)
WordPress filter.
Based on `$post_id` passed it attempts to find corresponding event
and check if user has indicated it as sold-out. If that is the case
- adds the prefix of user choice.
```php
class Ai1ecsa_View_Skeleton_Message extends Ai1ec_Base {protected $_message = '';public function filter_title( $title, $post_id ) {
```