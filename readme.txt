=== BSK Forms Blacklist ===
Contributors: bannersky
Plugin URI: https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/
Tags: gravity form,blacklist,ip blacklist,invitation code,formidable forms
Requires at least: 4.0
Tested up to: 6.6.2
Stable tag: 3.9

Checks field content and block submitting base on your keywords. Blocking IP, Country is only supported in the Pro version. 

== Description ==

This plugin helps you avoid spam submissions from Gravity Forms, Formidable Forms, WPForms, Contact Form 7 and Forminator.

This is the free version and you can set it up to use a blacklist. If the field value contains / same as an item / keyword in the applied blacklist, the form submission will be blocked. It's easy to use, you just need to create a blacklist, enable settings for the form and apply the blacklist to the form fields. You can set one or more fields to validate. Validation messages can be customized for the form.

The Pro version described here: https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/ can also be used as a whitelist, IP address, email or invitation code to validate visitor input. Support for blocking email domains, giving you the option to allow or block submissions instead of always allowing submissions. Supports blocking IP addresses by country or allowing only IP addresses from a certain country.

== Installation ==

It’s a standard WordPress plugin and you may install it from WordPress’ plugin Dashboard. After activate the plugin then you can create blacklist or whitelist or ip address list or email list.

== Frequently Asked Questions ==

Please visit <a href="https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/">https://www.bannersky.com/gravity-forms-blacklist-and-custom-validation/</a> for documents or support.

== Short Description ==

This plugin helps you avoid spam submissions from Gravity Forms, Formidable Forms, WPForms, Contact Form 7 and Forminator. It checks the form field content and block submitting base on your keywords. Blocking IP, Country is only supported in the Pro version. 

== Screenshots ==

1. Backend menu
2. Interface of creating blacklist and add items ( keywords )
3. Plugin setting page to choose the form plugin to be supported
4. Gravity Forms form setting page
5. Apply blacklist to Gravity Forms form field
6. Formidable Forms form setting page
7. Apply blacklist to Formidable Forms form field
8. Blocked when submit form

== Changelog ==

3.9

* ( Pro Version )Added: support Block IP ranges such as 97.157.155.*, 97.157.*, 97.*

* ( Pro Version )Added: new tag [ITEM_VALUE] to show blocked item( keyword ) in validation message.

* ( Pro Version )Fixed: the issue of cannot block word with symbols.

* ( Pro Version )Fixed: remove the html error message when add IP list

* Fixed: a Cross Site Scripting (XSS) vulnerability in backend.

* Compatible with Gravity Forms 2.8.17

* Compatible with Formidable Forms 6.14

* Compatible with WPForms 1.9.0.4

* Compatible with Forminator 1.35.0

* Compatible with Contact Form 7 5.9.8

* Compatible with WordPress 6.6.2

3.8.1

* Fixed: vulnerability of Cross Site Scripting (XSS).

* Compatible with WordPress 6.6

3.8

* Added: support using different validation message for invalid code and used code for invitation list ( pro )

* Added: sorting item by ID, Extra.

* Added: warning message when activate plugin

* Fixed: warnings under PHP 8.2

* Compatible with Gravity Forms 2.8.8

* Compatible with Formidable Forms 6.9

* Compatible with WPForms 1.8.8.2

* Compatible with Forminator 1.29.3

* Compatible with Contact Form 7 5.9.3

* Compatible with WordPress 6.5.2


3.7

* Fixed: vulnerability that allows administrators to run Cross-Site Scripting attacks

* Fixed: warning message on Forminator blacklist setting page

* Fixed: error message cannot be located to related Forminator form field

* Compatible with Gravity Forms 2.7.17

* Compatible with Formidable Forms 6.5.4

* Compatible with WP Forms 1.8.4.1

* Compatible with Forminator 1.28.0

* Compatible with Contact Form 7 5.8.3

* Compatible with WordPress 6.4.1

3.6.3

* Fixed: vulnerability in SQL on blakclist / white list / Email list / IP list / invitation code list page

* Compatible with Gravity Forms 2.7.4

* Compatible with Formidable Forms 6.2.3

* Compatible with WP Forms 1.8.1.2

* Compatible with Forminator 1.23.3

* Compatible with Contact Form 7 5.7.5.1

* Compatible with WordPress 6.2

3.6.2

* Fixed: the bug of skipping validation when html / hidden field exist in form in Formidable

* Compatible with WordPress 6.1.1

3.6.1

* Updated: still display blocked data if the related list / item is deleted

* Updated: improve backend interface

* Compatible with WordPress 6.0.2

3.6

* Added: Supporting Forminator

