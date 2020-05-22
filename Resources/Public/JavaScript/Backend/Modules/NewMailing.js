define(['TYPO3/CMS/In2bemail/Backend/Modules/SideBySideSelect','TYPO3/CMS/In2bemail/Utility/UiUtility'], function(SideBySideSelect,UiUtility) {
	'use strict';

	var NewMailing = {
		identifiers: {
			submit: '.js-in2bemail-create-mailing',
			contextSelect: '.js-in2bemail-context'
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
		var beGroupSelection = document.querySelector('.js-in2bemail-be-groups');
		var feGroupSelection = document.querySelector('.js-in2bemail-fe-groups');

		UiUtility.toggleClassForElement(beGroupSelection, 'in2bemail-hide');
		UiUtility.toggleClassForElement(feGroupSelection, 'in2bemail-hide');
	};

	NewMailing.createMailing = function() {
		for (var i = 0; i <= SideBySideSelect.sideBySideElements.length - 1; i++) {
			SideBySideSelect.selectAllItems(SideBySideSelect.sideBySideElements[i]);
		}
	};

	return NewMailing;
});
