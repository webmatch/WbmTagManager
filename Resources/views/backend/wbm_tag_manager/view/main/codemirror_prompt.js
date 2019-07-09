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
Ext.define('Shopware.apps.WbmTagManager.view.main.CodemirrorPrompt', {
    extend: 'Ext.window.MessageBox',
    alias: 'widget.codemirrorprompt',
    presetValue: '',
    initComponent: function() {
        var me = this;

        me.callParent();
        var index = me.promptContainer.items.indexOf(me.textField);

        me.promptContainer.remove(me.textField);
        me.textField = me._createCodemirrorPrompt();
        me.promptContainer.insert(index, me.textField);
    },

    _createCodemirrorPrompt: function() {
        var me = this,
            codemirrorfield = Ext.create('Shopware.form.field.CodeMirror', {
                id: me.id + '-textfield',
                anchor: '100%',
                enableKeyEvents: true,
                mode: 'smarty'
            });

        codemirrorfield.on('editorcreated', function(editor) {
            editor.setValue(me.presetValue);
            codemirrorfield.focus = function() {
                editor.getSession()._emit('change', {
                    start: { row: 0, column: 0 },
                    end: { row: 0, column: 0 },
                    action: 'insert',
                    lines: []
                });
            };
        });

        return codemirrorfield;
    }
});