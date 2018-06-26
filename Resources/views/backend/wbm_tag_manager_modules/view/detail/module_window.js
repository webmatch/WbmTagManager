//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules.view.detail.ModuleWindow', {
    extend: 'Enlight.app.Window',
    title: '{s name="modulesTitle"}Modules{/s}',
    alias: 'widget.wbm-tag-manager-modules-detail-module-window',
    border: false,
    autoShow: true,
    layout: 'fit',
    height: 300,
    width: 400,
    modal: true,
    initComponent: function() {
        var me = this;

        me.items = [
            {
                xtype: 'wbm-tag-manager-modules-detail-module',
                record: me.record,
                slotStore: me.slotStore
            }
        ];

        me.callParent(arguments);
    }
});