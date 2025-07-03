define([
    'jquery',
    'jquery-ui-modules/widget'
], function($) {
    'use strict';

    $.widget('ograre.offerRotator', {
        offerSelector: '[data-role=offer-item]',
        classCurrent: 'active',
        previousElement: {},
        nextElement: {},
        offerItems: {},
        currentIndex: 0,
        maxIndex: 0,
        _create: function() {
            // init inner data
            this.offerItems = $(this.offerSelector, this.element);
            this.maxIndex = this.offerItems.length -1;

            // create control
            this.previousElement = $('[data-role=control-previous]', this.element);
            this.nextElement = $('[data-role=control-next]', this.element);

            // init control event
            this.previousElement.on('click', this.previous.bind(this))
            this.nextElement.on('click', this.next.bind(this))

            // start rotating
            this.placeCurrentClass();
        },
        previous: function() {
            this.currentIndex--;
            if (this.currentIndex < 0) {
                this.currentIndex = this.maxIndex;
            }

            this.placeCurrentClass();
        },
        next: function() {
            this.currentIndex++;
            if (this.currentIndex > this.maxIndex) {
                this.currentIndex = 0;
            }

            this.placeCurrentClass();
        },
        placeCurrentClass: function() {
            $.each(this.offerItems, function(index, element) {
                element = $(element);
                if (index !== this.currentIndex) {
                    element.removeClass(this.classCurrent)
                } else {
                    element.addClass(this.classCurrent)
                }
            }.bind(this))
        }
    });

    return $.ograre.offerRotator;
})
