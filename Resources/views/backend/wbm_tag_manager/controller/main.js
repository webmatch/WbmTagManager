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
Ext.define('Shopware.apps.WbmTagManager.controller.Main', {
    /**
     * Extend from the standard ExtJS 4 controller
     * @string
     */
    extend: 'Ext.app.Controller',
    mainWindow: null,
    /**
     * Creates the necessary event listener for this
     * specific controller and opens a new Ext.window.Window
     * to display the subapplication
     *
     * @return void
     */
    init: function() {
        var me = this,
            openWindow = Ext.getCmp('WbmTagManagerMainWindow'),
            modulesStore = me.getStore('Modules');

        if (openWindow) {
            openWindow.show().toFront();
        } else {
            modulesStore.load({
                scope: this,
                callback: function (records, operation, success) {

                    me.mainWindow = me.getView('main.Window').create({
                        modulesStore: modulesStore
                    });
                }
            });
        }

        me.callParent(arguments);
    }
});
 
