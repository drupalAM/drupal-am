
# INTRODUCTION
The Link icon module is a simple link field formatter to create icon classes
based on a predefined set of link titles.

Link icon is an icon-agnostic formatter, meaning it doesn't care for whatever
icon you use. Any icon will simply work. It doesn't hard-code icon names, nor
creates a new field type, nor includes icons.

Drupal supports unlimited values, the limitation is your available icon fonts.

Linkicon is an entity-based field formatter, like link.module, so once you are
done with the Configuration, be sure to visit the assigned entity URL, e.g.:
node/1/edit, or user/1/edit, or inform your members/editors, to add the actual
icons at those URLs.


## REQUIREMENTS
- link.module (in core) should be enabled.


## INSTALLATION
Install the module as usual, more info can be found on:

[Installing Drupal 8 Modules](https://drupal.org/node/1897420)


## FEATURES
- Predefine allowed titles. Adding or removing more icons is as easy as adding
  or removing another line of key|value pairs to the allowed titles.
- Optional icon API, and fontawesome modules integration.
- Optional simple stylings: pure CSS tooltip, square, rounded and base colors,
  or disable it from outputting CSS, if you care to DIY. It's a starter anyway.
- Options to display or hide text, feed icon fonts CSS file, and a few other.
- The tooltip and title may be tokenized.
- And of course, link module own virtues as a field such as effortless
  integration with any fieldable entity.


## EXTENDING
- Use Views to place it as blocks, per node or other entity (NID or UID).
- For sitewide social links, an idea is to put the links into admin account
  field, and use Views to make them as sitewide block filtered by admin UID.
- This module doesn't support social share, although with tokenized URL may help
  simple share, it is recommended to use a specialized module for such purpose.


## USAGE EXAMPLES
- To create member social links for community-driven sites where you want to
  control what links are allowed,
- To create a set of supported social links for team members so they can put
  their own links without breaking the design, nor your intervention on the fly,
- Static iconized links, e.g.: View website, Try now, Buy now, Demo or other
  Hello there links.


## CONFIGURATION
Module-wide configurations are located within the *Configuration* options under
*User Interface, Linkicon*.

Two important places to visit in the correct orders:

  * Manage fields
  * Manage display

Then follow:

- Enable this module and its dependency, link.module

- At `/admin/config/people/accounts/fields` or Content types, hit
  **Manage fields**.
  Create a multi-value link field, make sure to choose **Predefined title** and
  input your key|value pairs of titles where key is the icon name (without
  prefix), and value title.
  If you have an icon named **icon-facebook** or **fa-facebook**, write, e.g.:

  `facebook|Visit my Facebook page
  google-plus|Google+`

  Sample with token:

  `facebook|Facebook|[node:title]`

  The first key translates to icon name **icon-facebook**
  The second to the title.
  The third to tooltip. If not provided, tooltip will use the second if enabled.

  Avoid hardcoding icon name **prefixes** here. The prefix is defined at Display
  formatter so that you are not stuck in database when the icon vendor change
  prefixes from **icon-** to just **fa-**, etc. Or you change icon sets.
  Make sure the icon name is available at your icon set.

- Download icon fonts from http://fontello.com or http://icomoon.io/app/.
  Place it somewhere (e.g.: sites/all/libraries/fontawesome), or use icon API
  import, and reference it either via this module Display formatter, or your
  theme, or loaded automatically if using fontawesome.module or icon API.

- Define path to icon font library at admin/config/user-interface/linkicon

- At `/admin/config/people/accounts/fields` or Content types, hit
  **Manage display**.

  Under **Format** of the active view mode, choose
  **Link icon, based on title**.

- Click the **Configure** icon to have some extra options. There is option to
  hide text so to display icon only, option to disable module from outputting
  CSS, if you want total DIY on theming, and a few other.

- The configuration is ready, now visit the configured entity edit page, e.g.:
  node/1/edit, or user/1/edit, and add the links accordingly.


## SETTINGS

### Path to icon font CSS file
Provide a valid path to a theme icon font CSS file, such as:

`/libraries/fontello/css/fontello.css`


## USAGE
Link icon usage can be found within entity configurations under Manage Display
including:

* **Content Types**:

  `/admin/structure/types/manage/ENTITY_MACHINE_NAME/display`

* **Custom Block Types**:

  `/admin/structure/block/block-content/manage/ENTITY_MACHINE_NAME/display`

* **Paragraphs**:

  `/admin/structure/paragraphs_type/ENTITY_MACHINE_NAME/display`

Within each entity display configuration, any Link field type will have a
*Link icon, based on Title* display format.


## RELATED MODULES

* [Follow](https://drupal.org/project/follow)

  It adds sitewide and per user links that link to various social networking
  sites. The links reside in two blocks.

* [Link favicon formatter](https://drupal.org/project/link_favicon_formatter)

  Adds a formatter to the link field that adds the host favicon to the front of
  the link.

* [Advanced link](https://drupal.org/project/advanced_link)

  Option to allow users select url title from predefined list of values, but
  nothing to do with icons. Linkicon owes credit to it for the idea on turning
  texfield into select box within the allowed titles, however since Advanced
  link builds another widget type, which Linkicon disagrees, Linkicon can not
  extend nor depend on it. Linkicon chooses to alter the link field widget
  instead.


## RECOMMENDED MODULES

Icon providers based on icon API module.

* [Icon](https://drupal.org/project/icon)
* [Fontawesome](https://drupal.org/project/fontawesome)
* [Iconmoon](https://drupal.org/project/icomoon)


## SAMPLE OPTIONS
Based on Fontawesome via fontello.com.
If not via fontello.com, Fontawesome may have class google-plus, not gplus.
Basically, adjust the icon names accordingly.

````
facebook|Facebook
twitter|Twitter
flickr|Flickr
github|Github
gplus|Google +
html5|HTML5
linkedin|Linkedin
pinterest|Pinterest
vimeo|Vimeo
youtube|Youtube
delicious|Delicious
````

## KNOWN ISSUES
If using Views Fields under **Format**, be sure to check **Use field template**
under **Style settings**, otherwise empty result.

## FontAwesome 5+:
[Upgrading from v4](https://fontawesome.com/how-to-use/on-the-web/setup/upgrading-from-version-4)

Basically to make FontAwesome 5+ and SVG with JS work with Linkicon:

1. Empty the CSS library import `/admin/config/user-interface/linkicon`, since
   it is all about JS. Unless Webfonts with CSS is preferred.
2. A new option is added to add additional classes via UI, be sure to fill it
   out with: `fab far fas` alike, while keeping the old fa intact for the Icon
   prefix class option.

## MAINTAINERS

* [Gaus Surahman](https://drupal.org/user/159062)
* [Contributors](https://www.drupal.org/node/2208459/committers)
* The CHANGELOG.txt for more helpful souls with suggestions, and bug reports.

This modules owes credits to Advanced link for the idea of Link title using
select box. Due credits to them.


## READ MORE
[Link Icon at Drupal](https://www.drupal.org/docs/8/modules/link-icon)
[Link Icon at Git](https://git.drupalcode.org/project/linkicon/blob/8.x-1.x/README.md)
