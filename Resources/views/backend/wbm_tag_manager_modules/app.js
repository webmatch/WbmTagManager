//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules', {
    extend:'Enlight.app.SubApplication',
    name:'Shopware.apps.WbmTagManagerModules',
    bulkLoad: true,
    loadPath: '{url action=load}',
    controllers: ['Main'],
    models: [ 'Modules' ],
    views: [
        'Window',
        'grid.Modules',
        'detail.Module',
        'detail.ModuleWindow'
    ],
    stores: [ 'Modules' ],

    /** Main Function
     * @private
     * @return [object] mainWindow - the main application window based on Enlight.app.Window
     */
    launch: function() {
        var me = this;
        var mainController = me.getController('Main');

        return mainController.mainWindow;
    }
});