* Added: special tags for item / keyword

* Updated: improve the interface of WPForms field mapping page

* Updated: display entry ID / link in blocked form data list

* Fixed: PHP warning message on Formidable Forms setting page

* Compatible with WordPress 6.0

3.5

* Added: Supporting Contact Form 7

* Added: Hits count for whitelist items

* Fixed: Invalid URL on Formidable Forms setting page and Gravity Forms setting page

* Fixed: Invalid entry url on blocked data interface

* Fixed: PHP error when save blocked data if only Formidable Forms or WPForms installed

* Fixed: wrong confirmation page for Formidable Forms

* Fixed: missing database table field

* Fixed: PHP warning message

* Compatible with WordPress 5.9.3

* Compatible with all form plugin's latest version

3.4

* Added: invitation code list

* Fixed: some codes cause JavaScript error if WPForms not activated

* Fixed: the hit count for item cannot be shown

* Fixed: typo

* Compatible with WordPress 5.8.2

* Compatible with Gravity Forms 2.5.15

* Compatible with Formidable Forms 5.0.13 and Formidable Forms Pro 5.0.13

* Compatible with WPForms Lite 1.7.1.1 and WPForms ( pro ) 1.6.9

3.2

* Support WPForms

* Fixed the trouble of new notification cannot be listed out on form setting page in Gravity Forms 2.5.7

* Compatible with WordPress 5.8

3.1

* Fixed the trouble of cannot apply list on Gravity Forms 2.5.x

* Remove PHP waring when Contact Form 7 installed

* Compatible with Formidable Forms 4.11 version

* Compatible with WordPress' latest version

3.0

* Supports Formidable forms.

* Improved backend interface.

* Support duplicate keywords / items checking when import from CSV.

* Support clear all keywords / items in a list.

* Fixed the bug of IP Geolocation API Server doesn't work when choose to ip-api.com.

* Compatible with WordPress 5.7.1

2.9

* Support fields with conditional logic enabled. 

* Fixed the bug of validation message cannot be shown for IP list.

* Fixed the issue of default settings not work for new form.

* Fixed the error of duplicated setting fields displayed for advanced fields.

* Compatible with Wordpress 5.5.3

2.8

* Divide the old plugin to two plugins and this one only include blacklist, whitelist, Email list and IP list      

* Support go to specific Gravity Forms confirmation when keyword hit

* Support notifying administrators( emails ) when keyword hit

* Support form more option on Gravity Forms setting page

* Fixed the bug of validation message cannot be overwritten for field

* Compatible with Gravity Forms coming 2.5 version

* Compatible with Wordpress 5.5

2.7

* Add the feature of supporting block / whitelist by country in IP list.

* Fixed the bug of block message not right for Name, Address, Checkbox, Radio and Time field.

* Fixed the bug of some IP can not be inserted.

* Fixed the bug of always block submission even the list removed in some case.

* Compatible with Wordpress 5.4.1.

2.6

* Adjust menu function to improve backend interface

* Add new feature of saving blocked entry and view blocked entries

* Add setting page to switch the new saving blocked entry feature

* Fixed a PHP warning when add custom validation list

* Compatible with Wordpress 5.4

2.5

* Fixed the bug of wrong active menu after delete item for IP list

* Backend interface improving

* Improve item( keyword ) match algorithm

* Support enhanced checking for multiple words item such as: i-n-ve-s-t opportunity

* Support check ANY or ALL item(s) in a list for Blacklist and White list

* Block full IP ranges, for example: 45.91.94.* and 45.91.94.1 - 45.91.94.123

* Compatible with Wordpress 5.3

2.4

* Add hits counter for keywords

* Compatible with Wordpress 5.2.4

2.3.2

* Support skip specific notification

* Compatible with Wordpress 5.2.2

2.3.1

* Support for new free version

* Fix typo

* Update links

2.3

* Fix PHP warning

* Support IP list, custom validation

* Support defining validation message for form 

* Support defining validation message for field

* Move apply field to field editing page

* Enhanced keyword checking, now k_e_w_o_r_d is taken as keyword

* Improve admin interface

* Compatible with PHP 7

* Compatible with WordPress 5.2.1

2.2

* Fix small bug

* Add screen option to set lists / items per page

* Improve admin interface

* Compatible with WordPress 4.9.7

2.1

* Compatible with Gravity Forms 2.3

* Fix activation error on some hosting

* Remove PHP warnings

* Compatible with WordPress 4.9.6

2.0

* Fixed to use on instead of deprecated function live

* Improve to compatible with Pro version 2.0

* Improve to compatible with WordPress 4.9.4

1.0 

* First version.
