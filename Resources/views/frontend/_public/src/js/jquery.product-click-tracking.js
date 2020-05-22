$.plugin('wbmProductClickTracking', {
    init: function () {
        var me = this,
            i,
            impressions = []

        if (window.dataLayer) {
            for (i = 0; i < window.dataLayer.length; i++) {
                var layer = window.dataLayer[i];

                if (layer.ecommerce && layer.ecommerce.impressions) {
                    impressions = layer.ecommerce.impressions;
                }
            }
        }

        me._on(me.$el, 'click', $.proxy(me.onProductClicked, me, impressions));
    },
    /**
        * @param {array} impressions
        * @param {jQuery.Event} event
    */
    onProductClicked: function (impressions, event) {
        let ordernumber = $(event.target).closest('.product--box').data('ordernumber');
        let product = impressions.find(x => x.id === ordernumber);

        window.dataLayer.push({
            'event': 'productClick',
            'ecommerce': {
                'click': {
                    'actionField': {'list': product.list},
                    'products': [{
                        'name': product.name,
                        'id': product.id,
                        'price': product.price,
                        'brand': product.brand,
                        'category': product.category,
                        'position': product.position
                    }]
                }
            }
        });
    }
});

StateManager.addPlugin('.product--box a', 'wbmProductClickTracking');