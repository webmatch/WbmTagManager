//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules.view.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name="modulesTitle"}Modules{/s}',
    id: 'WbmTagManagerModulesWindow',
    alias: 'widget.wbm-tag-manager-modules',
    border: false,
    autoShow: true,
    height: 400,
    width: 600,
    layout: 'fit',
 
    initComponent: function() {
        var me = this;

        me.items = [
            {
                xtype: 'wbm-tag-manager-modules-modules-grid',
                store: me.modulesStore,
                flex: 1
            }
        ];
    
        me.callParent(arguments);
    }

});
 
