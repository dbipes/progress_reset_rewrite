=== LifterLMS Progress Reset ===
Contributors: WooNinjas
Tags: course, memberships, engagement, email, users, progress, reset, lifterlms, llms, courses, membership, user
Requires at least: 5.1
Tested up to: 5.7
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows LifterLMS admin to reset engagement email sequence and the progress of users, courses and memberships.

== Description ==

Allows LifterLMS admin to reset engagement email sequence and the progress of users, courses and memberships.

= Prerequisites: =

* Wordpress (version 5.1 or greater)
* LifterLMS (version 4.5.0 or greater)

= Features: =

* Reset the progress of selected single or multiple users.
* Reset the progress of all users enrolled in the selected single or multiple courses.
* Reset the progress of all users or courses belongs to the selected single or multiple memberships.
* Reset the progress of all inter-related selected users, courses and memberships simultaneously.
* Asynchronous batch process in the background.


== Installation ==

Before installation please make sure you have latest LifterLMS installed.

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Are unreviewed quiz attempts deleted during the progress reset using this add-on? =

Yes, any unreviewed quiz attempts are also deleted when there is a progress reset.

= If there is more than one course included in a single membership, will resetting progress for that membership result in resetting the progress of all of the included courses? =

Yes, this reset will result in the deletion of progress for all the courses within the selected membership.

= Will enrolled students that haven’t started a course yet be affected by the reset? =

The add-on will ignore those users whose progress is zero.

= What does the “Reset email” option do? =

This option resets the sent engagements emails’ sequence, so that the engagements can be reused again. Please follow the official LifterLMS documentation to know more about the engagement emails.

== Screenshots ==

== Changelog ==

= 1.0.2 =
* Fix: Fixed lessons reset issue

= 1.0.1 =
* New: Added option to reset Engagement Emails
* Fix: Fixed code to support translations
* Fix: Fixed select input UI
* Fix: Fixed progress bar UI
* Fix: Fixed spelling mistakes

= 1.0 =
* Initial