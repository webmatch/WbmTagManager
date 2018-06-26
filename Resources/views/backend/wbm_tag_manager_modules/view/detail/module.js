//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules.view.detail.Module', {
    extend:'Ext.form.Panel',
    alias:'widget.wbm-tag-manager-modules-detail-module',
    collapsible: false,
    bodyPadding: 10,
    split: false,
    region: 'center',
    defaultType: 'textfield',
    autoScroll: true,
    layout: {
        type: 'vbox',
        align : 'stretch',
        pack  : 'start'
    },
    items : [],
    initComponent: function() {
        var me = this;
        
        me.dockedItems = [
            {
                xtype: 'toolbar',
                dock: 'bottom',
                cls: 'shopware-toolbar',
                ui: 'shopware-ui',
                items: me.getButtons()
            }
        ];
        
        me.items = me.getItems();
        
        me.callParent(arguments);

        me.loadRecord(me.record);
    },  
    getItems:function () {
        var me = this;

        return [
            {
                fieldLabel: '{s name="moduleNameLabel"}Name{/s}',
                labelWidth: 50,
                anchor: '100%',
                name: 'name',
                allowBlank: false
            },
            {
                fieldLabel: '{s name="moduleLabel"}Module{/s}',
                labelWidth: 50,
                anchor: '100%',
                name: 'module',
                allowBlank: false
            },
            {
                xtype: 'container',
                renderTpl: new Ext.XTemplate(
                    '{s name="moduleHelpText"}{/s}'
                )
            },
            {
                fieldLabel: '{s name="predispatch"}Pre-Dispatch{/s}',
                xtype: 'checkbox',
                name: 'predispatch',
                inputValue: 1,
                uncheckedValue: 0
            }
        ];
    },
    getButtons : function()
    {
        var me = this;
        return [
            '->',
            {
                text: '{s name="cancel"}Cancel{/s}',
                scope: me,
                cls: 'secondary',
                handler: function() {
                    var me = this,
                        win = me.up('window');

                    win.destroy();
                }
            },
            {
                text: '{s name="save"}Save{/s}',
                action: 'save',
                cls: 'primary',
                formBind: true
            }
        ];
    }
});