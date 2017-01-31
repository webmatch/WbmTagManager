/**
 * Tag Manager
 * Copyright (c) Webmatch GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

//{namespace name=backend/plugins/wbm/tagmanager}
//
Ext.define('Shopware.apps.WbmTagManager.view.main.Panel', {
    extend:'Ext.panel.Panel',
    border: false,
    alias:'widget.tag-manager-panel',
    region:'center',
    autoScroll:true,
    initComponent:function () {
        var me = this;
        me.store = Ext.create('Shopware.apps.WbmTagManager.store.Property', {
            listeners: {
                beforeload: function (store, operation, eOpts) {
                    operation.params.moduleName = me.module;
                }
            }
        });
        me.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            clicksToEdit: 1,
        });
        me.items = [
            {
                xtype: 'treepanel',
                border: false,
                store: me.store,
                useArrows: false,
                rootVisible: true,
                queryMode: 'local',
                plugins: [
                    me.cellEditing
                ],
                columns: [
                    {
                        xtype: 'treecolumn',
                        width: '30%',
                        text: '{s name=propertyLabel}Property{/s}',
                        sortable: false,
                        dataIndex: 'name',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        xtype:'actioncolumn',
                        width:80,
                        items: [
                            {
                                iconCls:'sprite-plus-circle-frame',
                                handler:function (view, rowIndex, colIndex, item) {
                                    var rec = view.getStore().getAt(rowIndex),
                                        parentId = 0;

                                    if(rec.parentNode){
                                        var parentId = rec.get('id');
                                    }

                                    Ext.MessageBox.prompt('Name', '{s name=propertyNamePrompt}Property name:{/s}', function(btn, propertyName){
                                        if(propertyName) {
                                            Ext.Ajax.request({
                                                url: '{url controller="WbmTagManager" action="create"}',
                                                method: 'POST',
                                                params: {
                                                    moduleName: me.module,
                                                    parentID: parentId,
                                                    name: propertyName,
                                                    value: ''
                                                },
                                                success: function () {
                                                    me.store.load();
                                                },
                                                failure: function () {
                                                }
                                            });
                                        }
                                    });
                                }
                            },
                            {
                                iconCls:'sprite-minus-circle-frame',
                                getClass: function(value, metadata, record) {
                                    if (!record.get("id")) {
                                        return 'x-hidden';
                                    }
                                },
                                handler:function (view, rowIndex, colIndex, item) {
                                    var rec = view.getStore().getAt(rowIndex);

                                    Ext.MessageBox.confirm('{s name=deletePropertyWindow}Delete Property?{/s}', '{s name=deleteProperty}Are you sure you want to delete the property?{/s}' , function (response) {
                                        if ( response !== 'yes' ) {
                                            return;
                                        }
                                        Ext.Ajax.request({
                                            url: '{url controller="WbmTagManager" action="delete"}',
                                            method: 'POST',
                                            params: {
                                                id: rec.get("id")
                                            },
                                            success: function(){
                                                me.store.load();
                                            },
                                            failure: function(){
                                            }
                                        });
                                    });
                                }
                            }
                        ]
                    },
                    {
                        text: '{s name=valueLabel}Value{/s}',
                        flex: 1,
                        sortable: false,
                        dataIndex: 'value',
                        editor: {
                            xtype: 'combo',
                            editable: true
                        }
                    }
                ],
                listeners: {
                    itemclick: function(s,r) {
                    },
                    edit: function(editor, e) {
                        // commit the changes right after editing finished
                        e.grid.store.save();
                    }
                }
            }
        ];
        me.callParent(arguments);
    }
});
