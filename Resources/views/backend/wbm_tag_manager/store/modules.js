//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManager.store.Modules', {
    extend: 'Ext.data.Store',
    remoteFilter: true,
    autoLoad : false,
    model : 'Shopware.apps.WbmTagManager.model.Modules',
    pageSize: 20,
    proxy: {
        type: 'ajax',
        url: '{url controller="WbmTagManagerModules" action="list"}',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});
