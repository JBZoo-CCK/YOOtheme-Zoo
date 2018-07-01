# Changelog

## 3.3.29

### Fixed

- Fixed rendering issue on iOS devices

## 3.3.28

### Changed

- Updated installation sql for GTID consistency compatibility
- Updated URL validator regex

### Fixed
 
- Fixed use https:// in google geolocate API

## 3.3.27

### Fixed

- Fixed keep current protocol in canonical links
- Fixed use API key in google geolocate

## 3.3.26

### Fixed

- Fixed bug using PDO database driver
- Fixed error in demo package installation (J3.7)
- Fixed gallery slideshow element

## 3.3.25

### Fixed

- Fixed PHP 7 warning
- Fixed UIkit 2 templates
- Fixed alpha-index in UIkit 3 themes

## 3.3.24

### Changed

- Updated UIkit 3 templates
- Updated module positions when using a YOOtheme Pro template (ZOO demo package)

## 3.3.23

### Added

- Added UIkit 3 templates for ZOO modules

### Fixed

- Fixed layout positions configuration
- Fixed modal behavior in administration

## 3.3.22

### Added

- Added new core element Item Primary Category
- Added UIkit 3 templates for all apps

## 3.3.21

### Fixed

- Fixed select author in item edit
- Fixed closing of modals
- Fixed ACL check for new items

## 3.3.20

### Fixed

- Fixed error in creating application (J3.6)
- Fixed ajax saving of permission rules

## 3.3.19

### Fixed

- Fixed error in loading configuration (J3.6)
- Fixed alignment of category image business app
- Fixed declaration of constructor zoosearch

## 3.3.18

### Changed

- Updated loading gravatar images

### Fixed

- Fixed encoding errors on non-utf8 folder/filenames
- Fixed setting system value for robots metatag
- Fixed positions.config in default template for download app
- Fixed cross-site scripting vulnerabilities

## 3.3.17

### Fixed

- Fixed push/pull classes in uikit templates for cookbook, movie and business
- Fixed invalid ID in textarea

## 3.3.16

### Added

- Added defaultvalues for core submission fields
- Added Poster image to HTML5 video

### Changed

- Updated mediaelement.js to 2.20.1
- Redirect to login page if guest user has no access to submissions
- Removed J25 reverse compatibility

### Fixed

- Hide image link on print page
- Fix image alignment on mobile module ZOOitem
- Fixed full screen display video iframes
- Fixed wrong class name in UIkit template renderer for movie App
- Fixed missing controls attibute in media element
- Fixed select author in item edit

## 3.3.15

### Changed

- Updated EU countries list
- Itemcategory element's submission view adapts in height

### Fixed

- Fixed submissions.css checkbox/radio for UIKit app templates
- Fixed Twitter oAuth, using API 1.1
- Fixed Submission redirect after saving
- Fixed Print element button display

## 3.3.14

### Fixed

- Fixed shortcode plugin issues

## 3.3.13

### Added

- Added images and links to Joomla importer

### Changed

- Removed index.html files from ZOO folders

### Fixed

- Fixed thumbnail cache

## 3.3.12

### Changed

- Moved images cache folder to media

## 3.3.11

### Fixed

- Fixed App install security restriction introduced in Joomla! 3.4.5
- Fixed Item Feed date display

## 3.3.10

### Fixed

- Fixed canonical links

## 3.3.9

### Fixed

- Fixed item routing

## 3.3.8

### Fixed

- Removed open/unclosed </div> from cookbook UIkit template
- Fixed missing render layout warning
- Fixed use of protocol-relative urls in Google Maps and AddThis element
- Fixed Google Maps element HTML validation
- ZOO source files can now be symlinked
- Fixed frontpage editing for non super users
- Improved item routing fixing some potential issues

## 3.3.7

### Fixed

- Fixed application template switching

## 3.3.6

### Added

- Added item:save event

### Fixed

- Fixed query issues rised with previous release

## 3.3.5

### Added

- Added cloak toggle parameter for Email element
- Added item:changeorder event

### Fixed

- Fixed alignement in UIkit template
- Fixed submission deleting bug for users without joomla 'can delete' permission
- Fixed submission access levels
- Fixed documentation link
- Fixed Options element empty values check
- Fixed itemaccess element submission validation

## 3.3.4

### Fixed

- Removed Item Access element as orderable option
- Fixed UIkit template display issues
- Fixed zoosmartsearch to no longer trigger an error if iconv is not present
- Fixed spotlight missing assets
- Fixed 1-Click Updates issues

## 3.3.3

### Added

- Renderer minor improvements

### Fixed

- Fixed content plugins triggering

## 3.3.2

### Added

- Gallery Element
- Image Element Lightbox and Spotlight feature
- Link Element Lightbox feature

## 3.3.1

### Fixed

- Recovered index.html file creation in some areas
- Fixed j25 compatibility issues

## 3.3.0

### Added

- 1-Click Updates support
- default layout of an newly created app set to 'uikit'
- added frontpage shortcode
- added shortcode output param

### Changed

- removed index.html files from ZOO folders

### Fixed

- fixed usage of special chars in item names
- avoid errors display if the item is of unrecognised type
- fixed category:delete event name typo
- fixed comments email links generation

## 3.2.4

### Fixed

- fixed item deleting

## 3.2.3

### Fixed

- improved backward compatibility for 3rd party extensions
- changed _respond.php input to button element for comments submission for UIKit templates
- fixed rating bug
- fixed image alignment in UIKit template for product app

