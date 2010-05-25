new JSAN('./lib').use('Test.More');
plan({tests: 5});

JSAN.addRepository('../lib').use('Gettext');

var json_locale_data = {
    'messages' : {
        '' : { 
            'domain'        : 'messages',
            'lang'          : 'en',
            'plural-forms'  : "nplurals=2; plural=(n != 1);"
            },
        'test' : [ null, 'XXtestXX' ]
        }
    };  
    
ok(typeof(Gettext) != 'undefined');

try { var gt = new Gettext({ 'domain' : 'messages', 'locale_data' : json_locale_data }); ok(1, 'initialize'); }
catch (e) { ok(0, 'initialize:'+e); }
ok(typeof(gt) != 'undefined', 'Gettext object created');

is(gt.gettext('test'), 'XXtestXX', "test translation is XXtestXX");

is(gt.gettext('Not translated'), 'Not translated', "untranslated strings pass through");
