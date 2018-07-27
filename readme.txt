=== File Renaming on Upload ===
Contributors: karzin
Tags: file rename, upload, renaming, file, rename, characters, accents
Requires at least: 4.0.0
Tested up to: 4.9
Requires PHP: 5.3
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=BAC8PT82YMTJL&lc=US&item_name=File%20Renaming%20on%20Upload&item_number=file%2drenaming%2don%2dupload&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Stable tag: 2.2.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Fixes file uploads with accents and special characters by renaming them. It also improves your SEO.

== Description ==

Have you ever had any problems uploading files with accents and some special characters to WordPress? Probably the answer is yes.

This plugin will help you fix this problem by renaming these files on upload. It will either convert these problematic characters or remove them.

Besides that, it can improve your SEO adding some relevant info to your filename, like your domain name or the post title your file is attached on.

Increase your control over your file names

== Premium version ==
Do you like the free version of this plugin? Imagine what the <strong>[Premium](https://wpcodefactory.com/item/file-renaming-on-upload-wordpress-plugin/ "File Renaming on Upload - Premium")</strong> version can do for you!

[Check it out](https://wpcodefactory.com/item/file-renaming-on-upload-wordpress-plugin/ "File Renaming on Upload - Premium")

**Take a look at some of its features:**

* Edit filenames and permalinks manually
* Update old media
* Autofill ALT tag
* Custom field rule
* New rules

== Frequently Asked Questions ==
= What are the available options provided by this plugin? =

**For now, you can choose these options:**

* **Add Site url:** Inserts "yoursite.com" at the beggining of the file name. Ex: yoursite.com_filename.jpg. It is good for your SEO

* **Post title:** If you are on a post edit page called "Spiderman will leave Marvel" and you upload a jpg it will be called spiderman-will-leave-marvel-my-file.jpg. This option allows you to replace filename by post title or add the post title.

* **Remove characters:** Remove any characters you want from filename

* **Datetime:** You can add or replace filename by Datetime in any format you want

* **Lowercase:** Converts all characters to lowercase

* **Remove accents**

* **Update permalink:** When the filename is changed, you can also change its permalink if you want

= How does this plugin work? =
It renames files on upload using the available rules. More specifically, it uses some filters provided by WordPress to handle file name sanitizing, like **sanitize_file_name**, **sanitize_file_name_chars** or actions like **add_attachment**

= What are rules? =
Rules are options to control how your filename will be. Rules are enabled on the rules tab and have to be placed on the filename scructure option

= What is filename structure option for? =
It's the option where you can put your rules or any other characters you want to set how your filename will be

= Are there any hooks available?
* **frou_sanitize_file_name** Creates custom rules. Take a look on (Can I create a custom rule?)
* **frou_ignored_extensions** Ignores extensions. Take a look on (How to ignore extensions programmatically?)

= How to ignore extensions programmatically?
You can use the **'frou_ignored_extensions'** filter to ignore extensions programmatically.
For example, if you'd like to ignore txt, js and zip extensions:

`add_filter( 'frou_ignored_extensions', function ( $extensions ) {
	$extensions = array_merge( $extensions, array(
		'txt',
		'js',
		'zip',
	) );
	return $extensions;
} );`

= Can I create a custom rule?
Yes. It's easy.

First, you have to create a custom rule in the **filename structure** option using curly braces, like **{my_custom_rule}**. You just have to write it, in any position you want.

Now you can use the filter **frou_sanitize_file_name** to create a custom function. For example, if you want to put the user id it would be something like this:

`add_filter( 'frou_sanitize_file_name', function($filename_infs){
	$filename_infs['structure']['translation']['my_custom_rule'] = get_current_user_id();
	return $filename_infs;
}, 20 );`

= How can i contribute with code development? =
Head over to the [File Renaming on Upload plugin GitHub Repository](https://github.com/pablo-sg-pacheco/file-renaming-on-upload) to find out how you can pitch in

== Installation ==

1. Upload the entire 'file-renaming-on-upload' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Start by visiting plugin settings at Settings > File Renaming

== Screenshots ==

1. An exemple of a sanitized filename in Media Library
2. On general settings, setup how your filename will be, using the filename structure option where you have some rules at your disposal
3. Setup how the rules will work on your filename

== Changelog ==

= 2.2.9 - 2018/07/27 =
* Check if rules exist before convert filename
* Add more names to ignore filenames option regarding visual composer

= 2.2.8 - 2018/04/20 =
* Check if permalink option is enabled on add_attachment function

= 2.2.7 - 2018/03/30 =
* Make it compatible with Nextgen gallery plugin

= 2.2.6 - 2018/03/26 =
* Update translation file
* Add persian translation
* Config auto deploy with travis
* Add wp.org assets on github

= 2.2.5 - 2018/01/30 =
* Fix empty function

= 2.2.4 - 2018/01/26 =
* Add translation to some missing strings
* Add option to truncate filename

= 2.2.3 - 2017/11/27 =
* Fix notice checking
* Replace "install_plugins" permission by "edit_users"
* Tested up to WordPress 4.9

= 2.2.2 =
* Update pot file
* Add strings to translation

= 2.2.1 =
* Set transient on update

= 2.2.0 =
* Open tab if hash is present
* Improve settings api
* Create notice asking for review
* Create notice talking about the pro version
* Improve plugin's description

= 2.1.9 =
* Create a filter to get the parent post id (frou_parent_post_id)

= 2.1.8 =
* Create a filter to ignore filename extensions (frou_ignored_extensions)

= 2.1.7 =
* Improve function to get post title, even with unsaved posts

= 2.1.6 =
* Add new filter 'frou_admin_sections' to filter admin sections
* Improve settings api
* Update tested up to
* Restrict settings to administrators only

= 2.1.5 =
* Add new option to convert characters to dash

= 2.1.4 =
* Fix conflict on WeDevs settings API libraries

= 2.1.3 =
* Update Settings API class

= 2.1.2 =
* Start the plugin after plugins_loaded hook
* Fix github link
* Improve readme

= 2.1.1 =
* Add new option to ignore filenames
* Fix conflict with sitemap.xml generated by All in one SEO pack

= 2.1.0 =
* Add new option to remove non ASCII characters

= 2.0.8 =
* Solve more conflicts with github updater plugin

= 2.0.7 =
* Fix datetime option fatal error on update() boolean

= 2.0.6 =
* Solves more conflicts with github updater plugin

= 2.0.5 =
* Ignores more basenames ('option_page', 'action', 'wpnonce', 'wp_http_referer', 'github_updater_repo', 'github_updater_branch', 'github_updater_api', 'github_access_token', 'bitbucket_username', 'bitbucket_password', 'gitlab_access_token', 'submit', 'db_version', 'github_updater_install_repo') when there is no extension provided to solve more conflicts with github-updater plugin

= 2.0.4 =
* Ignores some basenames ('path', 'scheme', 'host', 'owner', 'repo', 'owner_repo', 'base_uri', 'uri') when there is no extension provided. It solves conflicts with github-updater plugin

= 2.0.3 =
* Improve description
* Add option to ignore renaming for some filename extensions
* Add new screenshot
* Remove portuguese and german translation packs from languages folder

= 2.0.2 =
* Improve Portuguese translation
* Add German translation

= 2.0.1 =
* Fix autoloader bug on linux environments

= 2.0.0 =
* Recreate the plugin with some new options

= 1.3 =
* Fix bug where site url should be home url instead

= 1.2 =
* Added an option to renames files based on post title
* Fixed a bug where some strings were not properly removed from site url

= 1.1 =
* Added an option to remove string parts from url

= 1.0.1 =
* Admin page class renamed

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.2.9 =
* Check if rules exist before convert filename
* Add more names to ignore filenames option regarding visual composer