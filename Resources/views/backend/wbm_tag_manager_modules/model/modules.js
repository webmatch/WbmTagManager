//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules.model.Modules', {
    extend : 'Ext.data.Model', 
    fields : [
        {
            name: 'id',
            type: 'int',
            useNull: true
        },
        {
            name: 'module',
            type: 'string'
        },
        {
            name: 'name',
            type: 'string'
        },
        {
            name: 'predispatch',
            type: 'boolean',
            defaultValue: 0
        }
    ],
    idProperty: 'id',
    proxy: {
        type : 'ajax',
        api:{
            create : '{url controller="WbmTagManagerModules" action="save"}',
            update : '{url controller="WbmTagManagerModules" action="save"}',
            destroy : '{url controller="WbmTagManagerModules" action="delete"}'
        },
        reader : {
            type : 'json',
            root : 'data'
        }
    }
});
