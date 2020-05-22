define(['TYPO3/CMS/In2bemail/Utility/UiUtility'], function(UiUtility) {
	'use strict';

	var Loader = {
		identifiers: {
			loader: '.js-in2bemail-loader',
			loaderActive: '.in2bemail-loader--active'
		}
	};

	Loader.enableLoader = function() {
		UiUtility.toggleClassForElement(
			document.querySelector(this.identifiers.loader),
			this.identifiers.loaderActive.substr(1)
		);
	};

	Loader.disableLoader = function() {
		if (document.querySelector(this.identifiers.loaderActive) !== null) {
			UiUtility.toggleClassForElement(
				document.querySelector(this.identifiers.loaderActive),
				this.identifiers.loaderActive.substr(1)
			);
		}
	};

	return Loader;
});
