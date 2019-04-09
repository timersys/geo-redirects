=== Geo Redirects ===
Contributors: timersys
Donate link: https://timersys.com
Tags: geo redirects, geo redirection, redirect by country, geotargeting, geolite, maxmind
Tested up to: 5.1.1
Stable tag: 1.3.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Create Geo redirects in an incredible easy way and use different set of rules to match users

== Description ==
Geo redirects plugins it's much more than a simple geo plugin. You can create powerful redirections with the simple rules interface.

Rules available:


* Redirect based on City, country, states
* Redirect on specific pages, templates, posts, etc
* Redirect users from search engines
* Redirect users that never commented
* Redirect users that arrived via another page on your site
* Redirect users via roles
* Redirect depending on referrer
* Redirect to logged / non logged users
* Redirect or not to mobile, desktop and tablet users
* Redirect or not to bots / crawlers like Google
* Redirect or not depending on query strings EG: utm_source=email
* Redirect depending on post type, post template, post name, post format, post status and post taxonomy
* Redirect depending on page template, if page is parent, page name, page type

== Installation ==


== Changelog ==

= 1.3.6.1 =
* Core updates
* Added fix to avoid redirects loops automatically
* query string in ajax mode

= 1.3.6 =
* Added predefined continents regions
* Fixed api for plugin updates
* Fixed settings not saving unchecks

= 1.3.5 =
* Fixed uninstall
* Reorganized settings

= 1.3.4.2 =
* Hotfix for bug introduced in 2.3.6.1 ( Important update! )

= 1.3.4.1 =
* Updated core files to try prevent bots consuption
* Added zip rule
* Added cookie rule

= 1.3.4 =
* Updated core files and settings page

= 1.3.3 =
* Added redirect post type to be excluded from search
* Updated debug data page and core files
* Updates Crawler detect library
* Minor bugfixes

= 1.3.2.3 =
* Fix issue with ACF latest version
* Clean up database of old wp_session records

= 1.3.2.2 =
* Fixed "Redirect search engine option" being ignored
* Fixed custom url rule in AJAX mode

= 1.3.2.1 =
* Core updates that fix headers already sent error and fix exclude functionality

= 1.3.2 =
* Fixed core bug that on certain php version geo target function won't return results
* Added cache bust for admin assets

= 1.3.1 =
* Improved settings page and regions creation
* Fixed error where urls were appending ? to the end
* Added filters for devs
* Improved session handling

= 1.3 =
* Added feature to pass query string to destination url
* Fixed issues on ajax mode when sometimes {requested_url} failed
* Upgraded core sessions library

= 1.2.5.2 =
* Fixed bug with query string url rule
* Fixed bug with locales and cache mode that could lead into fatal error

= 1.2.5.1 =
* Fix bug introduced with locales detection

= 1.2.5 =
* Wide url rule /* bug fixed
* Core updates

= 1.2.4 =
* Core updates

= 1.2.3 =
* Added wide url /* rule support
* Improved query string rule
* Fixed issue with wprocket cache

= 1.2.2.1 =
* Fixed Taxonomies filter , not working

= 1.2.2 =
* Fixed issue with AJAX mode when multiple redirects are set

= 1.2.1 =
* Text fixes
* Fixed header broken when using with ajax mode and no other plugin
* Fixed multiple lines breaking on whitelist IP

= 1.2.0.1 =
* Fixed issue with subscription databases

= 1.2 =
* Added placeholders to create dynamic urls
* Updated core files , cache is now db handled

= 1.1 =
* Added ajax redirects for heavy cached sites
* Update core files
* Compatibility with WpEngine Geoip (business and enterprise plans)

= 1.0.2.3 =
* Core files didn't pack on previous version

= 1.0.2.2 =
* Changes rules priority to save users credits by simple putting geo rules at the end
* Updated core files

= 1.0.2.1 =
* Fixed problem with automatic updates
* Updated core files

= 1.0.2 =
* Updated core files

= 1.0.1 =
* Fixed admin assets not loaded if geotargeting pro was not installed
* Fixed countries not loaded if geotargeting pro was not installed
* Added redirect url to redirection list 

= 1.0.0 =
* First version
