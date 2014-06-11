=== WordPress Word Count and Limit ===
Contributors: jojaba
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5PXUPNR78J2YW&lc=FR&item_name=Jojaba&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: words, characters, count, limit
Requires at least: 3.0.1
Tested up to: 3.9
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Counts dynamically the characters and words in edit post window and limit the characters count if needed for one or more user roles.

== Description ==

This plugin replaces the word count info in bottom of the edit post window by the characters/words count (on the fly). Additionnaly, you can limit the characters count for defined user roles.

Here's the list of the settings (see screenshots for further infos):

* Enable or disable character count limit.
* Max characters count setting
* Warning characters count setting (the count before max count when the warning is fired)
* Output Format. You can define how you would like to see the output displayed using different placeholders : `#input` (the character count that has been typed), `#max` (the max characters allowed), `#left` (the characters count left), `#words` (The number of words).
* Choose what user role should be limited. Default set to contributor role.
* Choose the post types that should be limited. Default set to post.
* Set customised messages for warning or for contributor submission.

Availabe languages : english and french.

== Installation ==

1. Upload `word-count-limit` directory to the `/wp-content/plugins/` directory of your Wordpress installation
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Could I enable the Characters count limit for multiple user roles? =

Yes. You just have to check the right checkboxes in the plugin options screen. Default limited role is *contributor* but you can also limit other roles.

= What happens when user disable JavaScript? =

The native WP word count will be displayed. The characters limit will still be functionnal, but no warning message will be displayed before submitting. After clicking on the button to submit the post and if the characters count exceed the max characters count set in the options, the submission will be refused.

= Can I add html tags in the output format? =

Yes, all html tags enabled in your WordPress installation are allowed (see in `/wp-includes/kses.php` file to get a list of these tags).
So this format : `&lt;b&gt;#input&lt;/b&gt; characters | &lt;b&gt;#words&lt;/b&gt; words`
… will output something like that : **80** characters | **15** words (the numbers are bold).

== Screenshots ==

1. How to find the WordPress Word Count and Limit options
2. The WordPress Word Count and Limit options page
3. The output for the format : `#input` characters | `#words` words
4. The output for the format : `#input`/`#max` characters, `#left` left | `#words` words (when under the limit)
5. The output for the format : `#input`/`#max` characters, `#left` left | `#words` words (when over the limit)

== Changelog ==

= 1.1 =
* Improved the characters and word count system
* Adding new options : impacted post types and customised messages.
* Fixing typos in language files

= 1.0 =
* First release. Thanks for your feedback!
