# LifterLMS Progress Reset

Allows LifterLMS admin to reset engagement email sequence and the progress of users, courses and memberships.

## Description

Allows LifterLMS admin to reset engagement email sequence and the progress of users, courses and memberships.

**Prerequisites**

- Wordpress (version 5.1 or greater)
- LifterLMS (version 4.5.0 or greater)

**Features**

- Reset the progress of selected single or multiple users.
- Reset the progress of all users enrolled in the selected single or multiple courses.
- Reset the progress of all users or courses belongs to the selected single or multiple memberships.
- Reset the progress of all inter-related selected users, courses and memberships simultaneously.
- Asynchronous batch process in the background.

## Installation

Before installation please make sure you have latest LifterLMS installed.

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

## FAQ

**Question:** Are unreviewed quiz attempts deleted during the progress reset using this add-on?

**Answer:** Yes, any unreviewed quiz attempts are also deleted when there is a progress reset.

**Question:** If there is more than one course included in a single membership, will resetting progress for that membership result in resetting the progress of all of the included courses?

**Answer:** Yes, this reset will result in the deletion of progress for all the courses within the selected membership.

**Question:** Will enrolled students that haven’t started a course yet be affected by the reset?

**Answer:** The add-on will ignore those users whose progress is zero.

**Question:** What does the “Reset email” option do?

**Answer:** This option resets the sent engagements emails’ sequence, so that the engagements can be reused again. Please follow the official LifterLMS documentation to know more about the engagement emails.

## Changelog

[See all version changelogs](CHANGELOG.md)
