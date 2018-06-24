//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManager.model.Modules', {
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
        }
    ],
    idProperty: 'id'
});