## 3.2.2

### Fixed

- fixed update script

## 3.2.1

### Fixed

- fixed PHP 5.3 compatibility
- fixed submission hashes

## 3.2.0

### Added

- added frontend editing for comments and items (ACL)
- added ACL

### Fixed

- fixed submission security settings
- fixed UIkit templates comments headline markup

## 3.1.6

### Fixed

- fixed missing matchHeight function Comments' Module Bubbles style

## 3.1.5

### Changed

- updated UIkit icons markup to UIkit 2.0

## 3.1.4

### Added

- added zoo:initApp event

### Fixed

- fixed mysubmissions pagination renderer
- fixed CSV export of checkbox elements
- fixed K2 importer

## 3.1.3

### Added

- added Joomla 3.2 compatibility

### Changed

- changed the feed order to publishing date

### Fixed

- fixed update notifications in PHP 5.4+
- minor uikit theme fix
- fixed size of image select box
- fixed element access issue on search
- fixed "show empty categories" for individual categories setting

## 3.1.2

### Fixed

- fixed problem with Publish Down Element submission not accepting "never"

## 3.1.1

### Fixed

- fixed mysql index problem

## 3.1.0

### Added

- added # items filter to pagination views (Joomla 3.0)
- added UIkit templates to all apps

### Fixed

- fixed user edit route in items view
- fixed link element submission "open in new window" by default
- fixed conflict issues with JViewLegacy and other components

## 3.0.13

### Fixed

- fixed a php notice on single submission view
- fixed bug with Joomla 3.1.0 RelatedItem element on submission
- fixed canonical links with Joomla 3.*

## 3.0.12

### Fixed

- fix for Joomla 3.1.0
- fixed Joomla importer

## 3.0.11

### Changed

- refactored "Clean Database" function
- updated jQuery to 1.9.1
- updated jQuery UI to 1.10.1
- updated mediaelement.js to 2.10.3

### Fixed

- fixed use of default values on submission (media element)
- fixed issue with deleting tags
- fixed issue with category/tag module showing wrong links

## 3.0.10

### Fixed

- fixed jQuery 1.9 compatibility

## 3.0.9

### Added

- added option to show Captchas for guests only

### Changed

- moved sh404SEF plugin to separate Joomla plugin
- updated jQuery to 1.9
- updated jQuery UI to 1.9.2
- updated mediaelement.js to 2.10.1

### Fixed

- fixed pagination on mysubmissions view
- fixed item category submission (required state)

## 3.0.8

### Fixed
- fixed date display in items list view
- fixed issue with item counting introduced in 3.0.7

## 3.0.7

### Fixed

- fixed rss feed link
- fixed issue with category assignment (submission)
- fixed issue with limiting related layout (Related Items Element)

## 3.0.6

### Added

- added application:configparams event (3rd party developers)

### Changed

- improved Google geocoding, by trying to geocode during item save

### Fixed

