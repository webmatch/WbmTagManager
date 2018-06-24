//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules.controller.Main', {

    extend: 'Ext.app.Controller',

    mainWindow: null,

    init: function() {
        var me = this;

        me.mainWindow = me.getView('Window').create({
            modulesStore: me.getStore('Modules')
        });

        me.callParent(arguments);

        me.control({
            'wbm-tag-manager-modules-detail-module button[action=save]': {
                'click': function(btn) {
                    this.onModuleSave(btn);
                }
            },
            'wbm-tag-manager-modules-modules-grid button[action=addModule]': {
                'click': function (btn) {
                    this.addModule(btn);
                }
            },
            'wbm-tag-manager-modules-modules-grid': {
                openModuleDetail: me.openModuleDetail,
                deleteModule: me.deleteModule
            },
        });

    },

    onModuleSave: function (btn) {
        var me = this,
            win = btn.up('window'),
            form = win.down('form'),
            formBasis = form.getForm(),
            store = me.getStore('Modules'),
            record = formBasis.getRecord();

        formBasis.updateRecord(record);

        if (formBasis.isValid()) {
            record.save({
                success: function() {
                    store.load();
                    win.close();
                    Shopware.Msg.createGrowlMessage('','{s name="moduleSaved"}Module saved{/s}', '');

                    var me = this,
                        openWindow = Ext.getCmp('WbmTagManagerMainWindow');

                    if (openWindow) {
                        openWindow.close();
                    }

                    Shopware.app.Application.addSubApplication({
                        name: 'Shopware.apps.WbmTagManager',
                        action: 'index'
                    });
                },
                failure: function() {
                    store.load();
                    win.close();
                    Shopware.Msg.createGrowlMessage('',
                        '{s name="moduleError"}Error saving module{/s}',
                        '');
                }
            });
        }
    },

    addModule: function () {
        var me = this;

        me.record = Ext.create('Shopware.apps.WbmTagManagerModules.model.Modules');

        me.getView('detail.ModuleWindow').create({
            record: me.record
        }).show();
    },

    openModuleDetail: function (view, rowIndex) {
        var me = this;

        me.record = me.getStore('Modules').getAt(rowIndex);

        me.getView('detail.ModuleWindow').create({
            record: me.record
        }).show();
    },

    deleteModule: function (view, rowIndex) {
        var me = this,
            store = me.getStore('Modules');

        me.record = store.getAt(rowIndex);

        if (me.record instanceof Ext.data.Model && me.record.get('id') > 0) {
            Ext.MessageBox.confirm('', '{s name="moduleDeleteWarning"}Are you sure you want to delete?{/s}' , function (response) {
                if ( response !== 'yes' ) {
                    return;
                }
                me.record.destroy({
                    callback: function() {
                        Shopware.Msg.createGrowlMessage('','{s name="moduleDeleteSuccess"}Module was deleted{/s}', '');
                        store.load();

                        var openWindow = Ext.getCmp('WbmTagManagerMainWindow');

                        if (openWindow) {
                            openWindow.close();
                        }

                        Shopware.app.Application.addSubApplication({
                            name: 'Shopware.apps.WbmTagManager',
                            action: 'index'
                        });
                    }
                });
            });
        }
    }
   
});
 
