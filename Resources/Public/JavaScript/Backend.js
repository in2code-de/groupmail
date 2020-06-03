define(['TYPO3/CMS/Groupmailer/Backend/Modules/NewMailing'], function(NewMailing) {
	'use strict';

	if (document.querySelector('.js-groupmailer-new')) {
		NewMailing.initialize();
	}

	if (document.querySelector('.js-groupmailer-index')) {

	}

});
