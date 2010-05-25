if (typeof(Data) == 'undefined') Data = {};

Data.Dump = function () {
    return this;
}

Data.Dump.dump = function (obj) {
    return (new Data.Dump).dump(obj);
}

// Exporter System for JSAN ???
Data.Dump.EXPORT = [ 'Dump' ];

Data.Dump.VERSION = '0.01';

Data.Dump.Dump = function () {
    return Data.Dump.prototype.Dump.apply(
        new Data.Dump, arguments
    );
}

Data.Dump.prototype = {};

Data.Dump.prototype.ESC = {
    "\t": "\\t",
    "\n": "\\n",
    "\f": "\\f",
};

Data.Dump.prototype.Dump = function () {
    if (arguments.length > 1)
        return this._dump(arguments);
    else if (arguments.length == 1)
        return this._dump(arguments[0]);
    else
        return "()";
}

Data.Dump.prototype._dump = function (obj) {
    var out;
    switch (this._typeof(obj)) {
        case 'object':
            var pairs = new Array;
            
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) { //hide inherited properties
                    pairs.push(prop + ': ' + this._dump(obj[prop]));
                }
            }

            out = '{' + this._format_list(pairs) + '}';
            break;

        case 'string':
            for (var prop in this.ESC) {
                if (this.ESC.hasOwnProperty(prop)) {
                    obj = obj.replace(prop, this.ESC[prop]);
                }
            }
            if (obj.match(/^[\x00-\x7f]*$/)) {
                out = '"' + obj + '"';
            }
            else {
                out = "unescape('"+escape(obj)+"')";
            }
            break;

        case 'array':
            var elems = new Array;

            for (var i=0; i<obj.length; i++) {
                elems.push( this._dump(obj[i]) );
            }

            out = '[' + this._format_list(elems) + ']';
            break;

        case 'date':
            out = 'new Date("' + obj.toUTCString() + '")';
            break;

        default:
            out = obj;
    }

    out = String(out).replace(/\n/g, '\n    ');
    out = out.replace(/\n    (.*)$/,"\n$1");

    return out;
}

Data.Dump.prototype._format_list = function (list) {
    if (!list.length) return '';
    var nl = list.toString().length > 60 ? '\n' : ' ';
    return nl + list.join(',' + nl) + nl;
}

Data.Dump.prototype._typeof = function (obj) {
    if (Array.prototype.isPrototypeOf(obj)) return 'array';
    if (Date.prototype.isPrototypeOf(obj)) return 'date';
    return typeof(obj);
}

/*

=head1 NAME

Data.Dump - Dump objects and arrays as strings

=head1 SYNOPSIS

  var obj = new Object;
  obj['key'] = 'value';
  obj['array'] = new Array("one","two","three");

  alert( Dump(obj) )

=head1 DESCRIPTION

This module is an implemetation of the Data::Dump perl module
in JavaScript. It provides a C<Dump> method which takes a series
of arguments and produces a string that can be later C<eval>ed in
order to produce a deep copy of the original variables.

=head1 SEE ALSO

C<JSAN>

=head1 AUTHORS

The C<Data.Dump> JavaScript module is written by Kevin Jones <kevinj@cpan.org>, based
on C<Data::Dump> by Gisle Aas <gisle@aas.no>, based
on C<Data::Dumper> by Gurusamy Sarathy <gsar@umich.edu>.

=head1 COPYRIGHT

Copyright 2007 Kevin Jones.
Copyright 1998-2000,2003-2004 Gisle Aas.
Copyright 1996-1998 Gurusamy Sarathy.

This program is free software; you can redistribute it and/or modify it under
the terms of the Perl Artistic License

See http://www.perl.com/perl/misc/Artistic.html

=cut


*/