- fixed "Add Item" not clickable on MySubmissions view (iOS)
- fixed Smartsearch indexer (Joomla 3.0)
- fixed Joomla import (trashed categories are no longer imported)
- fixed Joomlamodule Element (doesn't show none published module any longer)

## 3.0.5

### Added

- added item:beforeSaveCategoryRelations event (3rd party developers)

### Fixed

- fixed Select Element submission
- fixed settings on GoogleMaps element
- fixed issue with element position assignment

## 3.0.4

### Added

- added chosen.js to category select field (item/category edit view)
- added Joomla textfiltering to frontend submission

## 3.0.3

### Fixed

- fixed problem with slug generation
- fixed problem with deleting codemirror editor

## 3.0.2

### Added

- items in the item view can now be found via alias
- the rating element now has an option for Googles Micro Data

### Changed

- ZOO allows for unicode slugs/aliases now

### Fixed

- fixed Joomla 3.0 conversion related bugs
- fixed problems prev/next buttons

## 3.0.1

### Changed

- updated jQuery UI to 1.9
- updated mediaelement.js to 2.9.4

### Fixed

- fixed Joomla 3.0 conversion related bugs

## 3.0

### Added

- added "Save As Copy" button on item edit
- added Joomla 3.0 compatibility

### Changed

- removed Joomla 1.5 compatibility

## 2.6.7

### Added

- added select, radio, checkbox elements to csv ex-/import

### Fixed

- fixed imports of '0's in CSV import

## 2.6.6

### Added

- added option for alphanumeric sorting to item ordering
- added Turkish language pack

### Fixed

- fixed check for realpath cache size after installation

## 2.6.5

### Added

- added Route Caching (you can enable/disable it in the ZOO manager section)

### Changed

- updated jQuery to 1.8.1
- ZOO now returns 404 errors if items or categories can not be found
- updated mediaelement.js to 2.9.3

### Fixed

- fixed redirect to menu item on submission
- fixed "show empty categories" in several places
- fixed links in comment reply notifcations, if replied from administration

## 2.6.4

### Changed

- updated timepicker to 1.0.1
- updated jQuery UI to 1.8.23 (fixed issue with timepicker)
- category_id will no longer be appended to url if navigating from primary category

## 2.6.3

### Added

- Googlemaps Element uses autocomplete now
- update items via csv

### Changed

- updated jQuery to 1.8.0
- updated jQuery UI to 1.8.22

### Fixed

- fixed twitter comment avatars
- fixed issue "Fatal error: Class 'systemHelper' not found in /administrator/components/com_zoo/framework/classes/app.php"

## 2.6.2

### Added

- added Item Previous/Next Element

### Fixed

- fixed googlemaps css issue
- fixed width problems with TinyMCE
- fixed issue with tag saving on submission

## 2.6.1

### Fixed

- fixed route helper

## 2.6

### Added

- all apps are now responsive
- you can now show/hide comments (Item Print Element)
- added keepalive messages to Item/Category Edit views
- you can now choose a menu item to redirect too (Submission)

### Changed

- response area is no longer shown on item print view

### Fixed

- fixed issue with showing search results in the search component backend
- mootools is loaded for recaptcha to work correctly
- print button image displays correctly on Blog with Warp6 template

## 2.5.20

### Added

- added metadata to frontpage (Menu Settings)
- limit the number of submissions per user
- added param to show item count on categories (Category module)
- added option to show empty categories

### Changed

- Date Element will accept more date formats now (Frontend Submission)
- Frontpage RSS shows its own items only
- updated mediaelement.js to 2.9.1

### Fixed

- fixed conflict between Widgetkit and ZOO (Media Element)

# 2.5.19

### Fixed

- fixed issue with core elements assignment

## 2.5.18

### Added

- added Captcha support to submissions/comments (Joomla 2.5)
- primary category will be set on submissions
- redirect user to login if item is not accessible

### Changed

- updated MediaElement.js to 2.8.2
- updated jQuery UI to 1.8.20

### Fixed

- fix to 404 redirect if ZOO is set as home page
- fixed problem with display of Itemaccess Element and Itemstate Element
- fix to "edit core elements"
- fixed issue with type copying
- fixed issue with setting a file value in IE9 (Download Element)

## 2.5.17

### Added

- added Item Edit Element

### Changed

- updated social buttons element (Google Plus One)
- updated media element
- updated jquery.cookie.js to the latest version
- updated timepicker.js to version 1.0.0
- protocol is prepended to the link by default now (Link Element)
- authors of spam comments won't be subscribed to items any longer
- updated jQuery UI to 1.8.19

### Fixed

- removed API Key from Geocoding Requests (Googlemaps Element)
- finder component now ignores unsearchable items (Smart Search)
- item order priority is ignored for feed now
- fixed conflict between mootools and jQuery slider (Timepicker in J2.5 now works properly again)
- fixed display of googlemaps (My Submissions view)
- fixed file upload submission (Image and Download Element)
- fixed search bug in items default view
- fixed css issue with tag submission
- fixed socialbuttons element
- fixed primary category selection
- fixed to search ordering (J2.5)

## 2.5.16

### Added

- added "addmenuitems" event
- added map type "Terrain" (GoogleMaps Element)
- API Key will be used for Geocoding Requests too (Googlemaps Element)

### Changed

- updated jQuery to version 1.7.2
- updated jQuery UI to 1.8.18
- longitude/latitude coordinates may now contain a space (Google Maps element)

### Fixed

- fixed type copying
- fixes to checkbox element edit view css


## 2.5.15

### Added

- you can now hide the update notifications for current session

### Fixed

- fixed problem with ZOO administration with open_basedir restriction in effect
- fixed issue with Publish Down date on some systems (Submission)
- fixes to protocoll part of url in Socialbutton and Media elements
- fix to DB backup functionality

## 2.5.14

### Fixed

- download element will check against default access value now, if none is set
- fixed bugs related to the field API change

## 2.5.13

### Added

- added option to fully hide/show categories on category/frontpage views
- assign access level to core elements

### Fixed

- editing a submission in none trusted mode will set the item to unpublished
- fixed bug with submission (Documentation app)
- Item Frontpage, Item Searchable and Item State elements no longer sortable
- fix to Page Titles on frontpage (J2.5)
- fixed css issues (Social Buttons Element)
- fixed language problem with Facebook Like Button (Social Buttons Element)

## 2.5.12

### Fixed

- fixed problem with Download element introduced in 2.5.11
- fixed issue with CSV export (PHP < 5.3)

## 2.5.11

### Added

- Widgetkit Element now submittable
- Joomla Module Element now submittable
- added CSV export
- import into subcategories by separating categories through "///" (CSV)
- import into existing categories by adding category alias (CSV)
- you can now add an API Key (Googlemaps Element)

### Changed

- updated K2 importer to reflect latest changes
- updated MediaElement.js to 2.6.5

### Fixed

- fixed issue in cookbook app (full layout)
- fixed bug with access level in download element
- fixed metadata import (Joomla)
- fix to sh404SEF plugin
- fix to Social Buttons element (IE)

## 2.5.10

### Fixed

- fixed build problem with Finder plugin (SmartSearch)
- fixed tag autocompletion

## 2.5.9

### Added

- added option to select primary category (frontend submission)
- added MIME TYPE "application/iges"
- added sunburst style to syntaxhighlighter (Documentation app - Choose in app template settings)
- added War6 Sidebar Style (Category Module)
- added Finder plugin (SmartSearch)
- item, category and comments save, delete and stateChanged events now trigger Joomla content plugin events (J2.5)

### Changed

- changes to renderer (Category Module)

### Fixed

- Fixed Joomla Exporter (J2.5)
- Fixed Image Frontend Submission

## 2.5.8

### Added

- added application:installed event
- category submission: added param to allow for single or multiple selection

### Changed

- updated German translation
- updated MediaElement.js to 2.6.4

### Fixed

- elements will receive new identifier on type copy
- fixed Norwegian translation
- text and textarea element now show default value, without having to edit the item

## 2.5.7

### Added

- added a few item core elements (mainly for item submissions)
- beforesave event (submission)
- new option to display date in Blog App + Warp 6 theme
- Norwegian translation (Thanks to Yngve Rodli)

### Changed

- it is now possible to assign item core elements to submissions
- submission errors are now being translated
- update to comments module

### Fixed

- fixed problems with page title on category view
- fixed problem with Blog application if no template is selected
- minor css fixes
- consistent ordering of items related categories
- fixed bug with adding images into textarea editor (submission) (J1.7)

## 2.5.6

### Added

- reintroduced the index.html files in ZOO folders (hello JED ;-) )
- updated MediaElement.js to 2.6.1

