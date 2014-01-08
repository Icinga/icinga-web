// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-present Icinga Developer Team.
// All rights reserved.
// 
// icinga-web is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
// 
// icinga-web is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with icinga-web.  If not, see <http://www.gnu.org/licenses/>.
// -----------------------------------------------------------------------------
// {{{ICINGA_LICENSE_CODE}}}

/*global Ext: false, Icinga: false, AppKit: false, _: false */

Ext.ns('Icinga.Api.Command.Type');

(function () {
    "use strict";
    Icinga.Api.Command.Type.AddComment = Ext.extend(Icinga.Api.Command.Type.Abstract, {
        layout: 'form',
        border: false,
        buildForm: function() {
            
            this.add([{
                xtype: 'checkbox',
                boxLabel: _('Create persistent comment (i.e. stays after icinga restart)'),
                name: 'persistent',
                getValue: function() {
                    return this.checked ? 0 : 1;
                },
                anchor: '100%'
            }, {
                xtype: 'hidden',
                name: 'author',
                value: AppKit.getPrefVal("author_name")
            },{
                xtype: 'textarea',
                fieldLabel: _('Comment'),
                allowBlank : false,
                name: 'comment',
                anchor: '100% 80%'
            }]);
            
            Icinga.Api.Command.Type.AddComment.superclass.buildForm.call(this);
        }

    });
    Icinga.Api.Command.Type.AddHostComment = Icinga.Api.Command.Type.AddComment;
    Icinga.Api.Command.Type.AddSvcComment = Icinga.Api.Command.Type.AddComment;
})();