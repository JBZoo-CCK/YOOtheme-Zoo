# ZOO #

- Version: 3.2.2
- Date: August 2014
- Author: YOOtheme GmbH
- Website: <http://www.yootheme.com>

## Changelog

	3.2.2
	# fixed update script

	3.2.1
	# fixed PHP 5.3 compatibility
	# fixed submission hashes

	3.2.0
	+ added frontend editing for comments and items (ACL)
	+ added ACL
	# fixed submission security settings
	# fixed UIkit templates comments headline markup

    3.1.6
    # fixed missing matchHeight function Comments' Module Bubbles style

	3.1.5
	# updated UIkit icons markup to UIkit 2.0

	3.1.4
	+ added zoo:initApp event
	# fixed mysubmissions pagination renderer
	# fixed CSV export of checkbox elements
	# fixed K2 importer

	3.1.3
    + added Joomla 3.2 compatibility
	^ changed the feed order to publishing date
	# fixed update notifications in PHP 5.4+
	# minor uikit theme fix
	# fixed size of image select box
	# fixed element access issue on search
	# fixed "show empty categories" for individual categories setting

    3.1.2
    # fixed problem with Publish Down Element submission not accepting "never"

	3.1.1
	# fixed mysql index problem

	3.1.0
    + added # items filter to pagination views (Joomla 3.0)
    + added UIkit templates to all apps
	# fixed user edit route in items view
    # fixed link element submission "open in new window" by default
    # fixed conflict issues with JViewLegacy and other components

	3.0.13
	# fixed a php notice on single submission view
	# fixed bug with Joomla 3.1.0 RelatedItem element on submission
	# fixed canonical links with Joomla 3.*

	3.0.12
	# fix for Joomla 3.1.0
	# fixed Joomla importer

	3.0.11
    ^ refactored "Clean Database" function
	^ updated jQuery to 1.9.1
	^ updated jQuery UI to 1.10.1
	^ updated mediaelement.js to 2.10.3
	# fixed use of default values on submission (media element)
	# fixed issue with deleting tags
	# fixed issue with category/tag module showing wrong links

	3.0.10
	# fixed jQuery 1.9 compatibility

    3.0.9
	+ added option to show Captchas for guests only
    # fixed pagination on mysubmissions view
    # fixed item category submission (required state)
	^ moved sh404SEF plugin to separate Joomla plugin
	^ updated jQuery to 1.9
	^ updated jQuery UI to 1.9.2
	^ updated mediaelement.js to 2.10.1

    3.0.8
    # fixed date display in items list view
    # fixed issue with item counting introduced in 3.0.7

	3.0.7
    # fixed rss feed link
    # fixed issue with category assignment (submission)
	# fixed issue with limiting related layout (Related Items Element)

    3.0.6
    ^ improved Google geocoding, by trying to geocode during item save
    + added application:configparams event (3rd party developers)
    # fixed "Add Item" not clickable on MySubmissions view (iOS)
    # fixed Smartsearch indexer (Joomla 3.0)
    # fixed Joomla import (trashed categories are no longer imported)
    # fixed Joomlamodule Element (doesn't show none published module any longer)

    3.0.5
    + added item:beforeSaveCategoryRelations event (3rd party developers)
    # fixed Select Element submission
    # fixed settings on GoogleMaps element
    # fixed issue with element position assignment

    3.0.4
    + added chosen.js to category select field (item/category edit view)
    + added Joomla textfiltering to frontend submission

    3.0.3
    # fixed problem with slug generation
    # fixed problem with deleting codemirror editor

	3.0.2
	+ items in the item view can now be found via alias
	+ the rating element now has an option for Googles Micro Data
    ^ ZOO allows for unicode slugs/aliases now
	# fixed Joomla 3.0 conversion related bugs
    # fixed problems prev/next buttons

	3.0.1
	^ updated jQuery UI to 1.9
	^ updated mediaelement.js to 2.9.4
	# fixed Joomla 3.0 conversion related bugs

	3.0
	+ added "Save As Copy" button on item edit
	+ added Joomla 3.0 compatibility
	- removed Joomla 1.5 compatibility

	2.6.7
	+ added select, radio, checkbox elements to csv ex-/import
	# fixed imports of '0's in CSV import

	2.6.6
	+ added option for alphanumeric sorting to item ordering
	+ added Turkish language pack
	# fixed check for realpath cache size after installation

	2.6.5
	+ added Route Caching (you can enable/disable it in the ZOO manager section)
	^ updated jQuery to 1.8.1
	^ ZOO now returns 404 errors if items or categories can not be found
	^ updated mediaelement.js to 2.9.3
	# fixed redirect to menu item on submission
	# fixed "show empty categories" in several places
	# fixed links in comment reply notifcations, if replied from administration

	2.6.4
	^ updated timepicker to 1.0.1
	^ updated jQuery UI to 1.8.23 (fixed issue with timepicker)
	^ category_id will no longer be appended to url if navigating from primary category

	2.6.3
	+ Googlemaps Element uses autocomplete now
	+ update items via csv
	^ updated jQuery to 1.8.0
	^ updated jQuery UI to 1.8.22
	# fixed twitter comment avatars
	# fixed issue "Fatal error: Class 'systemHelper' not found in /administrator/components/com_zoo/framework/classes/app.php"

	2.6.2
	+ added Item Previous/Next Element
	# fixed googlemaps css issue
	# fixed width problems with TinyMCE
	# fixed issue with tag saving on submission

	2.6.1
	# fixed route helper

	2.6
	+ all apps are now responsive
	+ you can now show/hide comments (Item Print Element)
	+ added keepalive messages to Item/Category Edit views
	+ you can now choose a menu item to redirect too (Submission)
	^ response area is no longer shown on item print view
	# fixed issue with showing search results in the search component backend
	# mootools is loaded for recaptcha to work correctly
	# print button image displays correctly on Blog with Warp6 template

	2.5.20
	+ added metadata to frontpage (Menu Settings)
	+ limit the number of submissions per user
	+ added param to show item count on categories (Category module)
	+ added option to show empty categories
	^ Date Element will accept more date formats now (Frontend Submission)
	^ Frontpage RSS shows its own items only
	^ updated mediaelement.js to 2.9.1
	# fixed conflict between Widgetkit and ZOO (Media Element)

	2.5.19
	# fixed issue with core elements assignment

	2.5.18
	+ added Captcha support to submissions/comments (Joomla 2.5)
	+ primary category will be set on submissions
	+ redirect user to login if item is not accessible
	^ updated MediaElement.js to 2.8.2
	^ updated jQuery UI to 1.8.20
	# fix to 404 redirect if ZOO is set as home page
	# fixed problem with display of Itemaccess Element and Itemstate Element
	# fix to "edit core elements"
	# fixed issue with type copying
	# fixed issue with setting a file value in IE9 (Download Element)

	2.5.17
	+ added Item Edit Element
	^ updated social buttons element (Google Plus One)
	^ updated media element
	^ updated jquery.cookie.js to the latest version
	^ updated timepicker.js to version 1.0.0
	^ protocol is prepended to the link by default now (Link Element)
	^ authors of spam comments won't be subscribed to items any longer
	^ updated jQuery UI to 1.8.19
	# removed API Key from Geocoding Requests (Googlemaps Element)
	# finder component now ignores unsearchable items (Smart Search)
	# item order priority is ignored for feed now
	# fixed conflict between mootools and jQuery slider (Timepicker in J2.5 now works properly again)
	# fixed display of googlemaps (My Submissions view)
	# fixed file upload submission (Image and Download Element)
	# fixed search bug in items default view
	# fixed css issue with tag submission
	# fixed socialbuttons element
	# fixed primary category selection
	# fixed to search ordering (J2.5)

	2.5.16
	+ added "addmenuitems" event
	+ added map type "Terrain" (GoogleMaps Element)
	+ API Key will be used for Geocoding Requests too (Googlemaps Element)
	^ updated jQuery to version 1.7.2
	^ updated jQuery UI to 1.8.18
	^ longitude/latitude coordinates may now contain a space (Google Maps element)
	# fixed type copying
	# fixes to checkbox element edit view css


	2.5.15
	+ you can now hide the update notifications for current session
	# fixed problem with ZOO administration with open_basedir restriction in effect
	# fixed issue with Publish Down date on some systems (Submission)
	# fixes to protocoll part of url in Socialbutton and Media elements
	# fix to DB backup functionality

	2.5.14
	# download element will check against default access value now, if none is set
	# fixed bugs related to the field API change

	2.5.13
	+ added option to fully hide/show categories on category/frontpage views
	+ assign access level to core elements
	# editing a submission in none trusted mode will set the item to unpublished
	# fixed bug with submission (Documentation app)
	# Item Frontpage, Item Searchable and Item State elements no longer sortable
	# fix to Page Titles on frontpage (J2.5)
	# fixed css issues (Social Buttons Element)
	# fixed language problem with Facebook Like Button (Social Buttons Element)

	2.5.12
	# fixed problem with Download element introduced in 2.5.11
	# fixed issue with CSV export (PHP < 5.3)

	2.5.11
	# fixed issue in cookbook app (full layout)
	# fixed bug with access level in download element
	+ Widgetkit Element now submittable
	+ Joomla Module Element now submittable
	+ added CSV export
	+ import into subcategories by separating categories through "///" (CSV)
	+ import into existing categories by adding category alias (CSV)
	# fixed metadata import (Joomla)
	# fix to sh404SEF plugin
	^ updated K2 importer to reflect latest changes
	^ updated MediaElement.js to 2.6.5
	# fix to Social Buttons element (IE)
	+ you can now add an API Key (Googlemaps Element)

	2.5.10
	# fixed build problem with Finder plugin (SmartSearch)
	# fixed tag autocompletion

	2.5.9
	+ added option to select primary category (frontend submission)
	+ added MIME TYPE "application/iges"
	+ added sunburst style to syntaxhighlighter (Documentation app - Choose in app template settings)
	+ added War6 Sidebar Style (Category Module)
	^ changes to renderer (Category Module)
	+ added Finder plugin (SmartSearch)
	+ item, category and comments save, delete and stateChanged events now trigger Joomla content plugin events (J2.5)
	# Fixed Joomla Exporter (J2.5)
	# Fixed Image Frontend Submission

	2.5.8
	# elements will receive new identifier on type copy
	+ added application:installed event
	^ updated German translation
	# fixed Norwegian translation
	+ category submission: added param to allow for single or multiple selection
	^ updated MediaElement.js to 2.6.4
	# text and textarea element now show default value, without having to edit the item

	2.5.7
	^ it is now possible to assign item core elements to submissions
	+ added a few item core elements (mainly for item submissions)
	+ beforesave event (submission)
	+ new option to display date in Blog App + Warp 6 theme
	# fixed problems with page title on category view
	^ submission errors are now being translated
	# fixed problem with Blog application if no template is selected
	# minor css fixes
	^ update to comments module
	# consistent ordering of items related categories
	+ Norwegian translation (Thanks to Yngve Rodli)
	# fixed bug with adding images into textarea editor (submission) (J1.7)

	2.5.6
	# fixed bug with twitter connect
	^ cleanup of some javascripts
	+ reintroduced the index.html files in ZOO folders (hello JED ;-) )
	+ updated MediaElement.js to 2.6.1
	# it is now possible to change the width of the audio player

	2.5.5
	# fixed bug with Twitter and Facebook connect
	# fixed bug with changing menu item types
	^ change for Joomla 2.5 compatibility
	+ you can now import into existing categories (JSON import)
	^ disallow "/" character in tags
	^ improved consistency with category links
	# fixed a redirect on item view

	2.5.4
	^ improved error message if ZOO minimum requirements are not met
	^ improved consistency with item links
	^ updated MediaElement.js to 2.5.0
	+ added tags to CSV import
	# fixed problems with showing comments from unpublished items (Comments module)
	# links in notification mails are now SEOed
	# fixed bug with email notifications on comments
	# fixed compatibility issue with Rockettheme Mission Control admin template

	2.5.3
	+ ZOO automatically checks for updates now
	# fixed bug with ordering by rating element
	+ added "toggle frontpage" button to item view
	# minor css fixes
	+ added ZOO version to manager view
	# fixed problem with sorting by publish up date
	+ you can now use youtube shortlinks e.g. "http://youtu.be/XYZ" (Media Element)
	# fixed bug with odd number of tags and outward, inward sorting (Tag module)
	+ csv import: first category is set to primary category

	2.5.2
	# fixed possible cause for JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING upon update
	# fixed default select options of several applications
	+ added "Publish Up" core element (useful for displaying dates in blog)
	^ updated MediaElement.js to 2.3.2
	^ updated jQuery to version 1.7.1
	+ added editDisplay event to type edit view
	# fixed problems with J1.7 import of categories having the same alias
	# minor css fixes in item edit view
	# fixed item ordering of RSS feeds

	2.5.1
	+ added installer check for missing DB tables (J1.7)
	+ added import of country element to csv import
	+ readded deprecated functions for submission renderer
	^ Tag view now displays tag in page title
	^ updated Hungarian language files
	# fixed problem with installing ZOO Quickicon module with open_basedir restriction in effect

	2.5
	# fixed bug with form field values in submission
	# fixed bug with comments export
	# alpha index in Documentation app won't show empty categories tab
	^ changed syntaxhighlighter of documentation app
	^ reverted interface of elements hasvalue and render methods to version 2.4
	^ updated jQuery to version 1.7
	+ added ordering to "my submissions" view
	+ added "clean database" functionality
	^ merged "update search data" functionality into "clean database"
	+ added search to mysubmissions view
	^ readded separated_by params to elements xml

	2.5 BETA 9
	# fixed bug in sh404SEF plugin
	# fixed item ordering

	2.5 BETA 8
	# fixed bug where updating would delete custom layouts/positions
	# couple minor bugfixes and improvements

	2.5 BETA 7
	# fixed bug with category view, introduced in BETA 6

	2.5 BETA 6
	# fixed bug with having multiple tabs from different app instances open (administration)
	# items and categories won't inherit Browser Page Titles any longer
	# improved html validation of social buttons element

	2.5 BETA 5
	+ added possibility to specify custom link text (Item Link element)
	# fixed bug with including subcategories in modules
	+ added search by tag in items overview
	# fixed pagination links
	# fixed problems with saving options of select, radio, checkbox elements

	2.5 BETA 4
	+ readded Socialbookmarks element
	+ added separator "none" to textarea element
	# fixed importers
	- removed J1.6, mtree, docman importer
	+ added SEO Pagetitle option (J1.7)
	^ pagetitle of categories changed
	# fixed display of category metadata
	# fixed comments for WARP blog template
	# fixed csv import
	# fixed problems with update process (e.g. Gallery element won't loose data anymore)
	# fixed problems with saving options of select elements
	# when upgrading from 2.4 ZOO will not loose video files any more
	# fixed problems with media element

	2.5 BETA 3
	+ added swf support to media element
	+ added random item ordering
	# fixed bug with renaming types
	# fixed bug with copying types
	^ updated media element

	2.5 BETA 2
	# fixed bug where type config would be overwritten

	2.5 BETA
	^ updated jQuery UI to 1.8.15
	+ added new WARP6 Blog template
	- removed ZOOtools (use Widgetkit instead)
	+ added Social Buttons element
	+ added Media element
	+ added Widgetkit element
	^ revamped Download element
	^ revamped Image element
	^ revamped Gallery element
	- removed Video element
	- removed Social Bookmarks element
	- removed Facebook I Like element
	^ element data now being stored as JSON
	^ ex-/import now uses JSON
	+ added comments ex-/import
	# fixed minor issues with submission.css
	+ new "Update Search Data" button in ZOO manager
	+ update screen now shows changelog

	2.4.17
	# item slug will not change after frontend submission
	^ remove path info from file upload
	# content plugins will be triggered on frontpage and category descriptions in Blog app
	# improved html validation of comments form
	# updated language packages
	# fixed typo in language files
	^ updated framework

	2.4.16
	# fix to path handling on unix systems where root path is empty
	^ updated jQuery to 1.6.4
	^ updated jQuery UI to 1.8.16
	# fixed item frontpage toggle

	2.4.15
	# fixed params for zoosearch module (J1.7)
	# tags are being removed upon item deletion
	# added some missing language strings
	# fixed minor css issue (J1.7)
	# fixed bug with repeatable element and advanced options

	2.4.14
	# fixed bug with params array introduced in 2.4.13

	2.4.13
	# fixed SQL dump functionality
	+ frontpage can be edited from items view now
	# fixed display of plugin names in layout view (J1.7)

	2.4.12
	+ item id being passed to content plugins handling textareas
	# fixed bug with path helper on BSD systems
	+ primary category now being im-/exported
	+ name matching on item xml import
	# fixed problem with timezone offset in item edit view
	^ renamed shortcut to shortcode plugin
	# enforcing nonetrusted mode for public submissions again (Joomla 1.7)
	# fixed pagination and filtering issues
	# fixed options.css for Joomla 1.7
	- removed metadata "title" from item views
	+ you can now assign metadata to categories
	+ you can now assign individual page titles to items and categories
	+ added import of Joomla 1.7 articles
	# fixed bug with display of comment dates in Joomla 1.7

	2.4.11
	^ updated jQuery UI to 1.8.14
	^ updated jQuery to 1.6.2
	# fixed bug with detecting superadmin priviliges on Joomla 1.6+
	^ admins won't receive email notifications, if new comments are rated as spam
	+ added warning if cache path is unwritable
	^ zoo modules now use the components language files instead of their own
	^ modifications to reflect changes in Joomla 1.7
	# fixed minor issue with installer
	# 'Add Tag' button now being translated
	# fixed problems with zoo uninstaller (Joomla 1.6)
	# fixed translation issue with some blog templates
	# fixed translations of validation errors
	+ output Google Geocoding API errors (Googlemaps element)
	^ updated ZOOmaps module
	# fixed issue with ZOO export

	2.4.10
	# ZOO tools load mootools again
	# fixed issues with sh404SEF (Joomla 1.5 and Joomla 1.6)
	# fixed bug with rating element (Joomla 1.6)
	^ updated ZOOcomment module to 2.4.3
	# fixed issue with lightbox not loading
	# fixed bug in slideshow.js
	# fixed bug with access level on csv imported items (Joomla 1.6)

	2.4.9
	# fixed bug introduced in 2.4.8

	2.4.8
	^ changed socialbookmarks element to reflect changes made by Twitter
	# minor fix to ZOOs SEF behavior
	- mootools is not being loaded per default in the frontend anymore
	+ new afterEdit element event
	^ email notification if user edits his submission (and submission is set to unpublished again)
	+ added comment to email notifications
	# fixed problem with date element submission in Joomla 1.6
	# fixed minor bug with related items element
	^ dates are now taking the users timezone into account
	# fixed problem with dates in Joomla 1.6
	# fixed typo in notification mails
	^ changed the elements field of item table to type LONGTEXT
	^ syntaxhighlighter fetches brushes locally now (previous amazon)
	# fixed bug with created feed links in frontpage
	# fixed bug in ZOO shortcut plugin with using aliases
	^ updated jQuery to 1.6.1
	+ added missing display options to facebook i like element
	# fixed typo in ZOO scroller markup

	2.4.7
	^ fixed minor issue with RSS feed link
	^ changed syntaxhighlighter syntax in documentation app
	^ changed plugin names
	+ added access levels for elements
	+ added ZOOitemshortcut plugin
	+ you can now specify title and link options in image frontend submission (trusted mode)
	# fixed problem with loading Joomla content modules in Joomla 1.6
	# fixed problems with language files in Joomla 1.6
	^ performance improvements
	^ enable Joomla plugins on textarea elements per default
	# as stated in the docs - if the app provides config or content params, you can now modify them (contributed by Daniele - Thanks!)
	# Fixed space for floated media elements (display: block)
	^ Renamed CSS classes beginning with "align-" to "alignment-" (Warp6 related)
	^ Added some base CSS in submission.css, comments.css and rating.css (Warp6 related)

	2.4.6
	# fixed bug during update
	# fixed bug with item submission (image, download)

	2.4.5
	# fixed timezone problem with publishing dates (submission)
	# fixed display problem with Finish Publishing date (Joomla 1.6)
	# fixed typo in notification emails
	# fixed bug with pagination link generation
	# fixed bug where users would have to enter a url while commenting (introduced in 2.4.4)
	^ changed modal behavior according to Joomla 1.6.2

	2.4.4
	# fixed: you can now overwrite the elements renderer in the template/renderer folder again (now modules too)
	^ pagination links: page 1 will not be included in link anymore
	^ updater has been revamped
	+ added office 2007 mime types
	^ thumbnails are written to cache via Joomlas write function
	- removed message about unknown files after installation
	# applications language files are loaded whenever application is initialized in frontend
	^ allow for German Umlaute in URLs
	# fixed bug, where directions would not show in IE8 (Googlemaps element)
	+ make use of "default access level" for new items and submissions (Joomla 1.6)
	# fixed problem with storing params in ZOO tag module (Joomla 1.6)
	+ added element:beforedisplay event
	# fixed gallery element (Joomla 1.6)
	+ added frontpage to category filter on items view (contributed by Rene Jeppesen - Thanks!)
	# fixed translation string in rating element

	2.4.3
	+ updated Italian language pack
	# fixed bug with replying to comments (administration)
	# fixed date position in blog noble template
	# fixed English module language files
	# fixed: you can now overwrite the elements renderer in the template/renderer folder again
	# fixed pagination in related items element (submission)
	# fixed typo in German language file
	# fixed problems with install.sql file
	# fixed editing in mysubmissions view (Joomla 1.6)
	+ added editing of access level to submission

	2.4.2
	# fixed bug with object initialization (MySQL in STRICT MODE)
	# fixed bug with installation
	^ updated install.sql script to use the ENGINE keyword (needed for newer MySQL versions)
	# fixed bug with counting category items (mysqli)
	^ timepicker now includes seconds
	# fixed problem with storing params in some modules (Joomla 1.6)
	# corrected typo in language files
	# fixed redirect problem after comment state change in administration
	^ empty folders are removed upon installation and modification cleanup
	^ removed last static calls to JUser class
	# fixed bug with gallery element

	2.4.1
	# fixed bug with deleted Joomla users and comment system
	# fixed bug with module params
	# fixed bug with zoosearch plugin on Joomla 1.6
	^ unknown files are removed automatically upon installation (media folder is ignored)
	# fixed bugs in sh404sef standard plugin
	# fixed bug with submitting items in untrusted mode
	^ updated spanish language pack (thanks Miljan)
	# fixed bug with download element
	# fixed bug with exporters (docman, k2, mtree)
	# fixed bug where custom elements would not get loaded

	2.4
	# fixed bug with relateditems element
	# fixed problem with RSS feeds (if 'add suffix' was enabled')
	# fixed javascript issues
	+ added more element events
	^ Socialbookmarks Element enabled by default now

	2.4 BETA4
	# fixed bug where frontpage settings would not be considered
	# fixed translation of timepicker script
	# fixed bug with rendering relateditems
	# fixed bug with displaying categories
	# canonical item links are now absolute
	# submission data is filtered before presented to the user in none trusted mode
	^ some css modifications
	^ changed size of some lightboxes in the administration
	# fixed mootools/jQuery conflict with timepickerscript

	2.4 BETA3
	# fixed several css issues
	# fixed display of quickicons module for Joomla 1.6.1
	# fixed zoo search plugin for Joomla 1.6
	+ added element download event
	+ added language strings
	+ added timepicker functionality
	^ moved remaining Javascripts to media folder
	# fixed bug with inserting images (submission: textarea element / no editor)
	# removed remaining static calls to helpers (fixes several bugs)
	# fixed submission handling on error
	^ removed elements ordering field (fixes saving type elements)
	+ added DB backup functionality
	+ readded missing blog templates

	2.4 BETA2
	+ added init events (triggered when objects are retrieved from database)
	+ added workaround for PHP bug (mysql_fetch_object populates fields after constructor gets called)
	# fixed bug with rendering textarea elements (jplugins)
	# fixed bug with feeds not displaying
	# fixed bug with editing item submissions
	^ performance improvements (specifically if you have items with many elements)
	+ added ITEMID_STATE_INDEX to comments table
	# fixed bug with zoo menu item background-color
	# fixed bug where ZOO was unable to create cache file
	# fixed a bug with selecting files on item edit view
	+ introduced default options for Video Element
	+ introduced default value for JoomlaModule Element
	# fixed bug in facebookilike button
	# removed duplicate email notification parameter from Blog app

	2.4 BETA
	+ added "check for modifications" functionality
	+ added notifications for comments and submissions
	+ added event system
	^ major framework overhaul

	2.3.7
	# fixed import/export of RelatedCategories Element
	- removed pnotify script
	# fixed bug with changing type identifier
	# fixed bug with menu item resizing

	2.3.6
	^ updated jQuery UI to 1.8.10
	+ fixed app tabs now scale in administration area
	# fixed bug in comments admin area
	# fixed spelling bug in Italian language file

	2.3.5
	# fixed bug with category import (csv)
	# fixed problem with mysql strict mode and submissions

	2.3.4
	^ updated jQuery 1.4.4 to jQuery 1.5.1
	# fixed rare problem with setting data on elements
	# fixed bug with catgories not showing in alpha index
	# fixed bug with video element - incorrect size in IE (contributed by daniel.y.balsam - Thanks!)
	# fixed bug with category import (csv)

	2.3.3
	# fixed problem with replying to a comment
	# fixed minor bug with tag renaming
	^ improved error message in pages app (category, frontpage, alphaindex, tag view)
	# fixed spelling bug in German language file
	+ added zoo quick icons module (admin control panel icons)
	# import/export of zoo articles now stores category ordering
	+ added collapsible categories (contributed by Miljan Aleksic - Thanks!)
	# fixed bug with csv import (line endings are recognized across OSs)
	# fix to admin category view

	2.3.2
	# fixed bug with selecting submission while editing menu item
	# fixed bug with pagination on comment, item and tag view (administration)
	# fixed bug with excact search in zoo search plugin
	^ performance optimization with showing items on category/frontpage view
	^ performance optimization in category view
	# fixed spelling bug in German language file
	# fixed bug with radio buttons on assign core elements
	# fixed sorting bug in Webkit browsers (Chrome/Safari)
	# fixed the Digg icon in socialbookmarks element
	^ updated jQuery-ui to 1.8.7

	2.3.1
	^ refactored function for detecting URLs in comments
	+ added some Turkish characters to sluggify function
	# fixed bug, where deleting an item would cause a fatal error upon editing a related item
	# fixed akismet param in business app
	# after editing submissions in untrusted mode, they'll be unpublished again
	^ if submitting too fast, form will stay populated now
	^ reintroduced the "Add tag" button to item edit view
	# fixed bug with inserting images in IE 8
	# fixed bug with download element

	2.3
	^ improved performance with tags
	+ added pagination to the tags default view
	# fixed typos in language files

	2.3 BETA3
	# minor bugfix to reordering categories
	# fixed alpha index special character handling
	# quick pulish/unpublish categories working now
	# fixed bug where loadmodule plugin causes error on feedview
	# fixed bug where expired item would be shown in modules
	# fixed bug with height setting in menu item configuration
	# fixed bug with missing submission values
	# fixed typos in language files
	# fixed bug with saving related items

	2.3 BETA2
	# fixed typo in language files
	# fixed tooltips in types view
	# fixed bug that prevented saving in the manager

	2.3 BETA
	^ migrated to jQuery

	2.2.5
	# fixed order of category tree in item edit view
	# fixed MySQL Database Error Disclosure Vulnerability
	# fixed bug with choosing ZOO items in menu item
	# fixed bug with table prefix

	2.2.4
	+ csv import: fixed bug with name column
	+ csv import: added gallery element
	^ image element - "link to item" will use custom title if specified
	# fixed category item count
	^ improved import/export performance
	+ added new expo template to blog app

	2.2.3
	# fixed import of categories into ZOO

	2.2.2
	# fixed: added MIME types for IE images jpg, png
	# fixed import of categories into ZOO

	2.2.1
	^ fixed memory leak in csv import (works with PHP 5.3 only)
	# fixed issue with sh404SEF and comments
	# fixed issue with autocompleter.request.js filename

	2.2
	# fixed bug with saving item/category relations on import
	+ added mp4 to mime types in framework/file.php
	# fixed issue with sh404SEF
	# fixed default setting for item order in zoomodule.php

	2.2 BETA 2
	# fixed the module class suffix was being ignored in the Joomla Module element
	# fixed bug with item save
	# changed capitalization of autocompleter script
	+ added sh404SEF Plugin
	# fixed bug with submission delete button under "my submissions"

	2.2 BETA
	^ major performance increase in several locations
	^ updated all scripts to run Mootools 1.2
	# fixed bug with date element in IE
	# fixed display bug with JCE editor

	2.1.3
	# fixed googlemaps csv import
	# fixed issues with publish up and publish down (submission)
	+ added support for limiting feeditems (Joomla setting)

	2.1.2
	# fixed bug with google maps marker position
	# fixed bug there deleting an element from a type, would crash the corresponding submission
	+ added support for unity3d files in download element
	# minor bugfix in validator script
	+ added googlemaps element to csv import
	# corrected links to images in feed view
	# fixed bug, where google maps would not detect users preferred language for directions
	^ submission: save category relations - only if not editing in none trusted mode
	# fixed path to files after docman import
	^ on item copy, hits will be reset to zero
	# fixed bug with alpha index (other character - #). Now you can specify a value that's used in the URL
	+ added default value option for Facebook like button

	2.1.1
	# fixed directions not showing in google maps element

	2.1
	# fixed bug with download element, where filesize would not be stored correctly
	# items in modules are ordered by their priority first now.
	# csv import into existing category (name match)
	# fixed timezone bug for date element
	# fixed publish up and down handling in submission
	# fixed bug with Facebook I like button element

	2.1 RC
	+ added publishing dates to submission (trusted mode)
	# fixed bug with JCE being cropped in item edit view
	+ csv import now supports import to repeatable elements
	# fixed bug, where default settings for the radio button couldn't be set
	^ images in the cache folder are now named FILENAME_HASH.Extension
	+ added facebook "I like" button
	+ added category import to CSV import (category will be newly created from category name)
	# fixed bug with item order in item module
	+ added Docman Importer (latest version 1.5.8)
	+ added Mosets Tree Importer (latest version 2.1.3)

	2.1 BETA3
	^ changed facebook connect authentication to oauth
	+ added print element
	+ added csv import
	+ added spam protection (users may only submit items every 5 minutes in public mode)
	+ added canonical link to item view (no more duplicate content worries)
	+ added new noble template to blog app
	# fixed bug with no slugs on adding applications
	# fixed bug with menu item ids and submissions

	2.1 BETA2
	# fixed bug with changing templates on app instance creation, while no app instance exists
	^ added tag name to breadcrumb on tag view
	# fixed filtering items on related items during submission
	# fixed bug with deleting type, while there were no application instances
	^ changed link generation for item and category links
	# "'"'s are now handled correctly in tags
	+ some minor changes to generating slugs
	+ added rel="nofollow" to comment author url
	# fixed bug on "my submissions", where type filter was lost on pagination
	+ added some diacritic characters to alias generation
	+ added "ELEMENT_LIBRARY=Element Library" entry to administrator language file
	^ it is now possible to have textareas as element params
	^ changed mime type for mp3 files
	# fixed bug with "sort into category" in none trusted mode
	+ added new noble template to blog app
	- removed Gzip for CSS files (should be handled by Joomla template or plugin)
	+ added error messages, if no template is chosen
	^ updated language files
	+ added application instance delete warning
	# social bookmarks element - twitter now includes url (Thanks to Jonathan Martin)
	# fixed some HTML markup validation errors
	+ added application slug (needed on tag and alpha index view)
	# fixed bug in "assign elments" screen if no positions were defined
	^ "."'s are no longer allowed in tags (as they cannot be escaped)

	2.1 BETA
	+ added submissions

	2.0.3
	# all template positions are now being saved on type slug rename and copy
	# fixed bug where an items tag would show, even though the publishing date was in the future
	# fixed bug where .flv movies would only play if set to autoplay
	# fixed some HTML markup validation errors
	^ general performance upgrade
	+ added new sans template to blog app
	+ added "above title" media alignment option to the default template of the blog app
	^ removed article separator if article is last in all templates of the blog app (CSS)
	^ changed padding for last elements in text areas (CSS)
	# replaced deprecated ereg function from googlemaps helper
	# fixed bug with tags view and pagination
	# fixed bug where Joomlas email cloaking plugin would introduce a leading space to the email address
	+ added k2 version 2.3 import support
	# fixed bug with chinese characters in slug
	+ added requirements check button to administration
	^ changed "READ MORE" to "READ_MORE" in all language files
	# removed unused helpers/menu.php file (caused problems with Advanced Module Manager)
	- removed needless $params in teaser layout (only documentation app)
	^ moved comment rendering from layouts to item view (all apps)

	2.0.2
	# fixed bug with changing the capitalization in tags
	# fixed bug with capitalized letters in Gravatar email addresses
	# fixed bug with slug input
	# fixed bug with tags import
	# fixed typo in english language file
	# fix to K2 import
	# fixes to ZOO import
	# template positions are now being saved on type slug rename
	# fixes to xml class
	# fixed bug with saving utf-8 encoded category-, type slugs
	^ sluggify now uses Joomlas string conversion
	# fixed bug with '/' being JPATH_ROOT
	# fixed problem with xpath and xmllib version
	# fixed path to zoo documentation in toolbar help
	# fixed path to tmp dir during app installation
	# fixed type name in relateditems element "choose item dialog"

	2.0.1
	+ googlemaps element now renders item layout in popup
	# fixed bug with Twitter Authenticate and SEF turned on
	+ added category module
	# fixed translation of country element
	# fixed language files to include googlemaps element
	# fixed minor css issue with category columns
	^ updated item module to version 2.0.1
	^ updated search plugin to version 2.0.1
	# fixed categories teaser description in cookbook app
	+ country element is now searchable
	^ changes to the applications installer, now accepts different archive types
	# fixed bug with rss feed item order
	# fixed bug with comment cookie scope
	# fixed minor CSS issue with comments in documentation app
	# fixed filtering bug for relateditems element
	# fixed bug with utf-8 encoding of the default.js file
	# fixed bug with saving utf-8 encoded item-, category-, type slugs
	# fixed bug with breadcrumbs (direct link to item)
	+ added some exceptions to the application installer
	# fixed bug with alpha index

	2.0.0
	^ changed error message for position.config not writable
	# fixed bug with gifs in imagethumbnail
	# fixed bug with removing last tag from item
	# fixed bugs with editing tags on item edit in browsers with webkit engine

	2.0.0 RC 2
	# fixed breadcrumbs in item view
	# fixed bug with comment login cookie
	^ added check script to installation process
	# fixed bug with exception class name
	# fixed comment filters in backend
	# fixed bug with special character in app name
	$ updated language files
	# fixed capital characters in position names
	# fixed option parameter in element links
	^ relateditems ordered by default are now ordered as ordered in item view

	2.0.0 RC
	# fixed relateditem.js
	# try to set timelimit in installer

	2.0.0 BETA 4
	# fixed bug with item copy, if no item is selected
	# fixed bug with install script
	# fixed bug with image element link
	# fixed bug with related items import
	# fixed bug with tag import
	# special characters in textarea and text control
	# fixed relateditems delete

	2.0.0 BETA 3
	# fixed "add options" bug in edit elements view
	# fixed parameter settings in ZOO administration panel
	^ updated addthis element
	# fixed pagination on frontpage layout in SEO mode
	# fixed link in item module
	# fixed link in image element
	# fixed generated link through menu_item parameter in module
	+ added update functionality to ZOO installer
	# fixed links to ZOO in rss feed
	^ changed editor handling in ZOO administration panel
	^ if menuitem is direct link to item, the category won't be added to breadcrump
	# moved applications group field from params to database field

	2.0.0 BETA 2
	+ added support for unicode characters (cyrillic, arabic, ...) in slug
	+ added application wide use of tinyMCE editors in Joomla administration panel
	+ added comment author caching
	# PHP 4 warning now functions as expected
	# use of htmlentities before output to text and textarea fields
	^ merged commentauthor classes into single file
	# vertical tabs are being filtered from CData areas in xml
	# image element: added file exist check
	# bugfixes to import/export
	# fixed some tooltips in Joomla administration panel
	# bugfixes to install application
	# bugfixes to comments
	# bugfix in type delete

	2.0.0 BETA
	+ Initial Release



	* -> Security Fix
	# -> Bug Fix
	$ -> Language fix or change
	+ -> Addition
	^ -> Change
	- -> Removed
	! -> Note
