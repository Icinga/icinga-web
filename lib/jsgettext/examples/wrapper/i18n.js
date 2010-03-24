/*
Simple wrapper class to export a function (i18n) into your namespace.
Copyright (C) 2008 Joshua I. Miller <unrtst@gmail.com>, all rights reserved

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU Library General Public License as published
by the Free Software Foundation; either version 2, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Library General Public License for more details.

You should have received a copy of the GNU Library General Public
License along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
USA.

=head1 NAME

wrap.i18n - convenience wrapper around Javascript Gettext.

=head1 SYNOPSYS

<script language="javascript" src="Gettext.js"></script>
<script language="javascript" src="i18n.js"></script>
<script language="javascript"><!--
    wrap.i18n.init(undefined, 'myDomain');
    alert( i18n("some string") );
// --></script>

=head1 METHODS

=head2 new

There should be no reason to explicitly call this.

=head2 i18n

This method will be exported into your namespace, or the root namespace if you don't specify a namespace in the init() call.

There are four ways to call this method:

    i18n(string);
    i18n(context, string);
    i18n(singular, plural, number);
    i18n(context, singular, plural, number);

It will call the appropriate gettext, pgettext, ngettext, npgettext call on the backend for you.

NOTE: if the string contains placeholders, you will still need to 
perform substitution after the translation. For example:

    Gettext.strargs( i18n('one ball', '%1 balls', number), number );

This method could easily be extended/re-written to do that automaticcally,
but how the substitution is handled (using %1, or %d, or {varname}, etc)
is a touchy subject, so this is left up to your design decisions.

=head1 BUILDING PO FILES

You can extract these i18n strings with GNU Gettext's xgettext:

    xgettext -L C -ki18n:2c,3,4,4t -ki18n:2,3,3t -ki18n:1c,2,2t -ki18n 

*/

if (typeof(wrap) == 'undefined') wrap = {};

wrap.i18n = function() {
}
wrap.i18n.prototype.i18n = function(str1, str2, str3, str4) {
    if (! this.gt)
        throw new Error("i18n not initialized");

    var n, context, singular, plural;
    if (typeof(str4) != 'undefined') {
        // number, context, singular, plural
        return this.gt.npgettext(str2, str3, str4, str1);
    } else if (typeof(str3) != 'undefined') {
        // number, singular, plural
        return this.gt.ngettext(str2, str3, str1);
    } else if (typeof(str2) != 'undefined') {
        // context, msgid
        return this.gt.pgettext(str1, str2);
    } else if (typeof(str1) != 'undefined') {
        // msgid
        return this.gt.gettext(str1);
    } else {
        // nothing passed in; return blank string.
        // XXX: we could error here, but that may cause more harm than good.
        return '';
    }
}
wrap.i18n.instances = [];

/*

=head2 init(root, domain, locale_data)

This initializes the i18n stuff, and exports the i18n function into the
namespace specified by "root".

"locale_data" is optional. domain defaults to "messages". See Gettext.js for more information on those.

Example - import to class:

    var my_locale_data = {
        'My.Class' : {
            'msgid' : [null, 'msgstr'],
            'some string' : [null, 'translated message'],
        }
    };
    My.Class = function() {
        wrap.i18n.init(My.Class, 'My.Class', my_locale_data);
    }
    My.Class.Something = function() {
        alert( this.i18n("some string") );
    }

Example 2 - import to root namespace :

    var my_locale_data = {
        'myDomain' : {
            'msgid' : [null, 'msgstr'],
            'some string' : [null, 'translated message'],
        }
    };
    wrap.i18n.init(undefined, 'myDomain', my_locale_data);
    alert( i18n("some string") );

*/
wrap.i18n.init = function (root, domain, locale_data) {
    if (typeof root == 'undefined') {
        root = self;
        if (!root) throw new Error("Platform unknown");
    }

    var gt_args = {};
    gt_args.domain = (!domain) ? 'messages' : domain;
    gt_args.locale_data = (!locale_data) ? undefined : locale_data;

    var obj = new wrap.i18n();
    obj.gt = new Gettext(gt_args);
    if (typeof(obj.gt) == 'undefined')
        throw new Error("Unable to initialize Gettext object");

    wrap.i18n.instances.push(obj);
    var index = wrap.i18n.instances.length -1;

    var that = this;

    // IE breaks trying to eval this, so use new Function
    // root['i18n'] = eval("(function (str1, str2, str3, str4) { return wrap.i18n.instances["+index+"].i18n(str1, str2, str3, str4); })");
    root['i18n'] = new Function("str1", "str2", "str3", "str4", "return wrap.i18n.instances["+index+"].i18n(str1, str2, str3, str4);");
}


/*

=head1 REQUIRES

Javascript Gettext.js

=head1 SEE ALSO

Gettext.js, gettext(1), gettext(3)

=head1 AUTHOR

Copyright (C) 2008, Joshua I. Miller E<lt>unrtst@cpan.org<gt>, all rights reserved. See the source code for details.

*/
