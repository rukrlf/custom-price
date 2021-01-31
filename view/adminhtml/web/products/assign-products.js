/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            custompriceProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000;

        $('in_customprice_products').value = Object.toJSON(custompriceProducts);

        /**
         * Register Customprice Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCustompriceProduct(grid, element, checked) {
            if (checked) {
                if (element.positionElement) {
                    element.positionElement.disabled = false;
                    custompriceProducts.set(element.value, element.positionElement.value);
                }
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                custompriceProducts.unset(element.value);
            }
            $('in_customprice_products').value = Object.toJSON(custompriceProducts);
            grid.reloadParams = {
                'selected_products[]': custompriceProducts.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function custompriceProductRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                eventElement = Event.element(event),
                isInputCheckbox = eventElement.tagName === 'INPUT' && eventElement.type === 'checkbox',
                isInputPosition = grid.targetElement &&
                    grid.targetElement.tagName === 'INPUT' &&
                    grid.targetElement.name === 'custom_special_price',
                checked = false,
                checkbox = null;

            if (eventElement.tagName === 'LABEL' &&
                trElement.querySelector('#' + eventElement.htmlFor) &&
                trElement.querySelector('#' + eventElement.htmlFor).type === 'checkbox'
            ) {
                event.stopPropagation();
                trElement.querySelector('#' + eventElement.htmlFor).trigger('click');

                return;
            }

            if (trElement && !isInputPosition) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInputCheckbox ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         * Change product position
         *
         * @param {String} event
         */
        function positionChange(event) {
            var element = Event.element(event);

            if (element && element.checkboxElement && element.checkboxElement.checked) {
                custompriceProducts.set(element.checkboxElement.value, element.value);
                $('in_customprice_products').value = Object.toJSON(custompriceProducts);
            }
        }

        /**
         * Initialize customprice product row
         *
         * @param {Object} grid
         * @param {String} row
         */
        function custompriceProductRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
                Event.observe(position, 'keyup', positionChange);
            }
        }

        gridJsObject.rowClickCallback = custompriceProductRowClick;
        gridJsObject.initRowCallback = custompriceProductRowInit;
        gridJsObject.checkboxCheckCallback = registerCustompriceProduct;

        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                custompriceProductRowInit(gridJsObject, row);
            });
        }
    };
});
