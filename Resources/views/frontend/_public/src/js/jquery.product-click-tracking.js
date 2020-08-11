(function ($, window) {
    $.plugin('wbmProductClickTracking', {
        init: function () {
            var me = this;

            me.setImpressions();

            me._on(me.$el, 'click', $.proxy(me.onProductClicked, me));
        },
        /**
         * @param {jQuery.Event} event
         */
        onProductClicked: function (event) {
            var me = this,
                ordernumber = $(event.target).closest('.product--box').data('ordernumber'),
                product = me.impressions.find(function (value, index) {
                    return value.id ===  ordernumber;
                });

            if (product === undefined) {
                return;
            }

            window.dataLayer.push({
                'event': 'productClick',
                'ecommerce': {
                    'click': {
                        'actionField': {'list': product.list},
                        'products': [ product ]
                    }
                }
            });
        },
        setImpressions: function () {
            var me = this;

            if (window.dataLayer) {
                for (i = 0; i < window.dataLayer.length; i++) {
                    var layer = window.dataLayer[i];

                    if (layer.ecommerce && layer.ecommerce.impressions) {
                        me.impressions = layer.ecommerce.impressions;
                    }
                }
            }
        }
    });

    $.subscribe('plugin/swInfiniteScrolling/onFetchNewPageFinished', function () {
        StateManager.addPlugin('.product--box a', 'wbmProductClickTracking');
    });

    StateManager.addPlugin('.product--box a', 'wbmProductClickTracking');
})(jQuery, window);