define(['TYPO3/CMS/Groupmailer/Backend/Modules/SideBySideSelect','TYPO3/CMS/Groupmailer/Utility/UiUtility'], function(SideBySideSelect,UiUtility) {
	'use strict';

	var NewMailing = {
		identifiers: {
			submit: '.js-groupmailer-create-mailing',
			contextSelect: '.js-groupmailer-context'
		}
	};

	NewMailing.initialize = function() {
		SideBySideSelect.initialize();
		NewMailing.addEventListener();
	};

	NewMailing.addEventListener = function() {
		var submitButton = document.querySelector(NewMailing.identifiers.submit);
		submitButton.addEventListener('click', NewMailing.createMailing);

		// on mailing type (context) switch
		var contextSelect = document.querySelector(NewMailing.identifiers.contextSelect);
		contextSelect.addEventListener('change', NewMailing.contextSwitch);
	};

	NewMailing.contextSwitch = function(event) {
		var beGroupSelection = document.querySelector('.js-groupmailer-be-groups');
		var feGroupSelection = document.querySelector('.js-groupmailer-fe-groups');

		UiUtility.toggleClassForElement(beGroupSelection, 'groupmailer-hide');
		UiUtility.toggleClassForElement(feGroupSelection, 'groupmailer-hide');
	};

	NewMailing.createMailing = function() {
		for (var i = 0; i <= SideBySideSelect.sideBySideElements.length - 1; i++) {
			SideBySideSelect.selectAllItems(SideBySideSelect.sideBySideElements[i]);
		}
	};

	return NewMailing;
});
