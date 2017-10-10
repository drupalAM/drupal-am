# Theme settings

If you go to the /admin/appearance/settings/druppio_small_business
and scroll down, you can see theme settings. 
There are two vertical tabs: Style and Blocks. 
In Style settings you can enable custom CSS. 
Enable this option if you want to add custom CSS without modifying theme. 
When you enable custom CSS, a new text area will appear below checkbox, 
where you can enter your CSS. 
Entire content of this text area will be saved in the following 
file: /sites/default/files/druppio-smallbusiness-custom.css after you click on the 
Save configuration button. 
If you disable this option you will not loose any of your existing custom CSS,
but it will not be applied to pages. 
Please don't delete or rename druppio-smallbusiness-custom.css file.


# Building themes using Gulp, Sass and Browsersync

Build systems provides a great way to automate common tasks that theme 
developers perform on a daily basis. 
One of the most prevalent tasks that developers do is refresh their 
browser/s after they've made some changes or use one of the 
available preprocessors to compile their CSS files. 
Build system provided alongside this theme is 
based on a build system called Gulp. 
Gulp is responsible for starting up the Browsersync with it's capabilities 
to live reload any styles compiled by Sass compiler. 
Build system is also capable of building a development versions of CSS files,
including sourcemaps and building a production versions outputing CSS files
with vendor prefixed rules for wider css3 features support.


## Gulp (http://gulpjs.com/)

The official website says it's the streaming build system but it could
also be referred to as a task runner. 
By tasks we can assume anything that can be done manually
and is repeatable by nature. 
Some of the most common tasks done when building a Drupal theme involves:

- compiling Sass into CSS,
- minifying, concatenating CSS and JavaScript
- optimizing images etc.

## Sass (http://sass-lang.com/)

Sass is a meta-language on top of CSS that’s used to describe the style of
a document cleanly and structurally, with more power than flat CSS allows. 
Sass both provides a simpler, more elegant syntax for CSS and implements
various features that are useful for creating manageable stylesheets.

## Browsersync (https://www.browsersync.io/)

With each web page, device and browser, testing time grows exponentially. 
From live reloads to URL pushing, form replication to click mirroring, 
Browsersync cuts out repetitive manual tasks. 
It’s like an extra pair of hands. 
Customise an array of sync settings from the UI or command line to 
create a personalised test environment. 
Need more control? Browsersync is easily integrated with your web platform, 
build tools, and other Node.js projects.


# 1. Requirements

1) System capable of running Node.js (OS X, Linux, OpenBSD, Windows ...)
2) Node.js


# 2. Installing Node.js

Depending on the environment Node.js can be installed from distribution 
repositiories if used on Unix or Unix like operating systems, 
from packaged binary installers for Microsoft Windows systems or 
compiled from the source files.

To see more and learn how to install Node.js on your environment 
please refer to: https://nodejs.org/en/download/package-manager/.


# 3. Installing Dependencies

Build system uses two separate utilities for managing theme dependencies. 
*NPM* (Node.js Package Manager) for build system itself, 
and *Bower* package manager for managing external Sass dependencies. 
By default there are couple of Sass libraries already preinstalled
with your theme and these include:

- normalize-libsass (libsass version of normalize.css
(https://necolas.github.io/normalize.css/)),
- compass-mixins* 
(ruby compass ported to libsass (https://github.com/Igosuki/compass-mixins)),
- breakpoint-sass 
(working with media queries (https://github.com/at-import/breakpoint)),


__*__ not all features of ruby compass are supported, 
please see the library documation for further information

## 3.1. NPM

### 3.1.1. Installing NPM

- NPM comes preinstalled with Node.js

### 3.1.2 Installing build system dependecies

To install Node.js dependecies please issue the 
following command at the themes root folder:

	npm install

## 3.2. Bower

### 3.2.1. Installing Bower

Bower can be installed using NPM by issuing the following command:

	npm install bower -g

### 3.2.2. Installing Sass Dependecies

To install Sass dependecies please issue the following 
command at the themes root folder:

	bower install


# 4.Configuration

Depending on the development preferences theme development 
can be done in one of the two following ways:
- local development,
- development against a live site

Build system provided with your theme comes 
with two predefined configuration files:

- config-local.js and
- config-live.js.

To start developing in a local environment please copy the provided 
**config-local.js** to **config.js**, or to start working with live 
environment copy **config-live.js** to **config.js**.

Please note that the live development is not modifying any of the 
files deployed to a live server but instead it is only *proxying* 
the calls through Browsersync.


# 5. Running the default task

Build systems comes with a default task which bootstraps the 
Browsersync to work in a *proxy* mode and sets up the Sass 
compiler to compile developer versions of CSS files after any 
change is made to original source Sass files.

To start the build system with a default task please 
enter the following command:

	gulp


# 6. Compiling Sass files

Besides the default task build system comes with two more 
additional tasks used for compiling Sass source files to CSS. 
Task for compiling a development version of CSS 
(also used in building CSS in default task) and task for compiling 
a production version of CSS files. 
Depending of needs tasks can be run by issuing the 
following commands at the command prompt:

	gulp sass:dev

Builds a development version of CSS files including sourcemaps for
easier debugging of styles during development process.


	gulp sass:prod

Builds a production version of CSS files without sourcmaps and 
is vendor prefixed using autoprefixer for wider support of css3 features.

# 7. Fonts

Fonts will be added by gulp to fonts folder first time you run gulp command