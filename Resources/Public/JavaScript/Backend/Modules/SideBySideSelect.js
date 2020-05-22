define(['TYPO3/CMS/In2bemail/Utility/UiUtility', 'TYPO3/CMS/In2bemail/Utility/Loader'], function(UiUtility, Loader) {
	'use strict';

	var SideBySideSelect = {
		sideBySideElements: document.querySelectorAll('[data-in2bemail-type="side-by-side"]'),

		identifiers: {
			container: '[data-in2bemail-type="side-by-side"]',
			selectedItemsList: '.js-in2bemail-selected-items',
			availableItemsList: '.js-in2bemail-available-items'
		}
	};

	SideBySideSelect.initialize = function() {
		for (var i = 0; i <= SideBySideSelect.sideBySideElements.length - 1; i++) {
			SideBySideSelect.prepareSelectedSelect(SideBySideSelect.sideBySideElements[i]);
			SideBySideSelect.addEventListener(SideBySideSelect.sideBySideElements[i]);
		}
	};

	/**
	 * @param sideBySideContainer
	 */
	SideBySideSelect.prepareSelectedSelect = function(sideBySideContainer) {
		var selectedItemsList = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);

		for (var i = 0; i <= selectedItemsList.options.length-1; i++) {
			selectedItemsList.options[i].classList.add('in2bemail-hide');
		}
	};

	SideBySideSelect.addEventListener = function(sideBySideContainer) {
		// click on an available item
		sideBySideContainer.querySelector(SideBySideSelect.identifiers.availableItemsList)
			.addEventListener('click', SideBySideSelect.addItemToSelected);

		// click on the remove item button
		var deleteButton = sideBySideContainer.querySelector('.js-in2bemail-remove-item');
		deleteButton.addEventListener('click', SideBySideSelect.removeSelectedItems);

		// click on move up button
		var moveUpButton = sideBySideContainer.querySelector('.js-in2bemail-move-item-up');
		moveUpButton.addEventListener('click', SideBySideSelect.moveSelectedItemsUp);

		// click on move down button
		var moveDownButton = sideBySideContainer.querySelector('.js-in2bemail-move-item-down');
		moveDownButton.addEventListener('click', SideBySideSelect.moveSelectedItemsDown);

		// click on move to the end button
		var moveToEndButton = sideBySideContainer.querySelector('.js-in2bemail-move-item-end');
		moveToEndButton.addEventListener('click', SideBySideSelect.moveSelectedItemsToEnd);

		// click on move to the beginning button
		var moveToBeginButton = sideBySideContainer.querySelector('.js-in2bemail-move-item-begin');
		moveToBeginButton.addEventListener('click', SideBySideSelect.moveSelectedItemsToBegin);
	};

	/**
	 *
	 * @param sideBySideContainer
	 */
	SideBySideSelect.selectAllItems = function(sideBySideContainer) {
		var items = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);

		// set all elements to selected
		for (var i = 0; i < items.options.length; i++) {
			if (!items.options[i].classList.contains('in2bemail-hide')) {
				items.options[i].selected = true;
			}
		}
	};

	/**
	 * copies the selected option element to the selected items list
	 *
	 * @param event
	 */
	SideBySideSelect.addItemToSelected = function(event) {
		var clickedOption = event.target;
		var sideBySideContainer = event.target.closest(SideBySideSelect.identifiers.container);
		if (clickedOption.getAttribute('data-in2bemail-selectable-item') === 'true') {
			var targetOption = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList + ' option[value="' + clickedOption.value + '"]');
			UiUtility.hideElement(clickedOption);
			UiUtility.toggleClassForElement(targetOption, 'in2bemail-hide');
		}
	};

	/**
	 * remove selected items from selected items list
	 */
	SideBySideSelect.removeSelectedItems = function(event) {
		var sideBySideContainer = event.target.closest(SideBySideSelect.identifiers.container);
		var currentSelection = SideBySideSelect.getSelectedItem(sideBySideContainer);

		if (currentSelection.length) {
			for (var i = 0; i < currentSelection.length; i++) {
				var optionElement = currentSelection[i];
				var optionToHide = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList).options[optionElement.index];

				UiUtility.toggleClassForElement(optionToHide, 'in2bemail-hide');
				SideBySideSelect.showItemInAvailableItems(sideBySideContainer, optionElement.value);
			}
		}
	};

	/**
	 * moves the selected items one position up
	 */
	SideBySideSelect.moveSelectedItemsUp = function(event) {
		var sideBySideContainer = event.target.closest(SideBySideSelect.identifiers.container);
		var selection = SideBySideSelect.getSelectedItem(sideBySideContainer);
		var selectedItemsList = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);

		if (selection.length) {
			for (var i = 0; i < selection.length; i++) {
				if (selection[i].index > 0) {
					var indexBefore = selection[i].index - 1;
					var indexAfter = selection[i].index;
					var elementAfter = selection[i];
					var elementBefore = selectedItemsList.options[selection[i].index - 1];

					selectedItemsList.removeChild(elementAfter);
					selectedItemsList.removeChild(elementBefore);

					selectedItemsList.add(elementAfter, indexBefore);
					selectedItemsList.add(elementBefore, indexAfter);
				}
			}
		}
	};

	/**
	 * moves the selected items one position up
	 */
	SideBySideSelect.moveSelectedItemsDown = function(event) {
		var sideBySideContainer = event.target.closest(SideBySideSelect.identifiers.container);
		var selection = SideBySideSelect.getSelectedItem(sideBySideContainer);
		var selectedItemsList = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);

		if (selection.length) {
			for (var i = selection.length - 1; i >= 0; i--) {
				if (selection[i].index + 1 <= selectedItemsList.length - 1) {
					var indexBefore = selection[i].index;
					var indexAfter = selection[i].index + 1;
					var elementBefore = selection[i];
					var elementAfter = selectedItemsList.options[selection[i].index + 1];

					selectedItemsList.removeChild(elementAfter);
					selectedItemsList.removeChild(elementBefore);

					selectedItemsList.add(elementAfter, indexBefore);
					selectedItemsList.add(elementBefore, indexAfter);
				}
			}
		}
	};

	/**
	 * moves the selected items to the end of the list
	 */
	SideBySideSelect.moveSelectedItemsToEnd = function(event) {
		var sideBySideContainer = event.target.closest(SideBySideSelect.identifiers.container);
		var selection = SideBySideSelect.getSelectedItem(sideBySideContainer);
		var selectedItemsList = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);


		if (selection.length) {
			for (var i = selection.length - 1; i >= 0; i--) {
				selectedItemsList.removeChild(selection[i]);
				selectedItemsList.add(selection[i]);
			}
		}
	};

	/**
	 * moves the selected items to the begin of the list
	 */
	SideBySideSelect.moveSelectedItemsToBegin = function(event) {
		var sideBySideContainer = event.target.closest(SideBySideSelect.identifiers.container);
		var selection = SideBySideSelect.getSelectedItem(sideBySideContainer);
		var selectedItemsList = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);


		if (selection.length) {
			for (var i = 0; i < selection.length; i++) {
				selectedItemsList.removeChild(selection[i]);
				selectedItemsList.add(selection[i], 0);
			}
		}
	};

	/**
	 * enables visibility of the given option name in the available items list
	 *
	 * @param sideBySideContainer
	 * @param value
	 */
	SideBySideSelect.showItemInAvailableItems = function(sideBySideContainer, value) {
		var option = sideBySideContainer.querySelector(SideBySideSelect.identifiers.availableItemsList).querySelector('option[value="' + value + '"]');
		UiUtility.showElementAsBlock(option);
	};

	/**
	 * get the current selected items form the selected items list
	 *
	 * @returns {Array}
	 */
	SideBySideSelect.getSelectedItem = function(sideBySideContainer) {
		var selection = [];
		var selectedItemsList = sideBySideContainer.querySelector(SideBySideSelect.identifiers.selectedItemsList);
		/*
		 * get current selection from selectedItemsList
		 * we use this method to get the current selection because
		 * "selectedOptions" do not work on IE
		 */
		for (var i = 0; i < selectedItemsList.length; i++) {
			if (selectedItemsList.options[i].selected) {
				selection.push(selectedItemsList.options[i]);
			}
		}

		return selection;
	};

	return SideBySideSelect;
});
