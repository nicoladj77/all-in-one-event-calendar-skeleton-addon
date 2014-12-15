Dictionary
==========
* Core - short name for 'All-in-One Event Calendar' plugin

Plugin Specific Prefix
======================
Functions, constants, and class names should use prefix constructed the
following way:
* split plugin directory name by '-' character into an array;
* take first letter of each word from the array;
* if you happen to have word 'AIOEC' - replace it with 'AI1EC'.

In this case plugin name is 'all-in-one-event-calendar-skeleton-addon', so:
* array( 'all', 'in', 'one', 'event', 'calendar', 'skeleton', 'addon' );
* 'aioecsa';
* 'ai1ecsa'.
That means prefix for constants will be 'AI1ECSA_', for functions 'ai1ecsa_'
and for class names - 'Ai1ecsa_'.

ProTip: use name representing your own project, like 'jedi-style-calendar-header'
and then 'JSCH_', 'jsch_' and 'Jsch_' prefix for your constants, functions and
classes. This way you will avoid collisions with other developers.

Plugin Name
===========
This is a convenience constant - it allows to not repeat yourself when you
need to use plugin name, like in loading text domain (for translations) and
similar.
```php
define( 'AI1ECSA_PLUGIN_NAME', 'all-in-one-event-calendar-skeleton-addon' );
```

Plugin Version
==============
This constant should be in sync with version number defined in plugin title.
While it is not used "automatically" it allows to make use of some automatics
like tracking if addon was updated and performing corresponding actions.
```php
define( 'AI1ECSA_VERSION',     '0.0.9' );
```

Core Debug
==========
There is one feature, which you may find useful during development, which is
called Core debug. You may enable it by adding the following line to your
[`wp-config.php`](http://codex.wordpress.org/Editing_wp-config.php)) file:
```php
define( 'AI1EC_DEBUG', true );
```
This will enable the Core debug mode (change 'true' to 'false' to disable it)
and that means that if you connected your plugin to the core (see
[section on bootstrapping](#bootstrap) for more on the topic) you will be able
to use [class loading](#class-loading).

Class Loading
=============
If you register your plugin with the Core - it allows you to use some of the
features. One of these is class loading.

For it to work you need to have a  writable directory `./lib/bootstrap/` (if
you are using Git or similar - it is easiet to create empty file in it called
EMPTY - then it will be always versioned).` Your classes are collected when
you refresh any page with [Core debug](#core-debug) mode enabled.

Now that class loading is enabled any class in your plugin might be loaded
using it's location.
For exampleif you have a class in file `./app/controller/ai1ecsa.php` then
you may load it using the following command:
```php
$registry->get( 'controller.ai1ecsa' );
```
Note that directories named 'app' or 'lib' are removed automatically to
increase readability. But if you have file named
`./app/controller/skeleton/view.php` then instead of `'controller.ai1ecsa'`
in the code block above you would use `'controller.skeleton.view'`.


Please Be *respectful*
----------------------
Just like [function names in WordPress](http://codex.wordpress.org/Writing_a_Plugin)
you should make sure your file names are unique amongst other Core addons.
Instead of creating file `./app/controller/main.php` choose name like
`./app/controller/skeleton/optimus-prime.php` or
`./app/controller/walking-dead.php` whichever represents it's content better
while being unique.

Bootstrap
=========
This is so called "bootstrap" function - it connects your plugin to the Core
and performs minimal initialization including the loading of textdomain which
is mandatory if you are using translations.
Last line is important:
* it shows use of class loading provided by the Core (see
[Class Loading](#class-loading) section for more);
* it calls `init` method on your controller, which will be explained in
a page for [Ai1ecsa Controller](ai1ecsa.md).

@wp_hook ai1ec_loaded Action 'ai1ec_loaded' is triggered when WordPress native
action 'plugins_loaded' executes.
```php
function ai1ec_skeleton_addon( Ai1ec_Registry_Object $registry ) {
```

Safeguard
=========
When user activates a plugin it's good to check that your plugin will be able
to do anything. Otherwise you will waste user resources, and may even
disappoint your user, as he will think that your plugin does nothing.
Bellow is a piece of code, which you may copy as-is (just make sure to update
prefixes accordingly. The lines which you are ought to modify are accomponied
by a comment stating what to change and how.

@wp_hook activation This function is triggered when a plugin is activated by
a user.