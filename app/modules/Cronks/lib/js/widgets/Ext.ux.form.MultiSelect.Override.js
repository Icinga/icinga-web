// {{{ICINGA_LICENSE_CODE}}}
// -----------------------------------------------------------------------------
// This file is part of icinga-web.
// 
// Copyright (c) 2009-2012 Icinga Developer Team.
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

/**
 * Some methods for the MultiSelect box to fix missing features
 */
Ext.override(Ext.ux.form.MultiSelect, {
	/**
	 * To set values by displayField
	 */
	setValueByDisplayValues : function(value) {
		var old = this.valueField;
		this.valueField = this.displayField;
		this.setValue(value);
		this.valueField = old;
		this.hiddenField.dom.value = this.getValue();
		this.validate();
	}	
});