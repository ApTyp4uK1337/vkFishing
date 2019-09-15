/*!
 * modernizr v3.6.0
 * Build https://modernizr.com/download?-cssanimations-csstransforms3d-csstransitions-touchevents-domprefixes-prefixes-setclasses-testallprops-testprop-teststyles-dontmin
 *
 * Copyright (c)
 *  Faruk Ates
 *  Paul Irish
 *  Alex Sexton
 *  Ryan Seddon
 *  Patrick Kettner
 *  Stu Cox
 *  Richard Herrera

 * MIT License
 */

/*
 * Modernizr tests which native CSS3 and HTML5 features are available in the
 * current UA and makes the results available to you in two ways: as properties on
 * a global `Modernizr` object, and as classes on the `<html>` element. This
 * information allows you to progressively enhance your pages with a granular level
 * of control over the experience.
*/

;(function(window, document, undefined){
	var classes = [];

	var tests = [];

	/**
	 *
	 * ModernizrProto is the constructor for Modernizr
	 *
	 * @class
	 * @access public
	 */

	var ModernizrProto = {
		// The current version, dummy
		_version: '3.6.0',

		// Any settings that don't work as separate modules
		// can go in here as configuration.
		_config: {
			'classPrefix': '',
			'enableClasses': true,
			'enableJSClass': true,
			'usePrefixes': true
		},

		// Queue of tests
		_q: [],

		// Stub these for people who are listening
		on: function(test, cb) {
			// I don't really think people should do this, but we can
			// safe guard it a bit.
			// -- NOTE:: this gets WAY overridden in src/addTest for actual async tests.
			// This is in case people listen to synchronous tests. I would leave it out,
			// but the code to *disallow* sync tests in the real version of this
			// function is actually larger than this.
			var self = this;
			setTimeout( function() {
				cb( self[test] );
			}, 0);
		},

		addTest: function(name, fn, options) {
			tests.push( {name: name, fn: fn, options: options} );
		},

		addAsyncTest: function(fn) {
			tests.push( {name: null, fn: fn} );
		}
	};

	/**
	 * List of property values to set for css tests. See ticket #21
	 * http://git.io/vUGl4
	 *
	 * @memberof Modernizr
	 * @name Modernizr._prefixes
	 * @optionName Modernizr._prefixes
	 * @optionProp prefixes
	 * @access public
	 * @example
	 *
	 * Modernizr._prefixes is the internal list of prefixes that we test against
	 * inside of things like [prefixed](#modernizr-prefixed) and [prefixedCSS](#-code-modernizr-prefixedcss). It is simply
	 * an array of kebab-case vendor prefixes you can use within your code.
	 *
	 * Some common use cases include
	 *
	 * Generating all possible prefixed version of a CSS property
	 * ```js
	 * var rule = Modernizr._prefixes.join('transform: rotate(20deg); ');
	 *
	 * rule === 'transform: rotate(20deg); webkit-transform: rotate(20deg); moz-transform: rotate(20deg); o-transform: rotate(20deg); ms-transform: rotate(20deg);'
	 * ```
	 *
	 * Generating all possible prefixed version of a CSS value
	 * ```js
	 * rule = 'display:' +  Modernizr._prefixes.join('flex; display:') + 'flex';
	 *
	 * rule === 'display:flex; display:-webkit-flex; display:-moz-flex; display:-o-flex; display:-ms-flex; display:flex'
	 * ```
	 */

	// we use ['',''] rather than an empty array in order to allow a pattern of .`join()`ing prefixes to test
	// values in feature detects to continue to work
	var prefixes = ( ModernizrProto._config.usePrefixes ? ' -webkit- -moz- -o- -ms- '.split( ' ' ) : ['',''] );

	// expose these for the plugin API. Look in the source for how to join() them against your input
	ModernizrProto._prefixes = prefixes;

	// Fake some of Object.create so we can force non test results to be non "own" properties.
	var Modernizr = function() {};
	Modernizr.prototype = ModernizrProto;

	// Leak modernizr globally when you `require` it rather than force it here.
	// Overwrite name so constructor name is nicer :D
	Modernizr = new Modernizr();

	/**
	 * is returns a boolean if the typeof an obj is exactly type.
	 *
	 * @access private
	 * @function is
	 * @param {*} obj - A thing we want to check the type of
	 * @param {string} type - A string to compare the typeof against
	 * @returns {boolean}
	 */

	function is(obj, type) {
		return typeof obj === type;
	}
	;

	/**
	 * Run through all tests and detect their support in the current UA.
	 *
	 * @access private
	 */

	function testRunner() {
		var featureNames;
		var feature;
		var aliasIdx;
		var result;
		var nameIdx;
		var featureName;
		var featureNameSplit;

		for (var featureIdx in tests) {
			if ( tests.hasOwnProperty( featureIdx ) ) {
				featureNames = [];
				feature = tests[featureIdx];
				// run the test, throw the return value into the Modernizr,
				// then based on that boolean, define an appropriate className
				// and push it into an array of classes we'll join later.
				//
				// If there is no name, it's an 'async' test that is run,
				// but not directly added to the object. That should
				// be done with a post-run addTest call.
				if (feature.name) {
					featureNames.push(feature.name.toLowerCase());

					if (feature.options && feature.options.aliases && feature.options.aliases.length) {
						// Add all the aliases into the names list
						for (aliasIdx = 0; aliasIdx < feature.options.aliases.length; aliasIdx++) {
							featureNames.push( feature.options.aliases[aliasIdx].toLowerCase() );
						}
					}
				}

				// Run the test, or use the raw value if it's not a function
				result = is( feature.fn, 'function' ) ? feature.fn() : feature.fn;

				// Set each of the names on the Modernizr object
				for (nameIdx = 0; nameIdx < featureNames.length; nameIdx++) {
					featureName = featureNames[nameIdx];
					// Support dot properties as sub tests. We don't do checking to make sure
					// that the implied parent tests have been added. You must call them in
					// order (either in the test, or make the parent test a dependency).
					//
					// Cap it to TWO to make the logic simple and because who needs that kind of subtesting
					// hashtag famous last words
					featureNameSplit = featureName.split( '.' );

					if (featureNameSplit.length === 1) {
						Modernizr[featureNameSplit[0]] = result;
					} else {
						// cast to a Boolean, if not one already
						if ( Modernizr[featureNameSplit[0]] && ! ( Modernizr[featureNameSplit[0]] instanceof Boolean ) ) {
							Modernizr[featureNameSplit[0]] = new Boolean( Modernizr[featureNameSplit[0]] );
						}

						Modernizr[featureNameSplit[0]][featureNameSplit[1]] = result;
					}

					classes.push( ( result ? '' : 'no-' ) + featureNameSplit.join( '-' ) );
				}
			}
		}
	};

	/**
	 * docElement is a convenience wrapper to grab the root element of the document
	 *
	 * @access private
	 * @returns {HTMLElement|SVGElement} The root element of the document
	 */

	var docElement = document.documentElement;

	/**
	 * A convenience helper to check if the document we are running in is an SVG document
	 *
	 * @access private
	 * @returns {boolean}
	 */

	var isSVG = docElement.nodeName.toLowerCase() === 'svg';

	/**
	 * setClasses takes an array of class names and adds them to the root element
	 *
	 * @access private
	 * @function setClasses
	 * @param {string[]} classes - Array of class names
	 */

	// Pass in an and array of class names, e.g.:
	//  ['no-webp', 'borderradius', ...]
	function setClasses(classes) {
		var className = docElement.className;
		var classPrefix = Modernizr._config.classPrefix || '';

		if (isSVG) {
			className = className.baseVal;
		}

		// Change `no-js` to `js` (independently of the `enableClasses` option)
		// Handle classPrefix on this too
		if ( Modernizr._config.enableJSClass ) {
			var reJS = new RegExp( '(^|\\s)' + classPrefix + 'no-js(\\s|$)' );
			className = className.replace( reJS, '$1' + classPrefix + 'js$2' );
		}

		if ( Modernizr._config.enableClasses ) {
			// Add the new classes
			className += ' ' + classPrefix + classes.join( ' ' + classPrefix );
			if (isSVG) {
				docElement.className.baseVal = className;
			} else {
				docElement.className = className;
			}
		}

	};

/*!
{
	"name": "CSS Supports",
	"property": "supports",
	"caniuse": "css-featurequeries",
	"tags": ["css"],
	"builderAliases": ["css_supports"],
	"notes": [{
		"name": "W3 Spec",
		"href": "http://dev.w3.org/csswg/css3-conditional/#at-supports"
	},{
		"name": "Related Github Issue",
		"href": "https://github.com/Modernizr/Modernizr/issues/648"
	},{
		"name": "W3 Info",
		"href": "http://dev.w3.org/csswg/css3-conditional/#the-csssupportsrule-interface"
	}]
}
!*/

	var newSyntax = 'CSS' in window && 'supports' in window.CSS;
	var oldSyntax = 'supportsCSS' in window;
	Modernizr.addTest('supports', newSyntax || oldSyntax);


	/**
	 * If the browsers follow the spec, then they would expose vendor-specific styles as:
	 *   elem.style.WebkitBorderRadius
	 * instead of something like the following (which is technically incorrect):
	 *   elem.style.webkitBorderRadius

	 * WebKit ghosts their properties in lowercase but Opera & Moz do not.
	 * Microsoft uses a lowercase `ms` instead of the correct `Ms` in IE8+
	 *   erik.eae.net/archives/2008/03/10/21.48.10/

	 * More here: github.com/Modernizr/Modernizr/issues/issue/21
	 *
	 * @access private
	 * @returns {string} The string representing the vendor-specific style properties
	 */

	var omPrefixes = 'Moz O ms Webkit';

	/**
	 * List of JavaScript DOM values used for tests
	 *
	 * @memberof Modernizr
	 * @name Modernizr._domPrefixes
	 * @optionName Modernizr._domPrefixes
	 * @optionProp domPrefixes
	 * @access public
	 * @example
	 *
	 * Modernizr._domPrefixes is exactly the same as [_prefixes](#modernizr-_prefixes), but rather
	 * than kebab-case properties, all properties are their Capitalized variant
	 *
	 * ```js
	 * Modernizr._domPrefixes === [ "Moz", "O", "ms", "Webkit" ];
	 * ```
	 */

	var domPrefixes = (ModernizrProto._config.usePrefixes ? omPrefixes.toLowerCase().split(' ') : []);
	ModernizrProto._domPrefixes = domPrefixes;

	/**
	 * contains checks to see if a string contains another string
	 *
	 * @access private
	 * @function contains
	 * @param {string} str - The string we want to check for substrings
	 * @param {string} substr - The substring we want to search the first string for
	 * @returns {boolean}
	 */

	function contains(str, substr) {
		return !!~('' + str).indexOf(substr);
	}

	;

	/**
	 * createElement is a convenience wrapper around document.createElement. Since we
	 * use createElement all over the place, this allows for (slightly) smaller code
	 * as well as abstracting away issues with creating elements in contexts other than
	 * HTML documents (e.g. SVG documents).
	 *
	 * @access private
	 * @function createElement
	 * @returns {HTMLElement|SVGElement} An HTML or SVG element
	 */

	function createElement() {
		if (typeof document.createElement !== 'function') {
			// This is the case in IE7, where the type of createElement is "object".
			// For this reason, we cannot call apply() as Object is not a Function.
			return document.createElement(arguments[0]);
		} else if (isSVG) {
			return document.createElementNS.call(document, 'http://www.w3.org/2000/svg', arguments[0]);
		} else {
			return document.createElement.apply(document, arguments);
		}
	}

	;

	/**
	 * cssToDOM takes a kebab-case string and converts it to camelCase
	 * e.g. box-sizing -> boxSizing
	 *
	 * @access private
	 * @function cssToDOM
	 * @param {string} name - String name of kebab-case prop we want to convert
	 * @returns {string} The camelCase version of the supplied name
	 */

	function cssToDOM(name) {
		return name.replace(/([a-z])-([a-z])/g, function(str, m1, m2) {
			return m1 + m2.toUpperCase();
		}).replace(/^-/, '');
	}
	;

	/**
	 * getBody returns the body of a document, or an element that can stand in for
	 * the body if a real body does not exist
	 *
	 * @access private
	 * @function getBody
	 * @returns {HTMLElement|SVGElement} Returns the real body of a document, or an
	 * artificially created element that stands in for the body
	 */

	function getBody() {
		// After page load injecting a fake body doesn't work so check if body exists
		var body = document.body;

		if (!body) {
			// Can't use the real body create a fake one.
			body = createElement(isSVG ? 'svg' : 'body');
			body.fake = true;
		}

		return body;
	}

	;

	/**
	 * injectElementWithStyles injects an element with style element and some CSS rules
	 *
	 * @access private
	 * @function injectElementWithStyles
	 * @param {string} rule - String representing a css rule
	 * @param {function} callback - A function that is used to test the injected element
	 * @param {number} [nodes] - An integer representing the number of additional nodes you want injected
	 * @param {string[]} [testnames] - An array of strings that are used as ids for the additional nodes
	 * @returns {boolean}
	 */

	function injectElementWithStyles(rule, callback, nodes, testnames) {
		var mod = 'modernizr';
		var style;
		var ret;
		var node;
		var docOverflow;
		var div = createElement('div');
		var body = getBody();

		if (parseInt(nodes, 10)) {
			// In order not to give false positives we create a node for each test
			// This also allows the method to scale for unspecified uses
			while (nodes--) {
				node = createElement('div');
				node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
				div.appendChild(node);
			}
		}

		style = createElement('style');
		style.type = 'text/css';
		style.id = 's' + mod;

		// IE6 will false positive on some tests due to the style element inside the test div somehow interfering offsetHeight, so insert it into body or fakebody.
		// Opera will act all quirky when injecting elements in documentElement when page is served as xml, needs fakebody too. #270
		(!body.fake ? div : body).appendChild(style);
		body.appendChild(div);

		if (style.styleSheet) {
			style.styleSheet.cssText = rule;
		} else {
			style.appendChild(document.createTextNode(rule));
		}
		div.id = mod;

		if (body.fake) {
			//avoid crashing IE8, if background image is used
			body.style.background = '';
			//Safari 5.13/5.1.4 OSX stops loading if ::-webkit-scrollbar is used and scrollbars are visible
			body.style.overflow = 'hidden';
			docOverflow = docElement.style.overflow;
			docElement.style.overflow = 'hidden';
			docElement.appendChild(body);
		}

		ret = callback(div, rule);
		// If this is done after page load we don't want to remove the body so check if body exists
		if (body.fake) {
			body.parentNode.removeChild(body);
			docElement.style.overflow = docOverflow;
			// Trigger layout so kinetic scrolling isn't disabled in iOS6+
			// eslint-disable-next-line
			docElement.offsetHeight;
		} else {
			div.parentNode.removeChild(div);
		}

		return !!ret;

	}

	;

	/**
	 * testStyles injects an element with style element and some CSS rules
	 *
	 * @memberof Modernizr
	 * @name Modernizr.testStyles
	 * @optionName Modernizr.testStyles()
	 * @optionProp testStyles
	 * @access public
	 * @function testStyles
	 * @param {string} rule - String representing a css rule
	 * @param {function} callback - A function that is used to test the injected element
	 * @param {number} [nodes] - An integer representing the number of additional nodes you want injected
	 * @param {string[]} [testnames] - An array of strings that are used as ids for the additional nodes
	 * @returns {boolean}
	 * @example
	 *
	 * `Modernizr.testStyles` takes a CSS rule and injects it onto the current page
	 * along with (possibly multiple) DOM elements. This lets you check for features
	 * that can not be detected by simply checking the [IDL](https://developer.mozilla.org/en-US/docs/Mozilla/Developer_guide/Interface_development_guide/IDL_interface_rules).
	 *
	 * ```js
	 * Modernizr.testStyles('#modernizr { width: 9px; color: papayawhip; }', function(elem, rule) {
	 *   // elem is the first DOM node in the page (by default #modernizr)
	 *   // rule is the first argument you supplied - the CSS rule in string form
	 *
	 *   addTest('widthworks', elem.style.width === '9px')
	 * });
	 * ```
	 *
	 * If your test requires multiple nodes, you can include a third argument
	 * indicating how many additional div elements to include on the page. The
	 * additional nodes are injected as children of the `elem` that is returned as
	 * the first argument to the callback.
	 *
	 * ```js
	 * Modernizr.testStyles('#modernizr {width: 1px}; #modernizr2 {width: 2px}', function(elem) {
	 *   document.getElementById('modernizr').style.width === '1px'; // true
	 *   document.getElementById('modernizr2').style.width === '2px'; // true
	 *   elem.firstChild === document.getElementById('modernizr2'); // true
	 * }, 1);
	 * ```
	 *
	 * By default, all of the additional elements have an ID of `modernizr[n]`, where
	 * `n` is its index (e.g. the first additional, second overall is `#modernizr2`,
	 * the second additional is `#modernizr3`, etc.).
	 * If you want to have more meaningful IDs for your function, you can provide
	 * them as the fourth argument, as an array of strings
	 *
	 * ```js
	 * Modernizr.testStyles('#foo {width: 10px}; #bar {height: 20px}', function(elem) {
	 *   elem.firstChild === document.getElementById('foo'); // true
	 *   elem.lastChild === document.getElementById('bar'); // true
	 * }, 2, ['foo', 'bar']);
	 * ```
	 *
	 */

	var testStyles = ModernizrProto.testStyles = injectElementWithStyles;
	
/*!
{
	"name": "Touch Events",
	"property": "touchevents",
	"caniuse" : "touch",
	"tags": ["media", "attribute"],
	"notes": [{
		"name": "Touch Events spec",
		"href": "https://www.w3.org/TR/2013/WD-touch-events-20130124/"
	}],
	"warnings": [
		"Indicates if the browser supports the Touch Events spec, and does not necessarily reflect a touchscreen device"
	],
	"knownBugs": [
		"False-positive on some configurations of Nokia N900",
		"False-positive on some BlackBerry 6.0 builds – https://github.com/Modernizr/Modernizr/issues/372#issuecomment-3112695"
	]
}
!*/
/* DOC
Indicates if the browser supports the W3C Touch Events API.

This *does not* necessarily reflect a touchscreen device:

* Older touchscreen devices only emulate mouse events
* Modern IE touch devices implement the Pointer Events API instead: use `Modernizr.pointerevents` to detect support for that
* Some browsers & OS setups may enable touch APIs when no touchscreen is connected
* Future browsers may implement other event models for touch interactions

See this article: [You Can't Detect A Touchscreen](http://www.stucox.com/blog/you-cant-detect-a-touchscreen/).

It's recommended to bind both mouse and touch/pointer events simultaneously – see [this HTML5 Rocks tutorial](http://www.html5rocks.com/en/mobile/touchandmouse/).

This test will also return `true` for Firefox 4 Multitouch support.
*/

	// Chrome (desktop) used to lie about its support on this, but that has since been rectified: http://crbug.com/36415
	Modernizr.addTest('touchevents', function() {
		var bool;
		if (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch) {
			bool = true;
		} else {
			// include the 'heartz' as a way to have a non matching MQ to help terminate the join
			// https://git.io/vznFH
			var query = ['@media (', prefixes.join('touch-enabled),('), 'heartz', ')', '{#modernizr{top:9px;position:absolute}}'].join('');
			testStyles(query, function(node) {
				bool = node.offsetTop === 9;
			});
		}
		return bool;
	});


	var cssomPrefixes = (ModernizrProto._config.usePrefixes ? omPrefixes.split(' ') : []);
	ModernizrProto._cssomPrefixes = cssomPrefixes;
	

	/**
	 * Create our "modernizr" element that we do most feature tests on.
	 *
	 * @access private
	 */

	var modElem = {
		elem: createElement('modernizr')
	};

	// Clean up this element
	Modernizr._q.push(function() {
		delete modElem.elem;
	});

	

	var mStyle = {
		style: modElem.elem.style
	};

	// kill ref for gc, must happen before mod.elem is removed, so we unshift on to
	// the front of the queue.
	Modernizr._q.unshift(function() {
		delete mStyle.style;
	});

	

	/**
	 * domToCSS takes a camelCase string and converts it to kebab-case
	 * e.g. boxSizing -> box-sizing
	 *
	 * @access private
	 * @function domToCSS
	 * @param {string} name - String name of camelCase prop we want to convert
	 * @returns {string} The kebab-case version of the supplied name
	 */

	function domToCSS(name) {
		return name.replace(/([A-Z])/g, function(str, m1) {
			return '-' + m1.toLowerCase();
		}).replace(/^ms-/, '-ms-');
	}
	;


	/**
	 * wrapper around getComputedStyle, to fix issues with Firefox returning null when
	 * called inside of a hidden iframe
	 *
	 * @access private
	 * @function computedStyle
	 * @param {HTMLElement|SVGElement} - The element we want to find the computed styles of
	 * @param {string|null} [pseudoSelector]- An optional pseudo element selector (e.g. :before), of null if none
	 * @returns {CSSStyleDeclaration}
	 */

	function computedStyle(elem, pseudo, prop) {
		var result;

		if ('getComputedStyle' in window) {
			result = getComputedStyle.call(window, elem, pseudo);
			var console = window.console;

			if (result !== null) {
				if (prop) {
					result = result.getPropertyValue(prop);
				}
			} else {
				if (console) {
					var method = console.error ? 'error' : 'log';
					console[method].call(console, 'getComputedStyle returning null, its possible modernizr test results are inaccurate');
				}
			}
		} else {
			result = !pseudo && elem.currentStyle && elem.currentStyle[prop];
		}

		return result;
	}

	;

	/**
	 * nativeTestProps allows for us to use native feature detection functionality if available.
	 * some prefixed form, or false, in the case of an unsupported rule
	 *
	 * @access private
	 * @function nativeTestProps
	 * @param {array} props - An array of property names
	 * @param {string} value - A string representing the value we want to check via @supports
	 * @returns {boolean|undefined} A boolean when @supports exists, undefined otherwise
	 */

	// Accepts a list of property names and a single value
	// Returns `undefined` if native detection not available
	function nativeTestProps(props, value) {
		var i = props.length;
		// Start with the JS API: http://www.w3.org/TR/css3-conditional/#the-css-interface
		if ('CSS' in window && 'supports' in window.CSS) {
			// Try every prefixed variant of the property
			while (i--) {
				if (window.CSS.supports(domToCSS(props[i]), value)) {
					return true;
				}
			}
			return false;
		}
		// Otherwise fall back to at-rule (for Opera 12.x)
		else if ('CSSSupportsRule' in window) {
			// Build a condition string for every prefixed variant
			var conditionText = [];
			while (i--) {
				conditionText.push('(' + domToCSS(props[i]) + ':' + value + ')');
			}
			conditionText = conditionText.join(' or ');
			return injectElementWithStyles('@supports (' + conditionText + ') { #modernizr { position: absolute; } }', function(node) {
				return computedStyle(node, null, 'position') == 'absolute';
			});
		}
		return undefined;
	}
	;

	// testProps is a generic CSS / DOM property test.

	// In testing support for a given CSS property, it's legit to test:
	//    `elem.style[styleName] !== undefined`
	// If the property is supported it will return an empty string,
	// if unsupported it will return undefined.

	// We'll take advantage of this quick test and skip setting a style
	// on our modernizr element, but instead just testing undefined vs
	// empty string.

	// Property names can be provided in either camelCase or kebab-case.

	function testProps(props, prefixed, value, skipValueTest) {
		skipValueTest = is(skipValueTest, 'undefined') ? false : skipValueTest;

		// Try native detect first
		if (!is(value, 'undefined')) {
			var result = nativeTestProps(props, value);
			if (!is(result, 'undefined')) {
				return result;
			}
		}

		// Otherwise do it properly
		var afterInit, i, propsLength, prop, before;

		// If we don't have a style element, that means we're running async or after
		// the core tests, so we'll need to create our own elements to use

		// inside of an SVG element, in certain browsers, the `style` element is only
		// defined for valid tags. Therefore, if `modernizr` does not have one, we
		// fall back to a less used element and hope for the best.
		// for strict XHTML browsers the hardly used samp element is used
		var elems = ['modernizr', 'tspan', 'samp'];
		while (!mStyle.style && elems.length) {
			afterInit = true;
			mStyle.modElem = createElement(elems.shift());
			mStyle.style = mStyle.modElem.style;
		}

		// Delete the objects if we created them.
		function cleanElems() {
			if (afterInit) {
				delete mStyle.style;
				delete mStyle.modElem;
			}
		}

		propsLength = props.length;
		for (i = 0; i < propsLength; i++) {
			prop = props[i];
			before = mStyle.style[prop];

			if (contains(prop, '-')) {
				prop = cssToDOM(prop);
			}

			if (mStyle.style[prop] !== undefined) {

				// If value to test has been passed in, do a set-and-check test.
				// 0 (integer) is a valid property value, so check that `value` isn't
				// undefined, rather than just checking it's truthy.
				if (!skipValueTest && !is(value, 'undefined')) {

					// Needs a try catch block because of old IE. This is slow, but will
					// be avoided in most cases because `skipValueTest` will be used.
					try {
						mStyle.style[prop] = value;
					} catch (e) {}

					// If the property value has changed, we assume the value used is
					// supported. If `value` is empty string, it'll fail here (because
					// it hasn't changed), which matches how browsers have implemented
					// CSS.supports()
					if (mStyle.style[prop] != before) {
						cleanElems();
						return prefixed == 'pfx' ? prop : true;
					}
				}
				// Otherwise just return true, or the property name if this is a
				// `prefixed()` call
				else {
					cleanElems();
					return prefixed == 'pfx' ? prop : true;
				}
			}
		}
		cleanElems();
		return false;
	}

	;

	/**
	 * testProp() investigates whether a given style property is recognized
	 * Property names can be provided in either camelCase or kebab-case.
	 *
	 * @memberof Modernizr
	 * @name Modernizr.testProp
	 * @access public
	 * @optionName Modernizr.testProp()
	 * @optionProp testProp
	 * @function testProp
	 * @param {string} prop - Name of the CSS property to check
	 * @param {string} [value] - Name of the CSS value to check
	 * @param {boolean} [useValue] - Whether or not to check the value if @supports isn't supported
	 * @returns {boolean}
	 * @example
	 *
	 * Just like [testAllProps](#modernizr-testallprops), only it does not check any vendor prefixed
	 * version of the string.
	 *
	 * Note that the property name must be provided in camelCase (e.g. boxSizing not box-sizing)
	 *
	 * ```js
	 * Modernizr.testProp('pointerEvents')  // true
	 * ```
	 *
	 * You can also provide a value as an optional second argument to check if a
	 * specific value is supported
	 *
	 * ```js
	 * Modernizr.testProp('pointerEvents', 'none') // true
	 * Modernizr.testProp('pointerEvents', 'penguin') // false
	 * ```
	 */

	var testProp = ModernizrProto.testProp = function(prop, value, useValue) {
		return testProps([prop], undefined, value, useValue);
	};
	

	/**
	 * fnBind is a super small [bind](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Function/bind) polyfill.
	 *
	 * @access private
	 * @function fnBind
	 * @param {function} fn - a function you want to change `this` reference to
	 * @param {object} that - the `this` you want to call the function with
	 * @returns {function} The wrapped version of the supplied function
	 */

	function fnBind(fn, that) {
		return function() {
			return fn.apply(that, arguments);
		};
	}

	;

	/**
	 * testDOMProps is a generic DOM property test; if a browser supports
	 *   a certain property, it won't return undefined for it.
	 *
	 * @access private
	 * @function testDOMProps
	 * @param {array.<string>} props - An array of properties to test for
	 * @param {object} obj - An object or Element you want to use to test the parameters again
	 * @param {boolean|object} elem - An Element to bind the property lookup again. Use `false` to prevent the check
	 * @returns {false|*} returns false if the prop is unsupported, otherwise the value that is supported
	 */
	function testDOMProps(props, obj, elem) {
		var item;

		for (var i in props) {
			if (props[i] in obj) {

				// return the property name as a string
				if (elem === false) {
					return props[i];
				}

				item = obj[props[i]];

				// let's bind a function
				if (is(item, 'function')) {
					// bind to obj unless overriden
					return fnBind(item, elem || obj);
				}

				// return the unbound function or obj or value
				return item;
			}
		}
		return false;
	}

	;

	/**
	 * testPropsAll tests a list of DOM properties we want to check against.
	 * We specify literally ALL possible (known and/or likely) properties on
	 * the element including the non-vendor prefixed one, for forward-
	 * compatibility.
	 *
	 * @access private
	 * @function testPropsAll
	 * @param {string} prop - A string of the property to test for
	 * @param {string|object} [prefixed] - An object to check the prefixed properties on. Use a string to skip
	 * @param {HTMLElement|SVGElement} [elem] - An element used to test the property and value against
	 * @param {string} [value] - A string of a css value
	 * @param {boolean} [skipValueTest] - An boolean representing if you want to test if value sticks when set
	 * @returns {false|string} returns the string version of the property, or false if it is unsupported
	 */
	function testPropsAll(prop, prefixed, elem, value, skipValueTest) {

		var ucProp = prop.charAt(0).toUpperCase() + prop.slice(1),
			props = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

		// did they call .prefixed('boxSizing') or are we just testing a prop?
		if (is(prefixed, 'string') || is(prefixed, 'undefined')) {
			return testProps(props, prefixed, value, skipValueTest);

			// otherwise, they called .prefixed('requestAnimationFrame', window[, elem])
		} else {
			props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
			return testDOMProps(props, prefixed, elem);
		}
	}

	// Modernizr.testAllProps() investigates whether a given style property,
	// or any of its vendor-prefixed variants, is recognized
	//
	// Note that the property names must be provided in the camelCase variant.
	// Modernizr.testAllProps('boxSizing')
	ModernizrProto.testAllProps = testPropsAll;

	

	/**
	 * testAllProps determines whether a given CSS property is supported in the browser
	 *
	 * @memberof Modernizr
	 * @name Modernizr.testAllProps
	 * @optionName Modernizr.testAllProps()
	 * @optionProp testAllProps
	 * @access public
	 * @function testAllProps
	 * @param {string} prop - String naming the property to test (either camelCase or kebab-case)
	 * @param {string} [value] - String of the value to test
	 * @param {boolean} [skipValueTest=false] - Whether to skip testing that the value is supported when using non-native detection
	 * @example
	 *
	 * testAllProps determines whether a given CSS property, in some prefixed form,
	 * is supported by the browser.
	 *
	 * ```js
	 * testAllProps('boxSizing')  // true
	 * ```
	 *
	 * It can optionally be given a CSS value in string form to test if a property
	 * value is valid
	 *
	 * ```js
	 * testAllProps('display', 'block') // true
	 * testAllProps('display', 'penguin') // false
	 * ```
	 *
	 * A boolean can be passed as a third parameter to skip the value check when
	 * native detection (@supports) isn't available.
	 *
	 * ```js
	 * testAllProps('shapeOutside', 'content-box', true);
	 * ```
	 */

	function testAllProps(prop, value, skipValueTest) {
		return testPropsAll(prop, undefined, undefined, value, skipValueTest);
	}
	ModernizrProto.testAllProps = testAllProps;
	
/*!
{
	"name": "CSS Transforms 3D",
	"property": "csstransforms3d",
	"caniuse": "transforms3d",
	"tags": ["css"],
	"warnings": [
		"Chrome may occassionally fail this test on some systems; more info: https://code.google.com/p/chromium/issues/detail?id=129004"
	]
}
!*/

	Modernizr.addTest('csstransforms3d', function() {
		return !!testAllProps('perspective', '1px', true);
	});

/*!
{
	"name": "CSS Animations",
	"property": "cssanimations",
	"caniuse": "css-animation",
	"polyfills": ["transformie", "csssandpaper"],
	"tags": ["css"],
	"warnings": ["Android < 4 will pass this test, but can only animate a single property at a time"],
	"notes": [{
		"name" : "Article: 'Dispelling the Android CSS animation myths'",
		"href": "https://goo.gl/OGw5Gm"
	}]
}
!*/
/* DOC
Detects whether or not elements can be animated using CSS
*/

	Modernizr.addTest('cssanimations', testAllProps('animationName', 'a', true));

/*!
{
	"name": "CSS Transitions",
	"property": "csstransitions",
	"caniuse": "css-transitions",
	"tags": ["css"]
}
!*/

	Modernizr.addTest('csstransitions', testAllProps('transition', 'all', true));

	// Run each test
	testRunner();

	// Remove the "no-js" class if it exists
	setClasses(classes);

	delete ModernizrProto.addTest;
	delete ModernizrProto.addAsyncTest;

	// Run the things that are supposed to run after the tests
	for (var i = 0; i < Modernizr._q.length; i++) {
		Modernizr._q[i]();
	}

	// Leak Modernizr namespace
	window.Modernizr = Modernizr;

;

})(window, document);
