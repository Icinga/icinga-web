/*==============================================================================
Ajax - Simple Ajax Support Library

DESCRIPTION:

This library defines simple cross-browser functions for rudimentary Ajax
support.

AUTHORS:

    Ingy döt Net <ingy@cpan.org>
    Kang-min Liu <gugod@gugod.org>

COPYRIGHT:

Copyright Ingy döt Net 2006. All rights reserved.

Ajax.js is free software. 

This library is free software; you can redistribute it and/or modify it
under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation; either version 2.1 of the License, or (at
your option) any later version.

This library is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
General Public License for more details.

    http://www.gnu.org/copyleft/lesser.txt

 =============================================================================*/

if (! this.Ajax) Ajax = function () {};
proto = Ajax.prototype;

Ajax.VERSION = '0.10';

// Allows one to override with something more drastic.
// Can even be done "on the fly" using a bookmarklet.
// As an example, the test suite overrides this to test error conditions.
proto.die = function(e) { throw(e) };

// The simple user interface function to GET. If no callback is used the
// function is synchronous.

Ajax.get = function(url, callback) {
    return (new Ajax()).get(
        { 'url': url, 'onComplete': callback }
    );
}

// The simple user interface function to POST. If no callback is used the
// function is synchronous.
Ajax.post = function(url, data, callback) {
    return (new Ajax()).post(
        { 'url': url, 'data': data, 'onComplete': callback }
    );
}

// Object interface
proto.get = function(params) {
    this._init_object(params);
    this.request.open('GET', this.url, Boolean(this.onComplete));
    return this._send();
}

proto.post = function(params) {
    this._init_object(params);
    this.request.open('POST', this.url, Boolean(this.onComplete));
    this.request.setRequestHeader(
        'Content-Type', 
        'application/x-www-form-urlencoded'
    );
    return this._send();
}

// Set up the Ajax object with a working XHR object.
proto._init_object = function(params) {
    for (key in params) {
        if (! key.match(/^url|data|onComplete$/))
            throw("Invalid Ajax parameter: " + key);
        this[key] = params[key];
    }

    if (! this.url)
        throw("'url' required for Ajax get/post method");

    if (this.request)
        throw("Don't yet support multiple requests on the same Ajax object");

    this.request = new XMLHttpRequest();

    if (! this.request)
        return this.die("Your browser doesn't do Ajax");
    if (this.request.readyState != 0)
        return this.die("Ajax readyState should be 0");

    return this;
}

proto._send = function() {
    var self = this;
    if (this.onComplete) {
        this.request.onreadystatechange = function() {
            self._check_asynchronous();
        };
    }
    this.request.send(this.data);
    return Boolean(this.onComplete)
        ? this
        : this._check_synchronous();
}

// TODO Allow handlers for various readyStates and statusCodes.
// Make these be the default handlers.
proto._check_status = function() {
    if (this.request.status != 200) {
        return this.die(
            'Ajax request for "' + this.url +
            '" failed with status: ' + this.request.status
        );
    }
}

proto._check_synchronous = function() {
    this._check_status();
    return this.request.responseText;
}

proto._check_asynchronous = function() {
    if (this.request.readyState != 4) return;
    this._check_status();
    this.onComplete(this.request.responseText);
}

// IE support
if (window.ActiveXObject && !window.XMLHttpRequest) {
    window.XMLHttpRequest = function() {
        var name = (navigator.userAgent.toLowerCase().indexOf('msie 5') != -1)
            ? 'Microsoft.XMLHTTP' : 'Msxml2.XMLHTTP';
        return new ActiveXObject(name);
    }
}

