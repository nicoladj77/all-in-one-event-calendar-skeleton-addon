All-in-One Event Calendar Skeleton Addon
========================================

Please use this plugin as a guide to building your own Timely
add-on. Start by reading the summary and notes in the file
[all-in-one-event-calendar-skeleton-addon.md](/all-in-one-event-calendar-skeleton-addon.md). Next
spend some time browsing the PHP files. Each one has detailed comments
that will give you a good understanding of the code's purpose.

Please also pay particular attention to the structure of the
folders. This should be consistent across all add-ons, including
yours. We structured our plugins following the
[MVC architectural pattern](http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller). It
helps you create a clean structure. You should get a good sense of
what it means from browsing these files.

Feel free to leave comments with feedback, questions, or improvement
ideas you may have. We look forward to working with you on your
add-on!

The Timely Dev Team.

Skeleton Addon functionality
----------------------------

The All-in-One Event Calendar Skeleton Addon (or *Ai1ECSA* as we would
code-name it) allows you to select a message to be prepended to the
title of selected events. Primary feature might be to display
consistent "Sold Out" message for all events. And be able to turn it's
display site-wide whenever you decide.

### Settings

First of all - there is a configuration section. It is created by
using features provided by
[All-in-One Event Calendar](https://wordpress.org/plugins/all-in-one-event-calendar/)
(or *Core*, as we call it). Skeleton Addon specific settings are found
in WordPress Dashboard, under *Events* -> *Settings* -> *Add-ons* ->
*Skeleton*.

One setting is named `Message to add to sold out events:`. This is the
text value that will be prepended to selected events. It defaults to
`Sold Out!`, but one may choose to use `GONE` or `LAST SEAT
AVAILABLE` because it's just text.

Another setting is `Disable messages everywhere?`. If user checks the
checkbox then the message will not be displayed anywhere.

We see these two types as most common setting elements, so we
feature them in the add-on. But you will find description of other
types in the PHP files,
[for example here](/app/controller/ai1ecsa.md).

### Events management

When you activate the add-on it starts showing a checkbox with the
lable `Event is Sold Out` in post meta box. Post meta box appears for
new and existing posts on top right corner of the page, next to
`Publish` button.

The user choice for this checkbox is stored in post meta. You will
find more about postmeta management in addons
[here](/app/model/skeleton-soldout.md).

### Displaying events

If user has selected the aforementioned checkbox for any of the
events - the value from the Settings will be prepended, surrounded by
square brackets, to the events title when event is being rendered on
the frontend.

So, if configured setting is `Sold Out!` and the event's title is
`Tour of National History museum` and the checkbox is selected then in
all views (Day, Month, Posterboard and others) including event details
page it will be displayed as `[Sold Out!] Tour of National History
museum`. That is unless second option in the settings is activated,
which would disable that prefix.
