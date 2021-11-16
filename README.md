Read me
==============

lbcg-binco-card-generator
==============

The package is including:
- Grunt - *JavaScript Task Runner (http://gruntjs.com)*
- SASS - *CSS Preprocessor (http://sass-lang.com)*
- PostCss 0.7.1 - (https://twitter.com/postcss)*
- Autoprefixer 6.1.1 - (https://twitter.com/autoprefixer)*

Installation
--------------

Before usage you must have node.js with npm (node package manager) installed.

First install grunt as a console service. Run

	npm install -g grunt-cli

This will install grunt in your system environment.
You can read more here http://gruntjs.com/getting-started

After grunt instalation change to your project dir where package.json is located. And run the following command.

    npm install

This will install grunt and node modules, which are used for SASS compilation into CSS 
To run autocompilation while development you would do this:

	grunt watch

To build once for production you may use

	grunt build

Enjoy! :)