### Changed

- cleanup of some javascripts

### Fixed

- fixed bug with twitter connect
- it is now possible to change the width of the audio player

## 2.5.5

### Added

- you can now import into existing categories (JSON import)

### Changed

- change for Joomla 2.5 compatibility
- disallow "/" character in tags
- improved consistency with category links

### Fixed

- fixed bug with Twitter and Facebook connect
- fixed bug with changing menu item types
- fixed a redirect on item view

## 2.5.4

### Added

- added tags to CSV import

### Changed

- improved error message if ZOO minimum requirements are not met
- improved consistency with item links
- updated MediaElement.js to 2.5.0

### Fixed

- fixed problems with showing comments from unpublished items (Comments module)
- links in notification mails are now SEOed
- fixed bug with email notifications on comments
- fixed compatibility issue with Rockettheme Mission Control admin template

## 2.5.3

### Added

- ZOO automatically checks for updates now
- added "toggle frontpage" button to item view
- added ZOO version to manager view
- you can now use youtube shortlinks e.g. "http://youtu.be/XYZ" (Media Element)
- csv import: first category is set to primary category

### Fixed

- fixed bug with ordering by rating element
- minor css fixes
- fixed problem with sorting by publish up date
- fixed bug with odd number of tags and outward, inward sorting (Tag module)

## 2.5.2

### Added

- added "Publish Up" core element (useful for displaying dates in blog)
- added editDisplay event to type edit view

### Changed

- updated MediaElement.js to 2.3.2
- updated jQuery to version 1.7.1

### Fixed

- fixed possible cause for JLIB_APPLICATION_ERROR_COMPONENT_NOT_LOADING upon update
- fixed default select options of several applications
- fixed problems with J1.7 import of categories having the same alias
- minor css fixes in item edit view
- fixed item ordering of RSS feeds

## 2.5.1

### Added

- added installer check for missing DB tables (J1.7)
- added import of country element to csv import
- readded deprecated functions for submission renderer

### Changed

- Tag view now displays tag in page title
- updated Hungarian language files

### Fixed

- fixed problem with installing ZOO Quickicon module with open_basedir restriction in effect

## 2.5

### Added

- added ordering to "my submissions" view
- added "clean database" functionality
- added search to mysubmissions view

### Changed

- changed syntaxhighlighter of documentation app
- reverted interface of elements hasvalue and render methods to version 2.4
- updated jQuery to version 1.7
- merged "update search data" functionality into "clean database"
- readded separated_by params to elements xml

### Fixed

- fixed bug with form field values in submission
- fixed bug with comments export
- alpha index in Documentation app won't show empty categories tab

## 2.5 BETA 9

### Fixed

- fixed bug in sh404SEF plugin
- fixed item ordering

## 2.5 BETA 8

### Fixed

- fixed bug where updating would delete custom layouts/positions
- couple minor bugfixes and improvements

## 2.5 BETA 7

### Fixed

- fixed bug with category view, introduced in BETA 6

## 2.5 BETA 6

### Fixed

- fixed bug with having multiple tabs from different app instances open (administration)
- items and categories won't inherit Browser Page Titles any longer
- improved html validation of social buttons element

## 2.5 BETA 5

### Added

- added possibility to specify custom link text (Item Link element)
- added search by tag in items overview

### Fixed

- fixed bug with including subcategories in modules
- fixed pagination links
- fixed problems with saving options of select, radio, checkbox elements

## 2.5 BETA 4

### Added

- readded Socialbookmarks element
- added separator "none" to textarea element
- added SEO Pagetitle option (J1.7)

### Changed

- removed J1.6, mtree, docman importer
- pagetitle of categories changed

### Fixed

