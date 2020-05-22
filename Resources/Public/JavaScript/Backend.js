define(['TYPO3/CMS/In2bemail/Backend/Modules/NewMailing'], function(NewMailing) {
	'use strict';

	if (document.querySelector('.js-in2bemail-new')) {
		NewMailing.initialize();
	}

	if (document.querySelector('.js-in2bemail-index')) {

	}

});
