//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManagerModules.view.grid.Modules', {
    extend:'Ext.grid.Panel',
    border: false,
    alias:'widget.wbm-tag-manager-modules-modules-grid',
    region:'center',
    autoScroll:true,
    initComponent:function () {
        var me = this;
        me.columns = me.getColumns();
        me.dockedItems = [
                {
                    xtype: 'toolbar',
                    dock: 'top',
                    cls: 'shopware-toolbar',
                    ui: 'shopware-ui',
                    items: me.getButtons()
                }
            ];
        me.callParent(arguments);
    },
    getColumns:function () {
        var me = this;

        return [
            {
                header: '{s name="moduleNameLabel"}Name{/s}',
                flex: 1,
                dataIndex: 'name'
            },
            {
                header: '{s name="moduleLabel"}Module{/s}',
                flex: 1,
                dataIndex: 'module'
            },
            {
                xtype: 'actioncolumn',
                width: 60,
                items: me.getActionColumnItems()
            }
        ];
    },
    getActionColumnItems: function () {
        var me = this;

        return [
            {
                iconCls:'x-action-col-icon sprite-pencil',
                tooltip:'{s name="edit"}Edit{/s}',
                getClass: function(value, metadata, record) {
                    if (!record.get("id")) {
                        return 'x-hidden';
                    }
                },
                handler:function (view, rowIndex, colIndex, item) {
                    me.fireEvent('openModuleDetail', view, rowIndex, colIndex, item);
                }
            },
            {
                iconCls:'x-action-col-icon sprite-minus-circle-frame',
                tooltip:'{s name="delete"}Delete{/s}',
                getClass: function(value, metadata, record) {
                    if (!record.get("id")) {
                        return 'x-hidden';
                    }
                },
                handler:function (view, rowIndex, colIndex, item) {
                    me.fireEvent('deleteModule', view, rowIndex, colIndex, item);
                }
            }
        ];
    },
    getButtons : function() {
        var me = this;

        return [
            {
                text: '{s name="add"}Add{/s}',
                scope: me,
                iconCls: 'sprite-plus-circle-frame',
                action: 'addModule'
            }
        ];
    }
});
