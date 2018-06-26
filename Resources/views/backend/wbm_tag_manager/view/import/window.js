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
Ext.define('Shopware.apps.WbmTagManager.view.import.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name="import"}Import{/s}',
    alias: 'widget.tag-manager-import-window',
    border: false,
    autoShow: true,
    height: 200,
    width: 400,
    layout: 'fit',
    initComponent: function() {
        var me = this;

        me.uploadField  = Ext.create('Ext.form.field.File', {
            buttonOnly: false,
            xtype: 'filefield',
            fieldLabel: '{s name="fileSelect"}Select JSON file{/s}',
            labelWidth: 150,
            anchor: '100%',
            buttonText: '{s name="select"}Select{/s}',
            buttonConfig : {
                iconCls: 'sprite-inbox-upload',
                cls: 'small secondary'
            }
        });

        me.items = [
            Ext.create('Ext.form.Panel', {
                bodyPadding: 10,
                url: '{url controller="WbmTagManager" action="import"}',
                items: [
                    {
                        fieldLabel: '{s name="truncate"}Empty tables before import{/s}',
                        xtype: 'checkbox',
                        name: 'truncate',
                        labelWidth: 150,
                        checked: false,
                        inputValue: true,
                        uncheckedValue:false
                    },
                    me.uploadField
                ],
                buttons: [{
                    text: '{s name="import"}Import{/s}',
                    cls: 'primary',
                    handler: function() {
                        var form = this.up('form').getForm();

                        if (form.isValid()) {
                            form.submit({
                                success: function(form) {
                                    form.up('window').close();

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
                        }
                    }
                }]
            })
        ];
        me.callParent(arguments);
    }

});
