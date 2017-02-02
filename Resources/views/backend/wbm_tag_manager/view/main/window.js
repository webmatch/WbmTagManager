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
Ext.define('Shopware.apps.WbmTagManager.view.main.Window', {
    extend: 'Enlight.app.Window',
    title: '{s name="pluginTitle"}Tag Manager{/s}',
    alias: 'widget.tag-manager-window',
    border: false,
    autoShow: true,
    height: 620,
    width: 768,
    layout: 'fit',
    initComponent: function() {
        var me = this;
        me.items = [
            Ext.create('Ext.tab.Panel', {
                items: [
                    //{foreach $modules as $module}
                    //
                    {
                        xtype: 'tag-manager-panel',
                        title: '{$module.name}',
                        module: '{$module.module}',
                        width: 200
                    },
                    //{/foreach}
                    //
                ]
            })
        ];
        me.callParent(arguments);
    }

});
 
