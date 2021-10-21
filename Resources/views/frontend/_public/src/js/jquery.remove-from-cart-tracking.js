(function ($, window) {
    $.plugin('wbmRemoveFromCartTracking', {
        init: function () {
            var me = this;

            me._on(me.$el, 'click', $.proxy(me.onProductClicked, me));
        },
        /**
         * @param {jQuery.Event} event
         */
        onProductClicked: function (event) {
            var ordernumber = $(event.target).closest('.cart-item-delete').data('ordernumber');
                quantity = $(event.target).closest('.cart-item-delete').data('quantity');
                price = $(event.target).closest('.cart-item-delete').data('price');

            window.dataLayer.push({
                'event': 'removeFromCart',
                'ecommerce': {
                    'remove': [
                        {
                            'id': ordernumber,
                            'quantity': quantity,
                            'price': price
                        }
                    ]
                }
            });
        }
    });

    $.subscribe('plugin/swInfiniteScrolling/onFetchNewPageFinished', function () {
        StateManager.addPlugin('.cart-item-delete', 'wbmRemoveFromCartTracking');
    });

    StateManager.addPlugin('.cart-item-delete', 'wbmRemoveFromCartTracking');
})(jQuery, window);