- fixed importers
- fixed display of category metadata
- fixed comments for WARP blog template
- fixed csv import
- fixed problems with update process (e.g. Gallery element won't loose data anymore)
- fixed problems with saving options of select elements
- when upgrading from 2.4 ZOO will not loose video files any more
- fixed problems with media element

## 2.5 BETA 3

### Added

- added swf support to media element
- added random item ordering

### Changed

- updated media element

### Fixed

- fixed bug with renaming types
- fixed bug with copying types

## 2.5 BETA 2

### Fixed

- fixed bug where type config would be overwritten

## 2.5 BETA

### Added

- added new WARP6 Blog template
- added Social Buttons element
- added Media element
- added Widgetkit element
- added comments ex-/import
- new "Update Search Data" button in ZOO manager
- update screen now shows changelog

### Changed

- updated jQuery UI to 1.8.15
- removed ZOOtools (use Widgetkit instead)
- revamped Download element
- revamped Image element
- revamped Gallery element
- removed Video element
- removed Social Bookmarks element
- removed Facebook I Like element
- element data now being stored as JSON
- ex-/import now uses JSON

### Fixed

- fixed minor issues with submission.css

## 2.4.17

### Changed

- remove path info from file upload
- updated framework

### Fixed

- item slug will not change after frontend submission
- content plugins will be triggered on frontpage and category descriptions in Blog app
- improved html validation of comments form
- updated language packages
- fixed typo in language files

## 2.4.16

### Changed

- updated jQuery to 1.6.4
- updated jQuery UI to 1.8.16

### Fixed

- fix to path handling on unix systems where root path is empty
- fixed item frontpage toggle

## 2.4.15

### Added

- added some missing language strings

### Fixed

- fixed params for zoosearch module (J1.7)
- tags are being removed upon item deletion
- fixed minor css issue (J1.7)
- fixed bug with repeatable element and advanced options

## 2.4.14

### Fixed

- fixed bug with params array introduced in 2.4.13

## 2.4.13

### Added

- frontpage can be edited from items view now

### Fixed

- fixed SQL dump functionality
- fixed display of plugin names in layout view (J1.7)

## 2.4.12

### Added

- item id being passed to content plugins handling textareas
- primary category now being im-/exported
- name matching on item xml import
- you can now assign metadata to categories
- you can now assign individual page titles to items and categories
- added import of Joomla 1.7 articles

### Changed

- renamed shortcut to shortcode plugin
- removed metadata "title" from item views

### Fixed

- fixed bug with path helper on BSD systems
- fixed problem with timezone offset in item edit view
- enforcing nonetrusted mode for public submissions again (Joomla 1.7)
- fixed pagination and filtering issues
- fixed options.css for Joomla 1.7
- fixed bug with display of comment dates in Joomla 1.7

## 2.4.11

### Added

- added warning if cache path is unwritable
- output Google Geocoding API errors (Googlemaps element)

### Changed

- updated jQuery UI to 1.8.14
- updated jQuery to 1.6.2
- admins won't receive email notifications, if new comments are rated as spam
- zoo modules now use the components language files instead of their own
- modifications to reflect changes in Joomla 1.7
- updated ZOOmaps module

### Fixed

- fixed bug with detecting superadmin priviliges on Joomla 1.6+
- fixed minor issue with installer
- 'Add Tag' button now being translated
- fixed problems with zoo uninstaller (Joomla 1.6)
- fixed translation issue with some blog templates
- fixed translations of validation errors
- fixed issue with ZOO export

## 2.4.10

### Changed

- updated ZOOcomment module to 2.4.3

### Fixed

- ZOO tools load mootools again
- fixed issues with sh404SEF (Joomla 1.5 and Joomla 1.6)
- fixed bug with rating element (Joomla 1.6)
- fixed issue with lightbox not loading
- fixed bug in slideshow.js
- fixed bug with access level on csv imported items (Joomla 1.6)

## 2.4.9

### Fixed

- fixed bug introduced in 2.4.8

## 2.4.8

### Added

- new afterEdit element event
- added comment to email notifications
- added missing display options to facebook i like element

### Changed

- changed socialbookmarks element to reflect changes made by Twitter
- mootools is not being loaded per default in the frontend anymore
- email notification if user edits his submission (and submission is set to unpublished again)
- dates are now taking the users timezone into account
- changed the elements field of item table to type LONGTEXT
- syntaxhighlighter fetches brushes locally now (previous amazon)
- updated jQuery to 1.6.1

### Fixed

- minor fix to ZOOs SEF behavior
- fixed problem with date element submission in Joomla 1.6
- fixed minor bug with related items element
- fixed problem with dates in Joomla 1.6
- fixed typo in notification mails
- fixed bug with created feed links in frontpage
- fixed bug in ZOO shortcut plugin with using aliases
- fixed typo in ZOO scroller markup

## 2.4.7

### Added

- added access levels for elements
- added ZOOitemshortcut plugin
- you can now specify title and link options in image frontend submission (trusted mode)

### Changed

- fixed minor issue with RSS feed link
- changed syntaxhighlighter syntax in documentation app
- changed plugin names
- performance improvements
- enable Joomla plugins on textarea elements per default
- Renamed CSS classes beginning with "align-" to "alignment-" (Warp6 related)
- Added some base CSS in submission.css, comments.css and rating.css (Warp6 related)

### Fixed

- fixed problem with loading Joomla content modules in Joomla 1.6
- fixed problems with language files in Joomla 1.6
- as stated in the docs - if the app provides config or content params, you can now modify them (contributed by Daniele - Thanks!)
- Fixed space for floated media elements (display: block)

## 2.4.6

### Fixed

- fixed bug during update
- fixed bug with item submission (image, download)

## 2.4.5

### Changed

- changed modal behavior according to Joomla 1.6.2

### Fixed

- fixed timezone problem with publishing dates (submission)
- fixed display problem with Finish Publishing date (Joomla 1.6)
- fixed typo in notification emails
- fixed bug with pagination link generation
- fixed bug where users would have to enter a url while commenting (introduced in 2.4.4)

## 2.4.4

### Added

- added office 2007 mime types
- make use of "default access level" for new items and submissions (Joomla 1.6)
- added element:beforedisplay event
- added frontpage to category filter on items view (contributed by Rene Jeppesen - Thanks!)

### Changed

- pagination links: page 1 will not be included in link anymore
- updater has been revamped
- thumbnails are written to cache via Joomlas write function
- removed message about unknown files after installation
- allow for German Umlaute in URLs

### Fixed

- fixed: you can now overwrite the elements renderer in the template/renderer folder again (now modules too)
- applications language files are loaded whenever application is initialized in frontend
- fixed bug, where directions would not show in IE8 (Googlemaps element)
- fixed problem with storing params in ZOO tag module (Joomla 1.6)
- fixed gallery element (Joomla 1.6)
- fixed translation string in rating element

## 2.4.3

### Added

- updated Italian language pack
- added editing of access level to submission

### Fixed

- fixed bug with replying to comments (administration)
- fixed date position in blog noble template
- fixed English module language files
- fixed: you can now overwrite the elements renderer in the template/renderer folder again
- fixed pagination in related items element (submission)
- fixed typo in German language file
- fixed problems with install.sql file
- fixed editing in mysubmissions view (Joomla 1.6)

## 2.4.2

### Changed

- updated install.sql script to use the ENGINE keyword (needed for newer MySQL versions)
- timepicker now includes seconds
- empty folders are removed upon installation and modification cleanup
- removed last static calls to JUser class

### Fixed

- fixed bug with object initialization (MySQL in STRICT MODE)
- fixed bug with installation
- fixed bug with counting category items (mysqli)
- fixed problem with storing params in some modules (Joomla 1.6)
- corrected typo in language files
- fixed redirect problem after comment state change in administration
- fixed bug with gallery element

## 2.4.1

### Changed

- unknown files are removed automatically upon installation (media folder is ignored)
- updated spanish language pack (thanks Miljan)

### Fixed

- fixed bug with deleted Joomla users and comment system
- fixed bug with module params
- fixed bug with zoosearch plugin on Joomla 1.6
- fixed bugs in sh404sef standard plugin
- fixed bug with submitting items in untrusted mode
- fixed bug with download element
- fixed bug with exporters (docman, k2, mtree)
- fixed bug where custom elements would not get loaded

## 2.4

### Added

- added more element events

### Changed

- Socialbookmarks Element enabled by default now

### Fixed

- fixed bug with relateditems element
- fixed problem with RSS feeds (if 'add suffix' was enabled')
- fixed javascript issues

## 2.4 BETA4

### Changed

- some css modifications
- changed size of some lightboxes in the administration

### Fixed

- fixed bug where frontpage settings would not be considered
- fixed translation of timepicker script
- fixed bug with rendering relateditems
- fixed bug with displaying categories
- canonical item links are now absolute
- submission data is filtered before presented to the user in none trusted mode
- fixed mootools/jQuery conflict with timepickerscript

## 2.4 BETA3

### Added

- added element download event
- added language strings
- added timepicker functionality
- added DB backup functionality
- readded missing blog templates

### Changed

- moved remaining Javascripts to media folder
- removed elements ordering field (fixes saving type elements)

### Fixed

- fixed several css issues
- fixed display of quickicons module for Joomla 1.6.1
- fixed zoo search plugin for Joomla 1.6
- fixed bug with inserting images (submission: textarea element / no editor)
- removed remaining static calls to helpers (fixes several bugs)
- fixed submission handling on error

## 2.4 BETA2

### Added

- added init events (triggered when objects are retrieved from database)
- added workaround for PHP bug (mysql_fetch_object populates fields after constructor gets called)
- added ITEMID_STATE_INDEX to comments table
- introduced default options for Video Element
- introduced default value for JoomlaModule Element

### Changed

- performance improvements (specifically if you have items with many elements)

### Fixed

- fixed bug with rendering textarea elements (jplugins)
- fixed bug with feeds not displaying
- fixed bug with editing item submissions
- fixed bug with zoo menu item background-color
- fixed bug where ZOO was unable to create cache file
- fixed a bug with selecting files on item edit view
- fixed bug in facebookilike button
- removed duplicate email notification parameter from Blog app

## 2.4 BETA

### Added

- added "check for modifications" functionality
- added notifications for comments and submissions
- added event system

### Changed

- major framework overhaul

## 2.3.7

### Changed
- removed pnotify script

### Fixed

- fixed import/export of RelatedCategories Element
- fixed bug with changing type identifier
- fixed bug with menu item resizing

## 2.3.6

### Added
- fixed app tabs now scale in administration area

### Changed

- updated jQuery UI to 1.8.10

### Fixed

- fixed bug in comments admin area
- fixed spelling bug in Italian language file

## 2.3.5

### Fixed

- fixed bug with category import (csv)
- fixed problem with mysql strict mode and submissions

## 2.3.4

### Changed

- updated jQuery 1.4.4 to jQuery 1.5.1

### Fixed

- fixed rare problem with setting data on elements
- fixed bug with catgories not showing in alpha index
- fixed bug with video element - incorrect size in IE (contributed by daniel.y.balsam - Thanks!)
- fixed bug with category import (csv)

## 2.3.3

### Added

- added zoo quick icons module (admin control panel icons)
- added collapsible categories (contributed by Miljan Aleksic - Thanks!)

### Changed

- improved error message in pages app (category, frontpage, alphaindex, tag view)

### Fixed

- fixed problem with replying to a comment
- fixed minor bug with tag renaming
- fixed spelling bug in German language file
- import/export of zoo articles now stores category ordering
- fixed bug with csv import (line endings are recognized across OSs)
- fix to admin category view

## 2.3.2

### Changed

- performance optimization with showing items on category/frontpage view
- performance optimization in category view
- updated jQuery-ui to 1.8.7

### Fixed

- fixed bug with selecting submission while editing menu item
- fixed bug with pagination on comment, item and tag view (administration)
- fixed bug with excact search in zoo search plugin
- fixed spelling bug in German language file
- fixed bug with radio buttons on assign core elements
- fixed sorting bug in Webkit browsers (Chrome/Safari)
- fixed the Digg icon in socialbookmarks element

## 2.3.1

### Added

- added some Turkish characters to sluggify function

### Changed

- refactored function for detecting URLs in comments
- if submitting too fast, form will stay populated now
- reintroduced the "Add tag" button to item edit view

### Fixed

- fixed bug, where deleting an item would cause a fatal error upon editing a related item
- fixed akismet param in business app
- after editing submissions in untrusted mode, they'll be unpublished again
- fixed bug with inserting images in IE 8
- fixed bug with download element

## 2.3

### Added
- added pagination to the tags default view

### Changed

- improved performance with tags

### Fixed

- fixed typos in language files

## 2.3 BETA3

### Fixed

- minor bugfix to reordering categories
- fixed alpha index special character handling
- quick pulish/unpublish categories working now
- fixed bug where loadmodule plugin causes error on feedview
- fixed bug where expired item would be shown in modules
- fixed bug with height setting in menu item configuration
- fixed bug with missing submission values
- fixed typos in language files
- fixed bug with saving related items

## 2.3 BETA2

### Fixed

- fixed typo in language files
- fixed tooltips in types view
- fixed bug that prevented saving in the manager

## 2.3 BETA

### Updated

- migrated to jQuery

## 2.2.5

### Fixed

- fixed order of category tree in item edit view
- fixed MySQL Database Error Disclosure Vulnerability
- fixed bug with choosing ZOO items in menu item
- fixed bug with table prefix

## 2.2.4

### Added

- csv import: fixed bug with name column
- csv import: added gallery element
- added new expo template to blog app

### Changed

- image element - "link to item" will use custom title if specified
- improved import/export performance

### Fixed

- fixed category item count

## 2.2.3

### Fixed

- fixed import of categories into ZOO

## 2.2.2

### Fixed

- fixed: added MIME types for IE images jpg, png
- fixed import of categories into ZOO

## 2.2.1

### Changed

- fixed memory leak in csv import (works with PHP 5.3 only)

### Fixed

- fixed issue with sh404SEF and comments
- fixed issue with autocompleter.request.js filename

## 2.2

### Added

- added mp4 to mime types in framework/file.php

### Fixed

- fixed bug with saving item/category relations on import
- fixed issue with sh404SEF
- fixed default setting for item order in zoomodule.php

## 2.2 BETA 2

### Added

- added sh404SEF Plugin

### Fixed

- fixed the module class suffix was being ignored in the Joomla Module element
- fixed bug with item save
- changed capitalization of autocompleter script
- fixed bug with submission delete button under "my submissions"

## 2.2 BETA

### Changed

- major performance increase in several locations
- updated all scripts to run Mootools 1.2

### Fixed

- fixed bug with date element in IE
- fixed display bug with JCE editor

## 2.1.3

### Added

- added support for limiting feeditems (Joomla setting)

### Fixed

- fixed googlemaps csv import
- fixed issues with publish up and publish down (submission)

## 2.1.2

### Added

- added support for unity3d files in download element
- added googlemaps element to csv import
- added default value option for Facebook like button

### Updated

- submission: save category relations - only if not editing in none trusted mode
- on item copy, hits will be reset to zero

### Fixed

- fixed bug with google maps marker position
- fixed bug there deleting an element from a type, would crash the corresponding submission
- minor bugfix in validator script
- corrected links to images in feed view
- fixed bug, where google maps would not detect users preferred language for directions
- fixed path to files after docman import
- fixed bug with alpha index (other character - #). Now you can specify a value that's used in the URL

## 2.1.1

### Fixed

- fixed directions not showing in google maps element

## 2.1

### Fixed

- fixed bug with download element, where filesize would not be stored correctly
- items in modules are ordered by their priority first now.
- csv import into existing category (name match)
- fixed timezone bug for date element
- fixed publish up and down handling in submission
- fixed bug with Facebook I like button element

## 2.1 RC

### Added

- added publishing dates to submission (trusted mode)
- csv import now supports import to repeatable elements
- added facebook "I like" button
- added category import to CSV import (category will be newly created from category name)
- added Docman Importer (latest version 1.5.8)
- added Mosets Tree Importer (latest version 2.1.3)

### Changed

- images in the cache folder are now named FILENAME_HASH.Extension

### Fixed

- fixed bug with JCE being cropped in item edit view
- fixed bug, where default settings for the radio button couldn't be set
- fixed bug with item order in item module

## 2.1 BETA3

### Added

- added print element
- added csv import
- added spam protection (users may only submit items every 5 minutes in public mode)
- added canonical link to item view (no more duplicate content worries)
- added new noble template to blog app

### Changed

- changed facebook connect authentication to oauth

### Fixed

- fixed bug with no slugs on adding applications
- fixed bug with menu item ids and submissions

## 2.1 BETA2

### Added

- added tag name to breadcrumb on tag view
- some minor changes to generating slugs
- added rel="nofollow" to comment author url
- added some diacritic characters to alias generation
- added "ELEMENT_LIBRARY=Element Library" entry to administrator language file
- added new noble template to blog app
- added error messages, if no template is chosen
- added application instance delete warning
- added application slug (needed on tag and alpha index view)

### Changed

- changed link generation for item and category links
- it is now possible to have textareas as element params
- changed mime type for mp3 files
- removed Gzip for CSS files (should be handled by Joomla template or plugin)
- updated language files
- "."'s are no longer allowed in tags (as they cannot be escaped)

### Fixed

- fixed bug with changing templates on app instance creation, while no app instance exists
- fixed filtering items on related items during submission
- fixed bug with deleting type, while there were no application instances
- "'"'s are now handled correctly in tags
- fixed bug on "my submissions", where type filter was lost on pagination
- fixed bug with "sort into category" in none trusted mode
- social bookmarks element - twitter now includes url (Thanks to Jonathan Martin)
- fixed some HTML markup validation errors
- fixed bug in "assign elments" screen if no positions were defined

## 2.1 BETA

### Added

- added submissions

## 2.0.3

### Added

- added new sans template to blog app
- added "above title" media alignment option to the default template of the blog app
- added k2 version 2.3 import support
- added requirements check button to administration

### Changed

- general performance upgrade
- removed article separator if article is last in all templates of the blog app (CSS)
- changed padding for last elements in text areas (CSS)
- changed "READ MORE" to "READ_MORE" in all language files
- removed needless $params in teaser layout (only documentation app)
- moved comment rendering from layouts to item view (all apps)

### Fixed

- all template positions are now being saved on type slug rename and copy
- fixed bug where an items tag would show, even though the publishing date was in the future
- fixed bug where .flv movies would only play if set to autoplay
- fixed some HTML markup validation errors
- replaced deprecated ereg function from googlemaps helper
- fixed bug with tags view and pagination
- fixed bug where Joomlas email cloaking plugin would introduce a leading space to the email address
- fixed bug with chinese characters in slug
- removed unused helpers/menu.php file (caused problems with Advanced Module Manager)

## 2.0.2

### Changed

- sluggify now uses Joomlas string conversion

### Fixed

- fixed bug with changing the capitalization in tags
- fixed bug with capitalized letters in Gravatar email addresses
- fixed bug with slug input
- fixed bug with tags import
- fixed typo in english language file
- fix to K2 import
- fixes to ZOO import
- template positions are now being saved on type slug rename
- fixes to xml class
- fixed bug with saving utf-8 encoded category-, type slugs
- fixed bug with '/' being JPATH_ROOT
- fixed problem with xpath and xmllib version
- fixed path to zoo documentation in toolbar help
- fixed path to tmp dir during app installation
- fixed type name in relateditems element "choose item dialog"

## 2.0.1

### Added

- googlemaps element now renders item layout in popup
- added category module
- country element is now searchable
- added some exceptions to the application installer

### Changed

- updated item module to version 2.0.1
- updated search plugin to version 2.0.1
- changes to the applications installer, now accepts different archive types

### Fixed

- fixed bug with Twitter Authenticate and SEF turned on
- fixed translation of country element
- fixed language files to include googlemaps element
- fixed minor css issue with category columns
- fixed categories teaser description in cookbook app
- fixed bug with rss feed item order
- fixed bug with comment cookie scope
- fixed minor CSS issue with comments in documentation app
- fixed filtering bug for relateditems element
- fixed bug with utf-8 encoding of the default.js file
- fixed bug with saving utf-8 encoded item-, category-, type slugs
- fixed bug with breadcrumbs (direct link to item)
- fixed bug with alpha index

## 2.0.0

### Changed

- changed error message for position.config not writable

### Fixed

- fixed bug with gifs in imagethumbnail
- fixed bug with removing last tag from item
- fixed bugs with editing tags on item edit in browsers with webkit engine

## 2.0.0 RC 2

### Added

- added check script to installation process
- relateditems ordered by default are now ordered as ordered in item view

### Changed

- updated language files

### Fixed

- fixed breadcrumbs in item view
- fixed bug with comment login cookie
- fixed bug with exception class name
- fixed comment filters in backend
- fixed bug with special character in app name
- fixed capital characters in position names
- fixed option parameter in element links

## 2.0.0 RC

### Fixed

- fixed relateditem.js
- try to set timelimit in installer

## 2.0.0 BETA 4

### Fixed

- fixed bug with item copy, if no item is selected
- fixed bug with install script
- fixed bug with image element link
- fixed bug with related items import
- fixed bug with tag import
- special characters in textarea and text control
- fixed relateditems delete

## 2.0.0 BETA 3

### Added

- added update functionality to ZOO installer

### Changed

- updated addthis element
- changed editor handling in ZOO administration panel
- if menuitem is direct link to item, the category won't be added to breadcrump

### Fixed

- fixed "add options" bug in edit elements view
- fixed parameter settings in ZOO administration panel
- fixed pagination on frontpage layout in SEO mode
- fixed link in item module
- fixed link in image element
- fixed generated link through menu_item parameter in module
- fixed links to ZOO in rss feed
- moved applications group field from params to database field

## 2.0.0 BETA 2

### Added

- added support for unicode characters (cyrillic, arabic, ...) in slug
- added application wide use of tinyMCE editors in Joomla administration panel
- added comment author caching

### Changed
- merged commentauthor classes into single file

### Fixed

- PHP 4 warning now functions as expected
- use of htmlentities before output to text and textarea fields
- vertical tabs are being filtered from CData areas in xml
- image element: added file exist check
- bugfixes to import/export
- fixed some tooltips in Joomla administration panel
- bugfixes to install application
- bugfixes to comments
- bugfix in type delete

## 2.0.0 BETA

### Added

- Initial Release
