;(function (root, factory) {
	"use strict";

	if (typeof define === 'function' && define.amd) {
		define(['lottie', 'in-view'], factory);
	} else {
		root.LottieInitialization = factory(root.lottie, root.inView);
		root.LottieInitializationInstance = new root.LottieInitialization();
        // Support old naming mistake, this will be removed in future versions
		root.LottieIntialization = root.LottieInitialization
		root.LottieIntializationInstance = root.LottieInitializationInstance
	}
} (typeof self !== 'undefined' ? self : this, function (lottie, inView) {

	// Pass in the objects to merge as arguments.
	// For a deep extend, set the first argument to `true`.
	// https://gomakethings.com/vanilla-javascript-version-of-jquery-extend/#a-native-js-extend-function
	var extend = function () {

		// Variables
		var extended = {};
		var deep = false;
		var i = 0;
		var length = arguments.length;

		// Check if a deep merge
		if ( Object.prototype.toString.call( arguments[0] ) === '[object Boolean]' ) {
			deep = arguments[0];
			i++;
		}

		// Merge the object into the extended object
		var merge = function (obj) {
			for ( var prop in obj ) {
				if ( Object.prototype.hasOwnProperty.call( obj, prop ) ) {
					// If deep merge and property is an object, merge properties
					if ( deep && Object.prototype.toString.call(obj[prop]) === '[object Object]' ) {
						extended[prop] = extend( true, extended[prop], obj[prop] );
					} else {
						extended[prop] = obj[prop];
					}
				}
			}
		};

		// Loop through each object and conduct a merge
		for ( ; i < length; i++ ) {
			var obj = arguments[i];
			merge(obj);
		}

		return extended;
	};

	function LottieInitialization (options) {
		var that = this;
		that.options = extend(true, LottieInitialization.defaultOptions, options);

		lottie.searchAnimations();

		var inViewElements = that.inViewElements = inView(that.options.inViewSelector);
		that.inViewElements.options.threshold = that.options.inViewThreshold;
		inViewElements.on('enter', function (inViewElement) {
			that.inViewLottieHandler(inViewElement, function (animation) {
				animation.play();
			});
		});
		inViewElements.on('exit', function (inViewElement) {
			that.inViewLottieHandler(inViewElement, function (animation) {
				animation.pause();
			});
		});
	}
	LottieInitialization.defaultOptions = {
		inViewSelector: '.lottie',
		inViewThreshold: 0.5
	};
	LottieInitialization.prototype = extend(true, LottieInitialization.prototype, {
		inViewLottieHandler: function (element, callback) {
			this.getRegisteredAnimations().forEach(function (animation) {
				if (element === animation.wrapper) {
					callback.apply(element, [animation, element]);
				}
			});
		},
		getRegisteredAnimations: function () {
			return lottie.getRegisteredAnimations();
		},
		getInviewElements: function() {
			return this.inViewElements;
		}
	});

	return LottieInitialization;
}));
