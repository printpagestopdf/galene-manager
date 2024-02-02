=== Manager for Galène videoconference ===
Contributors: theripper
Tags: videoconference, lectures, conferences, meetings
Requires at least: 4.7
Tested up to: 6.4.3
Requires PHP: 7.4
Stable tag: 0.5.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Management system for a local or remote Galène videoconference server. Manages access for registered users, wordpress roles or by accesscode.

== Description ==

# Manager for Galène videoconference

*"Galène* (or <em>Galene</em>) is a videoconference server (an “[SFU](https://webrtcglossary.com/sfu/)”) that is easy to deploy and that requires moderate server resources. It was originally designed for lectures, conferences and student tutorials, but is also useful for traditional meetings. ... Galène is free and open source software, subject to the [MIT licence](https://github.com/jech/galene/blob/master/LICENCE)." (Detailed information on [Product Page](https://galene.org/) and [Code Repository](https://github.com/jech/galene))

Galène does not provide a graphical user interface to manage (create/change/delete) conferencing rooms (groups), dedicated users or access control by a code number. This wordpress plugin is able to fullfill such tasks.
Prerequisit is a local or remote Galène videoconference server.

## Using the plugin

There are two ways to access the management interface of the Plugin:

1. Page template 'Galène video conferencing manager'
    The plugin installs a custom page template ('Galène video conferencing manager'). Create a page and use this template. This page is the manager app that can be used.
2. Shortcode [galene\_main]
    The shortcode [galene\_main] can be added to arbitrary pages to show up the manager app.

On first invocation there is an empty list of rooms. You have to login by the "Administration" Button. After installation there is one administrative user registered as:
Login: *galene\_admin*
Password: *galene*
(Change password immediately after login by the user dialog!)

**Alternative** login to the administration screens:
The plugin adds the wordpress role '<strong>GaleneManager</strong>'. If a wordpress user with this role is logged in at wordpress admin (backend), this user is allowed use the management interface of Galène video conferencing manager.

## System settings

**Tab: General**
The url to the galene group without the name of a dedicated group

**Tab: Server connection**
There are 3 ways to connect to the Galène videoserver for managing rooms (groups):

1. filesystem access
2. sftp access
3. manually exchanging .json files with the Galène server manager.

Case 1. or 2. are set up by this tab. If you set up one of these, the syncronization between the plugin and the Galène server is done automatically when you save room settings.

(The export and import of .json room (group) configuration files can be done by the Server tab of the <strong>Room settings</strong>.)

Important: If SFTP access is used it is possible to save the credentials in the Database of the Plugin. To ensure that this data is encrypted, it is advised to add an encryption key to wp-config.php with the following setting:
`define( 'GALMGR_CRYPT_KEY', 'TapaWrJuFy1KpSxfzKzN1Nx07MgdTGV0BakcNcEg/V4=' );`
You can easily get a random crypt key by opening the settings Form. In the SFTP part you will find a randomly generated key.

## Room settings

(<em>groups</em> in Galène terminology)
There are various variations and attributes of videoconference rooms that can be created by the plugin. The most important on the room settings screen are:

* Name and description
* which roles need a user authentication
* optional generating a code for accessing the room
* selecting users if appropriate
* selecting wordpress roles if appropriate
* setting parameters for Galène room (group)

## Users

Beneath wordpress users (roles) and anonymous users it is possible (and adviced) to use named users that have to use a username/password combination to authenticate before allowed to access the room. In most cases this should be used for room operators and presenters.

By the users dialog users can be added and updated:

* display name
* loginname
* password
* if the user is allowed to administrate the Galène manager

= Demo =
None

== Installation ==

* Install and activate the plugin by one of usual wordpress methods
* Create a page with the new page template 'Galène video conferencing manager'
* Open this page in preview or normal mode and login with default credentials (<em>galene\_admin</em>, <em>galene</em>)
* Change password of *galene\_admin* and setup system

== Screenshots ==

1. Public rooms list.
2. Room settings form.
3. User access list of room.
4. System settings (sftp access).

== Frequently Asked Questions ==

= The Gutenberg Editor always displays 'Standard Template' if 'Galène video conferencing manager' is chosen as page template =
Gutenberg Editor seems to handle page template names differently. Nevertheless, if 'Galène video conferencing manager' page template was choosen and the page successfully saved, this template ist used (independantly from the missleading display).
Best way is, to use the Quick Edit in page listing to set and control the used template.

= Is it possible to edit the page if page template 'Galène video conferencing manager' is used =
Yes, the title and the top of page is visible on the resulting page and can freely edited.

= After installation an error message shows up in 'Settings -> Public base link of Galène Server' =
This is a missleading message, that this field should not be empty. Add a valid link to the group path of your Galène Server, save it, and the message is not longer shown

== Changelog ==
= 0.5 =

* Initial release.

== Upgrade Notice ==
None

== License ==
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

== Version history ==
0.5 - Initial release.