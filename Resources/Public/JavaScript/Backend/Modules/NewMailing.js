define(['TYPO3/CMS/Groupmailer/Backend/Modules/SideBySideSelect', 'TYPO3/CMS/Groupmailer/Utility/UiUtility', 'TYPO3/CMS/Backend/Notification'], function(SideBySideSelect, UiUtility, Notification) {
	'use strict';

	var NewMailing = {
		identifiers: {
			submit: '.js-groupmailer-create-mailing',
			contextSelect: '.js-groupmailer-context'
		},
		i18n: {
			de: {
				error: {
					groupSelection: {
						title: 'Keine Benutzergruppe ausgewählt!',
						message: 'Es muss mindestens eine Benutzergruppe für ein Mailing ausgewählt sein!'
					}
				}
			},
			en: {
				error: {
					groupSelection: {
						title: 'No user group selected!',
						message: 'At least one user group must be selected for a mailing!'
					}
				}
			}
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

	/**
	 * @returns {number}
	 */
	NewMailing.groupSelectionValidation = function() {
		var items = document.querySelector(SideBySideSelect.identifiers.selectedItemsList);
		var count = 0;
		for (var i = 0; i < items.options.length; i++) {
			if (!items.options[i].classList.contains('groupmailer-hide')) {
				count++;
			}
		}

		return count;
	};

	NewMailing.renderSelectGroupError = function() {
		var language = 'en';

		if (document.querySelector('.js-groupmailer-new')) {
			language = document.querySelector('.js-groupmailer-new').getAttribute('data-groupmailer-language');
		}

		if (language in NewMailing.i18n) {
			Notification.error(NewMailing.i18n[language].error.groupSelection.title, NewMailing.i18n[language].error.groupSelection.message, 5);
		} else {
			Notification.error(NewMailing.i18n.en.error.groupSelection.title, NewMailing.i18n.en.error.groupSelection.message, 5);
		}
	};

	/**
	 * @param event
	 */
	NewMailing.createMailing = function(event) {
		if (NewMailing.groupSelectionValidation() === 0) {
			event.preventDefault();

			NewMailing.renderSelectGroupError();
		}

		for (var i = 0; i <= SideBySideSelect.sideBySideElements.length - 1; i++) {
			SideBySideSelect.selectAllItems(SideBySideSelect.sideBySideElements[i]);
		}
	};

	return NewMailing;
});
