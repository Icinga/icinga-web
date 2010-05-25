new JSAN('./lib').use('Test.More');
plan({tests: 3});

JSAN.addRepository('../lib').use('Gettext');

var json_locale_data = {
    'messages' : {
        '' : { 
            'domain'        : 'messages',
            'lang'          : 'en',
            'plural-forms'  : "nplurals=2; plural=(n != 1);"
            },
        'test' : 'XXtestXX'
        }
    };  
    
ok(typeof(Gettext) != 'undefined');

try { var gt = new Gettext({ 'domain' : 'messages', 'locale_data' : json_locale_data }); ok(1, 'initialized'); }
catch (e) { ok(0, 'initialize:'+e); }

ok(typeof(gt) != 'undefined', 'Gettext object created');

