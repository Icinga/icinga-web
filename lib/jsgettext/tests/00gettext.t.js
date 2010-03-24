new JSAN('./lib').use('Test.More');
plan({tests: 2});

JSAN.addRepository('../lib').use('Gettext');
ok(1);

ok(typeof(Gettext) != 'undefined', 'use Gettext');

ok(Gettext.context_glue == "\004", 'Gettext class has .context_glue');

