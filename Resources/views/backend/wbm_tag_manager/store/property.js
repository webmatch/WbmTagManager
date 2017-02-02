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

Ext.define('Shopware.apps.WbmTagManager.store.Property', {
    extend: 'Ext.data.TreeStore',
    remoteFilter: true,
    remoteSort: false,
    autoLoad: false,
    model : 'Shopware.apps.WbmTagManager.model.Property',
    proxy: {
        type: 'ajax',
        url: '{url controller="WbmTagManager" action="list"}',
        reader: {
            type: 'json',
            root: 'data'
        },
        api:{
            update: '{url controller="WbmTagManager" action="update"}'
        }
    },
    root: {
        name: 'dataLayer',
        id: 0,
        expanded: true,
        loaded: true
    }
});
