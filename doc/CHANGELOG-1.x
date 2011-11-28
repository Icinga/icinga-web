################################
Changelog for versions below 1.6
################################

2011-09-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: eada8e9f69dadd33b4c43954f2352a4819d9d02e

* REPORTING: Readable error messages when JasperServer is out of reach


2011-09-15 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: fa95e8cb583f6c35b8e4bd73af16a86b3a8e956b

* rework icinga-web.spec for all the changes in 1.5.x, use /etc/icinga-web and 
  /var/log/icinga-web* Thu Sep 15 2011 Michael Friedrich 
  <michael.friedrich@univie.ac.at> - 1.5.2-1- drop icinga-api dependency

* - drop BuildRequires - not needed at this stage

* - add --with-api-cmd-file, using same location as icinga rpm 
  %{_localstatedir}/icinga/rw/icinga.cmd

* - change new config location from default $prefix/etc/conf.d to 
  %{_sysconfdir}/icinga-web

* - mark all config xmls as config noreplace

* - set %{_localstatedir}/log/icinga-web and use it instead of $prefix/logs

* - set apache user/group to write logdir

* - reorder files to be included in the package


* fixes #1871




2011-09-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: afab590700f8fb64dcd46b77d4eb5c2917fbec49

* Changed column label for command
* Removed fixed database from sql upgrade


2011-09-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a84b3ac85dc17122bc307e4298d85ef405462db6

* Updated changelog


2011-09-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 64c1f540c8ea239e62960afeba694c2402483d71

* Prepared tagging of 1.5.2


2011-09-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5b089f51b355314aefd8dfde5f24d064d7aa08bc

* Changed default cronks to run icinga classic items on same server


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2ab50bf4758dc329ba986df386f187d4c4dc9454

* Patched doctrine to use foreignId, added service notifications method
* (fixes 
  #1836)


2011-09-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 52f3c2319eb8e3cac38b7a68ffd7799d11628461

* CSS fix for to's


2011-09-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d89a0632581ff9a9e1af075ae1382254d7a097fc

* Fixed bug for status map model


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 62b9b7686c763dc8dfe4f2f9590c8a12e945bc83

* Added sort and grouping persistence


2011-09-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5ab7b92a1bddb70f331d37246a0385fc15d6b288

* Prepared notification template issue (ref #1896)


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9467784b058c836c24e602c42fc0f9200cb04eb9

* Fixed configure using NONE as path for config when no prefix is given


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 456456ff53020df9712336977031691e9abf3b04

* Added sqlite schema


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f26ac530ef4aec9e62c4bda5a83727f2781d6be0

* Fixed http-command handler


2011-09-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8d5867d86cc54f94e6ddfac2cbf355a1a5f68658

* Re added +x for dev tools
* Fixed fuzzy translation error


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d0ac635671bc58669bc2c920239575ba50007567

* Removed DoctrineSessionStorage, fixed Api principals not being
* recognized, fixed 
  navbar not escaping user name


2011-09-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 69e043909fd33cf9ca4a29f5f6ccb0c7d7c946b2

* Removed swap files


2011-09-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 97fc2cb73d4501049f51d8b4030ff683194b7a0e

* Default localhost for reporting (fixes #1894)
* Fixed clear cache console version


2011-09-13 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: fe7ffca713eb72080469b5febe317d8eefec63eb

* Fixed typo in DBALMetaManagerModel which prevented connection binding
* to the 
  logEntries table


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0228a17b27c2043cee727d10c61e5f3ddd03f6f1

* Expanded email length to VARCHAR(254) (fixes #1784)


2011-09-12 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0f7b407d8a0d3d802bd08b68fb35820c2fe052b1

* Fixed executable check not testing for filenames (just for symbols)


2011-09-12 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0074f76ceb2db81442aa454cb3aa8c2c0cedc8c6

* Fixed Typo in relation, fixed '*' not being recognized when appended
* to alias, 
  fixed AppKitSplitValidator crashing on array input


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 41bfe35ca59ccf2d5d5108da9b88028403e160c9

* Added host check commands to open problems (ref #1892)


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 550f844bce6e9b3513ba7e712cbcd53e6e95eb5f

* Merge branch 'mhein/default'


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4f28bf6987821f34d21962e599db91438b0d46a0

* Added missing commands (fixes #1812)
* Consolidate ArrayCombos for commands


2011-09-12 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ac29b5fd2e62faa1a7ca77051479dd0d62bcbda6

* Removed uneccessary exec flags from several files and added missing +x
* to other 
  executables


2011-09-12 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 40ae013e3db936f8ef08acb1d861c903ad66f315

* Added hideDelay(refs #1842)


2011-09-12 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ac3c196d834c994cedae760bb42b86504cd6d844

* Added notification target (fixes #1891)


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2ba1e4978ee4668c4467b237d22397b65f7c5248

* Removed contrib/Skeleton


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 650f9d28da673b468e00a0738b20c6e7be37daf6

* Fixed console clear cache


2011-09-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3fcfc23e9b80c3e2500a227c7b8f4c7c030fc167

* Upgraded phing version to 2.4.6


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7d48dd246e643d9656868b9f12a5be48106cd929

* Removed unused additional index from contrib folder


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 38f77074776ec6a82f0314d5e09ef4917e8da694

* Fixed TARGET_LOGENTRIES relation


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c5a00d24f506e09aa2c631afeea51473028c4f30

* Added default cronk image (fixed #1588)


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 14da124fad2ab084a2a250be7b99ca9519fb3288

* Moved etc/contrib to contrib


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f0e7e3f8f077a5fb24086b3113331c260db58cf4

* Fixed conf and log options and deprecated marker (fixes #1876)


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 57e54b4db88acef87ea78227d1a43e58697813cb

* 500er error on note_url (fixes #1881)


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7fc1a172296a0dc067472d93fb2f3271c8876c4c

* REPORTING: Fixed service search field (fixes #1873)


2011-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4bc5e6286d87c95dfd621027b736c12f5544a8a1

* Reporting: Finished scheduling mode (fixes #1872)


2011-09-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9977a326363bfb36156f9ee0023a1a5d056786f5

* REPORTING: Scheduling Feature - working stage


2011-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ba0152e18543cc5216270e82afbdafe7948e75d3

* Fixed typo in relations


2011-09-01 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a07a3afa09f452f2141a8e079a0208d1aa93650f

* Fixed missing hosts xinclude, fixed Uncaught PHPError when command
* times out


2011-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 371f7a6a8306c72db0336539f14cb6455a7f7e3c

* HTML check_multi patch applied (THANKS to Philipp Herz)


2011-08-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a7e7b845fb2fd456eb308a0c8c3573675cd9933e

* Merge branch 'master' into r1.5


2011-08-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f293746cbf8fb704b5011d3b479ca83654f57ae8

* Better way for info icons: Readded object info icons (fixes #1854)


2011-08-31 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 54ceb7d8a539c6b8e10469a1b5a08a2b133e24f9

* Updated REST commandhandler


2011-08-31 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 723d75bb05be056e04e09d479772ee1c0dc481b1

* Made web_cfg_path absolute in siteconfig, removed oracle debug
* settings from 
  index.php


2011-08-31 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: bfa780f27b45c16d19fdbd6f760f96be00caeec9

* Fixed wrong contact alias name in api


2011-08-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 71e30e080730ed52c5c4d07d9b6bb2ede962c54f

* Fixed PHP syntax error


2011-08-30 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 84831d30aaeb7ed50bba0a9aa94b4d84d39f1ad9

* Fixed trailing comma of dead that crashed IE


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f7b70e7f32bb7796357649ef1c2f9772cfde4d2e

* Fixed principal caching error that prevented some principals to work
* correctly


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b40ff1913e2a78aeaea1370d8062d24ac3a584a1

* Fixed relation issues causing search to craash when used with
* principals


2011-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2e64f9a9e822e607362ae16963bdb88c9678a61f

* Changed meta data


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: dd70358ef51fc7838f8367fedd9a1b14bcdb1375

* Fixed installation issue and set fallback timezone if none is provided


2011-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e1cdb8b3fad8665b428a1c2bfc7b2cd57540e574

* Removed test module from context


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e12c66c36749dbc4a535a610a6e8acf97076f5d5

* Fixed principal inheritance


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8027897c133a9882c6f51a2b30a3e50a64550fba

* icingaOracle fixes in Doctrine, fixed outdated route in group edit


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 51f9a755d0b6924f7c1d8456122c3d006f1e4100

* status map now hides layer if an error occurs and doesn'T freeze interface


2011-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 35e4ca340cecc990e16f23fca3cebc8dbfe510c0

* Fixed wrong url in custom portal


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: dab5124eed2473b5b56d34f49c164f54e7385ff5

* fixed status map


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d25bada97c563fee9a4fa565aeb83d552fd4c94b

* Fixed command author name being cached in template, added hint for timezone 
  problems to translation.xml.in


2011-08-26 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: 5572c3ebfaf8851833e13b929392e252a20ae37c

* Added missing semicolons to critical places


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: bbeeef5239fd218db9d9978f51754d76f47d6009

* Added static join in host_status, should fix wrong state information


2011-08-26 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 5aa8ce2393eba6f15fd88d33894122ec8808d160

* correct retained config_type selection in doctrine legacy layer, add description 
  how to be used


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0084ed2fb1c71c54cf298adb2cd28715e302ad8e

* Fixed flipped retained state selection


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3ef22e8ff499c02701fb8dd7980521d010938fb1

* Cleaned up default configs (comment separations, default db settings)


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d2b945cf75ea2c1a256f1866aafefc9b994f78a5

* Added field displaying current servertime to indicate timezone errors to menu


2011-08-26 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: 716876c8cf6784bf02b2806ae84e01402dc886b0

* Fixed AppkitResourceConfigHandler possibly including same file multiple times


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 92d59b76bfbd79e207b11bf6b474c18d64a62a45

* Fixed log dir not being created


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c360030799df9f5f93ce9bc969a89bd0336c2ada

* typo error


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 70300b073cc71cfd2daf269e432921b84c395b66

* Changed www-user error to a more general version


2011-08-26 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e60273a6dfa1b94fc09c6a39a42dd4d0feca8736

* Command sending errors now are more verbose and will be send


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 11811e73c6229548c0ecdb6c6c6af586254e690f

* Removed deprecated site.xml link


2011-08-26 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 127d41fa74dff5af1c04569fc427f0235db7e076

* Readded default.timezone and added default timezone set before agavi check


2011-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b1fed8f55cfd5c426074ce12cf4e3a3270441a95

* Export reporting cronk configuration (fixes #1846)


2011-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ecdfddc01ee68666fd99dce48c83f22c8134ef7c

* Export reporting cronk configuration (fixes #1846)


2011-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fd59b848a36f17ebf4d628f32be740edb20d358f

* Removed JS files from PHP syntax check


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b8037f4e0133ff5e4dd60c4abf9b221bba39946f

* Added use_retained to database config


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6401e51fb9561327fef8acd5715673aff3b0198a

* fixed install paths and configure option regression (fixes #1851,
* fixes #1852)


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8dcae6ad8bcbf232b116c3569ba41fd98e61752e

* Wrong comments on access.xml


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 55ee11bb6cf559ed880ee4b69634c0e1b53c5268

* Fixed access.xml from siteconfig


2011-08-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8b0cff16018726ad0ae4884b8ed8f8187f0c08be

* LDAP fix based on doctrine bug?


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2f66fe0d5f2f6d55161340b7d3084d960406e960

* Fixed active/passive performance view in hostgroups-to


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 4f31d2310dfa722d4e0b10e430f3857d5ec9c274

* Fixed aliased count fields in legacy api


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8a0b76a7ad6f6f0aa2bee2fe05ba1f7aa8a494d7

* Adde missing alias


2011-08-25 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8c48ca9b13b079365e5651c3c82596b85732ec2a

* Fixed status map freeze in some cases


2011-08-24 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3f0aae0cdb8b6ec24c6a22bd9ddad07d68d0d006

* added databases namespace to example template documentation


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ba34b991a9b209f1f1015f38121dfc68e991ee4a

* Fixed cronks including


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 09d771ccac146bcd50dd43c40cfb663e74191190

* distclean fixes


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 34fc5778033311654f9c5ae83133fa2a47ac071f

* configure fixes for finding binaries


2011-08-24 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f34354e17cf77143cb8192d29a31bebd34e3dcac

* fixed log, fixed data overflow on wrong ssh key


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5d82ce5f1b2a6c5630da56ba575e094f8f3009a6

* fixes #1839
* removed configured files
* changed gitignore to match configure output


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6da75677485ec753451416ebef578329da7b5748

* fixes #1839
* removed configured files
* changed gitignore to match configure output


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5fe4c2e902d4a48131e4c0d23ed8ac004d3a2acf

* fixes #1839
* removed configured files
* changed gitignore to match configure output


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b1693d94abc3224eb48d80af8dde4b0c04fb5474

* Changed meta things


2011-08-24 Jannis Moßhammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 17a828b235439ab7a254170b3de99410987478aa

* update in contrib


2011-08-24 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c16c697be83183b13f7cbd6bffb97cfb91f5bdd5

* updated api fallback route name


2011-08-24 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a245ca5411bef008cf63b0e0ca68cbcfaa39cd85

* Updated contrib


2011-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 54697b206ed552073c6b30cee0aa6ef96bdf0749

* Conform view for Instance status


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0ac2ea3a7c3b82366fffbf73d26d78684f5fdfc9

* Changed submodule pointer


2011-08-23 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3eb3c6e944e7e841cc2ffebebe4e30e0c79768d1

* Fixed missing default conenction name in DataStoreModel


2011-08-23 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b23af03c303a095fa2baf146eb91d7ae68c53b0f

* Fixed IcignaDoctrine_Query auto-alias resolve bug


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e8cdfb8fa87a3abd6fb470625ae49c7ce2db4331

* Updated changelog


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c31dca14e6622117d429b5e6bc5ed11008f16260

* Changed meta things for 1.5


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b90f058bc5479a89c31d8cc7f22dffd4842e982c

* Added persistent flag for add comment in api (fixes #1437)


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b03ad65f1449f65844ada76f7412ad741aa59086

* Allow selectable text in grids (fixes #1612)


2011-08-23 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 383ed49e1abcbfe7b3be12ac6286ec98739cf80a

* Fixed scheduler, updated translation.xml to automagically check for Translations


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9ec99d028e5bad5f5e59e64dd186b90868b2392d

* Fixed #1646, ColumnRenderer encodings


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9800229227016000cd295c093e44a5acce324063

* Configurable app name (fixes #1648)


2011-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 75c75bda35fc9e8e73801a939aa7434e82793598

* Added view for instances and their status (ref #1821)


2011-08-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ec8f1cd502873332878e9cca9372142506b59321

* Added mini instance status (fixes #1821)


2011-08-22 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: efdbebba2ac7cbed8743b9b52e45a4db1faa2fcf

* Fixed db-initialize complaining, fixed log to respect dynamic path, disabled task 
  view


2011-08-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8e8032ae5b2d6742bcc3aa764148f9d418361955

* Added new OverallStatus JS implementation


2011-08-21 Jannis Moßhanner <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ee1183e8e6c16bb79e7b62160d74997ce8d7c9eb

* Added DirectoryMatch to allow proper access to module ressources


2011-08-21 root <root(AT)linux-7kv8(DOT)site>
           Commit: 7f0d70c0167a5300e196ff1e30e5de8c41873554

* Fixed sql-issue in status-summary and missing quoting in legacy filter


2011-08-21 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6868938645cdb8764d621edf21ebcca986a43167

* working state module installer, not used anywhere at this time


2011-08-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7c203eb0fba3c02f42d5b646ba30ee0217ca3a80

* Log dir fixes


2011-08-18 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 237841ae9c793aad5b9edbd618ae19b26e102022

* Fixed context awareness bug in AppKitXIncludeConfigHandler


2011-08-18 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3187fb3cb7e0a65083c345ae30165ac0977800b0

* Modules can now be disabled (altough menu,etc. will be displayed)


2011-08-18 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 04041d094703affe486eba09cb061639889fc03e

* modlarized schedules xml, created SubSettingConfigHandler that allows to create own 
  setting-xmls


2011-08-18 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e614b440ebc2b96e5fa794c4a9bd5b1c4769f96d

* Added AliasMatch for public module resources


2011-08-18 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 1592dda2f166cd28246ac6dab45709ef2931dde1

* Removed initModules from icinga.xml


2011-08-18 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 4340b033864f9ef6c843aefbd7600117a1549e53

* Removed access.xml from modules and put it in a own xml document, added 
  ConfigHandler that enables modules to add access definitions


2011-08-17 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 4e5117487f04ae933a98c9ff76c7c2ff8f8ec431

* Fixed typo in service-tempalte


2011-08-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ec9f83f00d1df3f8653c2ab17e208e9fa6dc40fe

* Changed version to beta
* Resize bug in reporting cronk (fixes #1822)
* Fixed fuzzy 
  translation


2011-08-16 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 14ab31e5165a3ce9d426805dc74fb27b3df4b067

* Fixed clickhandler. added column extensions to Cronks, fixed icons missing in 
  column selection, modularisation fixes


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8aee5b7b7a1dd0958a418abf5f8eaa0c1b1b3f67

* Removed template based refresh and pagination values (fixes #1817)


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f4c6b7cb49f2b70fc9c54b55aae011991f53e006

* Fixed drop action


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f044a9fb1773fb604a41903294f56bb5f27c1dc7

* removed with


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fd708b05ee5eb3dbef85418079abf057d8666130

* fixes #1816


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0ee43ad1d86246b0e023865c84d1bc80058b2b34

* Added PHPSeclib implementation instead of libssh


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b81df97245e6d5f0ebf424d672dae20c4f16eec3

* Installation fixes for Reporting


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0db32fcc3c28650ace628913ff7ec360b615fa12

* Install fixes


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 83135a1510309545344214f36c7015a39706d348

* Added session-caching for principals


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 53bec97d8c9a6bc89a2300a7a62b61942cbd28a5

* Testing starts now only from make (fixes #1814)


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c09e3459d0cf1e893b607666f5dd19ac877a685d

* Search now stays open on click, services are displayed on host-click (fixes #1815)


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3a246a65b5101c90da767429e856f5da41e96b94

* Excluded disabled hostgroups in view


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 845cb9c46c7c164d3a6c2ea4ac7675359eabd9b8

* Changed menu route for about


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 31425cd7f79749c54870771f8d19e162e0b2f3d4

* Due to a bug in doctrine, CronkGridTemplateWorker retrieves data now as array


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 97a58c7b631b4cd77bc0dbdcd9abf9a8e83c85a1

* Moved js action to squishloader action because of etag handling (fixes #1813)


2011-08-12 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c7fb653344bec7df527f3d87b39539f7d8917a4e

* Added host_id to default sort in host view to prevent hosts not showing in 
  pagination when they have exactly the same check dates


2011-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 37086c14ec98405ff6e4ed9e9067ee99c0f2efa4

* Applied new translations from pootle (THANKS TO ALL TRANSLATOR
* DOING THIS GREAT 
  WORK!) fixes #1811


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 7ea3adec657c8db4ee99884d87e062ba4a304815

* Added icinga.css


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0f4bc9f5e198caed71ce2c42c33b6c3bc64f7325

* Fixed tyepo from conflict


2011-08-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2254006f093a876db565d1b0e71a8c0e2439883d

* Fixed small overview cronks (fixes #1808)


2011-08-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7ac3e10e7867f2e52233c9f914f7055e14349f08

* Fixed config handler issue (sub module include databases)


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0b9e08e328e30bfe0cc4cf378968c2024dda5572

* Added failure fallback for IcingaStoreTargetModifier w/o columns given


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: dbd53a5d77b3d4e9ce8ca22f0154abcc439a2406

* Removed duplicated cronk loadings on start (fixes #1804) and added open problems 
  summary to hosts (fixes #1803)


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8d51b9907cad0004284a32f6a8db6fec9afc5efe

* fixed default columns not working in search


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d37522aa6a3d9163d64e99c6298d0471ddcaeb7e

* Fixed wrong variable in check


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 634bb915dd45dc075cf8c65b82d6cd6f61334b80

* Made api more tolerant, added slash to folder definition in logging.xml.in


2011-08-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1417c30a4274370323cbf38ccfd005be1deec91a

* Applied coding style (ref #1536)


2011-08-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 04c7fc79c915b8a23399877b582ea36950a14a73

* Added astyle config


2011-08-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 114909bda538d87054150c6d9e336af013e8ebfe

* Cleanup pt2 (fixes #1530, fixes #1532)


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 1e365827d8170eb52ea407f9a9cbf4c244658e48

* Removed timezone conversion in translation.xml.in (fixes #1733)


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: df15c0adb0699e65f88a763a3207848ca214747c

* Fixed makefile type (fixes #1801)


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b15dbc475e6ebd58ff4d99eb31d5b8f1870e6d6d

* Fixed appkit.admin routing


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 85e2347856326bc1afca669c7d71dd642b2a5e69

* Moved config files to etc/conf.d (or --with-conf-folder, fixes #1708), moved log to 
  log (or --with-log-folder, fixes #1714)


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 87ccfbbd675e3531e001e8bf9851838490c89f98

* Fixed css typo that killed the layout on non-WebKit browser


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ad9d4a2d2bc6d867d019eb076afe65bdeeeb02cb

* Removed squishloader from configure


2011-08-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b45f3fcca2ff17287aa92a73261061d954b42e7e

* Added caching for template xmls, removed Agavi-based Squishloader cache


2011-08-10 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: cd499a264283031d8d7ce6581519c132b4451361

* updated gitignore


2011-08-10 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ceb1596779b58e49c578b96c12c60fad67992361

* Added unhandled host/service problems (fixes #1614) added lazy loading of comments 
  (fixes #1806)


2011-08-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0a0d70099ea634a91609f0ca74d08fea17759e70

* Cleanup AppKit library (fixes #1531)


2011-08-10 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 83ce3e1f33791bcef8f91da5c0fc82415771da30

* added configure flag to squishloader cache


2011-08-10 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 64a401b1bbee3e886f68a2f08d866adcf05556c6

* Fixed old IcingaApi definition breaking ObjectSearch


2011-08-10 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: eaaf53595e21e56fea694099ee67164add8db3f2

* Compiled doctrine, added config-property in module.xml for using the compiled 
  version, moved Doctrine-specific changes to
* own AppKitDoctrineDatabase class and 
  added caching configuration in databases.xml


2011-08-09 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 64ce97f7166f58b4ae715c7a2d93842f5fef34eb

* Added custom caching mechanism for squishloader (uses ETags)


2011-08-09 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 830e6dfb363a1a70f7d8d84a4928a5df3de50134

* Updated stylesheet for menubar


2011-08-09 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6d783075a6d2e0c0b08defa3093d254f4a03e6be

* Forgot config_handler.xml changes


2011-08-09 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f231d82838c1c2f53d72e7203a92817ee6392628

* Rewrite of menubar, removed events, added AppKitLinkedList (that is used by the 
  menu), replaced DoctrineSessionStorage with simple AgaviSessionstorage
* which should 
  be a major performance improvement


2011-08-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4be5ffb1168a05f478f23958dbe8970fe53f9ca2

* Fixed simpleDataProvider URL  (ref #1548)


2011-08-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e295e8cbb6632d734c461010e30d4b77bce3d86e

* Removed global routing space and moved everything into modules (ref #1548)


2011-08-05 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 062a430bd0bd9a52f6df8f83f32e95268d4b7e35

* Removed initializeModule from index and added AgaviAppKitContext to compile.xml


2011-08-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5ed55109433a6bff2098c6ab78d204438bbaf503

* Added agavi javascript actions to javascript config handler (ref #1548)


2011-08-05 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: bca31028bf9f15837ec531ca7e5581cd1b55ea03

* Added dispatcher routes to Ext.Ajax and Ping action to avoid false-positives on 
  server-down check


2011-08-05 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f9551a52bdf4216609a6c35c6184cb4eba26044f

* Added (HOST|SERVICE)_PROCESS_PERFORMANCE_DATA to target defs


2011-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 93340db0a41ef7019e0f42e086c3bf130e15cd7d

* Removed the module util (ref #1548)


2011-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7cde0ba5d0cc3e90ed3ec19d6f149f4e01281517

* Removed the javascript dynamic loader (fixes #1534)


2011-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2d4765aa53ad2ff608e3f359be1de138e655d061

* fixes #723 cookie for login credntials


2011-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: df06e9da59b6b6b145299ca1045765c10faa5149

* Removed some php5.2 errors


2011-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ab3898c2e8ac03cac5697ca264f1a763c1809b42

* Finished merge of new module based configuration (II)


2011-08-02 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 233548439d054cbd6df4cedc9cf23bf3a6d2ae59

* Removed PHP syntax error getting contants from dynamic class


2011-08-02 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: acd2c80e49af713c71c8e2039843c6bada688a86

* Finished merge of new module based configuration


2011-08-02 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b28a702010a6e56e350f8814359ba8c845af27fb

* Fixed typo that crashed php 5.2.x


2011-08-02 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 946989ef09a0b83f83da7386cdd692c7e6e4e462

* fixed xmls


2011-08-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3960125883ba4c04c31a60b383c95edf9741b254

* REPORTING: Fixed merge structure


2011-08-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 947a54b11562213d144bd8cc6bdc13e6bd5772ab

* Merge branch 'mhein/reporting'


2011-08-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 15fda344c68755188d15724960e8598bd25f6c2a

* REPORTING: Ready for release


2011-07-29 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d64ada4a77f391b5af86de2826cd70a8a3f024e1

* Added logging for queries


2011-07-29 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ba4812274a9a95704f708d01a5b80c14e1699518

* Fixed installation


2011-07-29 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0cef24db3a27db984c03ca5de300271e90416249

* Fixed relations, cleaned up configs/vim swp files


2011-07-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ee10e9c5308838ade042d7cbcf72926a649b8ed6

* REPORTING: Work in progress


2011-07-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 324dee03b867c1ec8f8193637e4ee2a2346ca9e9

* REPORTING: pre merge


2011-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3fa3e1f2ffb8ea44c0bbbd241c0f55ddaf4339b3

* REPORTING: Working stage


2011-07-27 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a0c6a7217055771f94d0707cbcf06d2b97f985cc

* Updated configure


2011-07-27 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2dda181f3c7a39fb309a8d046ffd83abd283a9f2

* Removed the icinga-api dependencies and created Commanddispatcher, several fixes in 
  relations


2011-07-26 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: ededf0ecb72e978e79845931e98a85fe66977936

* Fixed AppKitResourceConfigHandler using values of directory iterator's array 
  instead of keys
* Added try catch to missing xincludes
* Allowed array syntax for 
  pointer definition of AppKitXIncludeConfigHandler since Agavi is not capable of 
  multiple config handlers for one file (which I assumed)
* Dropped second css.xml 
  handler and updated the remaining onerefs #1548



2011-07-26 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: 1414202fea679cb3d12136425d048192c140c7db

* Removed squishloader.css route
* Added styles.css route allowing url(../images*) 
  for module css filesrefs #1548



2011-07-25 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: 75b3960c914997e200a2861f3519d2da2ed3ab38

* Added css / css_import processing to AppKitResourceConfigHandler
* Moved css 
  imports from module.xmls to own files
* Revamped AddHeaderDataAction
* Dropped meta 
  config from module.xml but added to template
* Dropped HeaderData modelrefs #1548



2011-07-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4cca607779bc2b01827328cfe5d69536bd598ee3

* REPORTING: Working stage


2011-07-22 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: b84cb11563f8eeee7c36baa604af52a02360d109

* Removed debug print_r


2011-07-22 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0cda6e9a64f8838957727288dfaa9eb5e3e30265

* Several LegacyLayer fixes, only statusmap query must be fixed


2011-07-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 236d38a6500a54001b9b0331a2018c6207059c58

* REPORTING: Fill scheduling form from soap request


2011-07-22 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: e2b09a1a829a0eb6cede62e77cc0ed1788147823

* Removed AppKit BulkLoader from autoload


2011-07-22 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2a21e1c2e56e20c2a1344139b240c506d4924abb

* Fixed parent_host relation, fixed LegacyApiQueries


2011-07-22 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: acafaf090f89115ae1aeb2c2036866f5223055fc

* Added cronks.xml from mhein/testing


2011-07-21 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: ffbfae93473aa5b5ba2b6fe24792edb2779fa428

* Added AppKit_BuldLoader model, replacing class AppKitBulkLoader
* Added minify 
  tasks to loader
* Adapted SquishFileContainer model to use the new model


2011-07-21 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: d5fadaf517c092cece8aefb69f2f96dab1de08c1

* Added AppKitXIncludeConfigHandler which includes module config files into their 
  global equivalent respectively
* Added xsd/xsl validation/transformation for configs 
  handled with AppKitXIncludeConfigHandler
* Added AppKitResourceConfigHandler which 
  collects modules' javascript and stylesheet (not yet) includes
* Added 
  AppKit_Resources model holding those includes
* Outsourced modules' javascript 
  includes to own files
* Adapted global config_handlers and SquishLoader for the 
  changes to take effectrefs #1548



2011-07-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 61811b516d3eae06d5928d9e1de0b44eb7275687

* REPORTING: Rewritten the constants handling of jasper
* REPORTING: Started 
  scheduleing implementation


2011-07-19 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: da5f844b920f715538b6286eb9387a1e8a7b1882

* Current working state


2011-07-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1e4f122502d3f4a0e63481e683fa8b20382a102d

* Added global temp directory


2011-07-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f771adeb410e4fef4b78c6946190337f87133be7

* Fixes grid filter (fixes #1564)
* REPORTING: Resource view ready now


2011-07-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b997f182d8d9dbab15027560dc7d22be1f6771a7

* REPORTING: Add resource tab panel
* REPORTING: Added preview for resource data


2011-07-15 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 50f0021f64e5eeacec66e8d09e84c8c803307c8c

* Changed Icinga-Api functions to be able to call doctrine based legacy layer


2011-07-15 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0a6fb326da2093c432ab6d3be9ef0d04964fa2e4

* Added grouping functions to query, LegacyLayer only needs filtering to be 
  finished
* Fixed some relations and made the alias functions more comfortable *


2011-07-15 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: 10771dd11a042dc07c69e81f67baa9bb27231f4f

* Added folder creation (app/cache/config) to make devel-inplace-config


2011-07-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a76918047365a6428023960fcc59096410a84f1a

* Reporting: Added missing files


2011-07-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fcfffda7c00b875cea9f0c0fff2c290f42cd879d

* Added fix for filters in grid


2011-07-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a619b8150ab1793df15724863d5c1879c5821910

* Reporting: Added multipart soap client
* Reporting: Changed multipart 
  implementation to new client


2011-07-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: dc55be9e9576c33d4bc566c4dd2355914b403566

* Removed working files


2011-07-11 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 91719fbe0d788fdd91df3ab653a966c49469fa58

* Fixed some relations, added raw setting of all icinga-api targets


2011-07-09 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d1952ecb756691394b9ad8408546b3fdba0d8b4d

* Created custom adapter for oracle and fixed several model issues NOTE: The 
  icinga-Api should now work in oracle without any changes in code. The driver name 
  herefore is icingaOracle://... and it reparses the sql prior to executing it (it's 
  not very nice but the cleanest way to realize it i found - and I tried EVERYTHING :) )


2011-07-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 39ca1234dda8eed4ae908133f009a195579c9cd6

* Reporting: Added repository node
* Reporting: Better tree, icons, qtips, tree filter


2011-07-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b6e9eb9a3a4960b8c4127118efd460bb4d87424a

* Fixed "SilentAuth" login issue
* Disabled silent user could not login
* Fixed 
  session creation for silent providers


2011-07-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 867f5803edccd14edb2821deecbbc69d6669ed20

* Oracle fix for editing users with principals


2011-07-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 426e2a1932ae680708491e5753414d306a00d0ea

* Rewritten report form handling in JS
* Reports: Added preview option with html 
  reports
* Reports: Better UI integration


2011-07-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 261fad312db6a7c6b749d3ecf54531d2c62267a6

* Added oracle fix for deleting cronks


2011-07-05 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 458841f1c52248e0cadb64110530a75827e80bd5

* Extended Doctrine to support n-m relations without being forced to use the primary 
  key


2011-07-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 314a00e3e50bfd23125c13d1f7304aca0e6bb5fb

* Added additional output types
* Better parsing of multipart response
* Added HTML 
  embedded images for HTML export


2011-07-04 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: eaa7cad278aba9baa45f30254c3fd617626c8b9f

* Added additional relations to doctrine models, started LegacyLayer for Icinga-Api 
  emulation


2011-07-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 87cecf940224ff860bc57bf5a8a1ea27e0cf5115

* Fix for FF4


2011-07-01 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8a7438cef1b4438e7d26d3deb64abe3ac7d76e38

* Added a bunch of PHPDoc tags to store/data classes


2011-07-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9a527a0ac831835774afc838d96e848b4a2de9c2

* Bugfixes, reporting and session only for PHP5.3


2011-07-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 37db20f458a06b794c2fb6b71fd0b3ee92c98686

* Working stage final


2011-07-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 39565af64ff200e4838dd585bdde08830291836e

* Working stage


2011-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fff6a8c5619ad1495eb6977a94b33e5173acb393

* Working stage


2011-06-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8c83c561bbbc126a68e684f5f46f5961c521e1a6

* Added initial reporting module


2011-06-28 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ff3b392bd99ca4656e84f24fb4edfb39e084d6c5

* Updated tests


2011-06-28 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: aa37619ca5d56792c95e34da888bab17ea8e786e

* Grid concept works, needs some tweaking in the filtering, but this can be handled 
  when it will be implemented in icinga 1.6


2011-06-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dd2791f409b1062f41092892d0b009f862cf7700

* Provded oracle fix for session garbage collector


2011-06-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a216431bb4af0d8b1c927d1c73f32747e42ab579

* Changed clear cache to agavi way


2011-06-26 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 7d63bb602f19c8e74ec48b69a00cf4fb5f4f27af

* removed debug output


2011-06-26 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d30255cec126fbe68d40a2412fd014deeca08383

* Patched agavi exceptions and classes for doctrine connection


2011-06-26 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 1b05582ef962cb22f983ece80d7c38b370eead35

* Added IDispatchable


2011-06-26 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ec319694bd9e9f7c7f85843600f31b1e8c4cb22d

* Added JS functions for frontend


2011-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9ab4e4f3f4ae24b0472761181fdbc2416f3ec069

* Provded oracle fix for session garbage collector


2011-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 25d93fb5c7ec1d60cb691272c1381d71748a3030

* Provded oracle fix for session garbage collector


2011-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b6da33ecb4e13e4f9cbf835cdc51194ac60f9882

* Fixed translation issue (fixes #1671)


2011-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9f6bb962827c5a6971c12485532792780171a4bb

* Upgraded to agavi 1.0.5


2011-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8832c25ce321bf387f3e43452bb40504841a0f8c

* Added icinga-web db oracle default config option (fixes #1284)


2011-06-20 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: eec2600a341e7fc8cabc6260f14c375618d78c02

* Almost finished server side part of api creationJ:


2011-06-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a46b9e729459117997d80c1df973206ddc713df1

* Added module based translation and tests (ref #1548)


2011-06-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5f3808d381823618fe69ea7692d2227153a690e9

* Renamed AppKitRoutingHandler -> AppKitRoutingConfigHandler


2011-06-14 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d33e3b64967642e7908f0f4bd5b5bebc79aa987e

* Added IcingaDoctrine_Query (readded), finished DataStoreModel


2011-06-14 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 42fd32b91eab43af9ab57caa8a3e7491d4424dec

* Current working state !! unstable, just for merge !!


2011-06-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b905a4775374048cb44e4bea7e5f89435995b056

* Independent cronk module ref (cronk.xml, AppKitModuleUticronk.xml, 
  AppKitModuleUtil) #1548


2011-06-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ae749ff07406e81912ef17c388bfdd6b16ba2a9c

* Added database independent configuration for modules (ref #1548)


2011-06-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 66a17e5b3b17eeef960da518225a903ff8a9cf3c

* Added database independent configuration for modules (ref #1548)


2011-06-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 625d0ca0df67882d76f25ae18bd9fd503b3712b6

* Fixed modules based routing handler
* Added testing context for module routes
* 
  Added test stub module for use with phpunit


2011-06-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d4c7d729b8edb3fd3bb1f061e9a99e478375545a

* Fixes some errors in TestInit


2011-06-07 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9febfa198410be09cd55c69dbad581cb457ef1a5

* Fixed problem in routing provider


2011-06-07 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 48e089f19c25adc1358f69fa576ef55b99797130

* Added Filter modifier for DataStore


2011-06-06 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 542984d2913d500ee41a089bbe7e788b2a2137ea

* Added connection identifier to internal icinga_web doctrine models (icinga_web 
  connection can now
* be placed in anywhere in the databases.xml and needn't to be the 
  last element anymore)
* Prepared API tests to succeed where they should
* Added 
  'connection' property to ConsoleCommand
* Removed deleted files


2011-06-04 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 2b3ab8db6e5f00c242f9da05ba4d77515b7a9d36

* add .gitattributes to ignore .git* in git archive


2011-06-01 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: fdbf7e3006007a792a2c4b4e4d8f0d4637c9db48

* Added AppKitJson Validator, started concrete DataStore implementation


2011-06-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4e5f5ce416defd9731b551fe316dd5195acc1b1b

* Added tree-like tactical overview (based on special CV layout)


2011-05-31 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 486f66cd440554949c590387d0f14eddddbe610d

* current (unstable and not usable) working state
* - Added Filter definitions
* - 
  Started DataStore implementation
* - Started DataStoreModifier implementation
* - 
  Wrote tests


2011-05-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e458c48a2ceba2d2e5db0e1370c38bfbf5392aab

* Upgraded bp icinga cronk


2011-05-30 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ac166cbb2702fa386880a9ded2551b5542bcd5ff

* Validator arguments are now parsed for apiProvider routes, started action 
  reflection in cache creation process


2011-05-30 Jannis Mosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 61f1faf82f16142889482e614074e0ad025e6900

* Updated to working status


2011-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 69af3196cd0caca8cf5f19f5b9d419e195d4faf1

* Merge branch 'master' into 
  mhein/testingConflicts:	app/modules/AppKit/models/Auth/Provider/LDAPModel.class.php

* 	app/modules/Web/lib/principal/IcingaDataPrincipalTarget.class.php




2011-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5765288e7e006846bd082fe37cd35c5ef33b2cad

* Applied patch from lydon, thanks, fixes #1596


2011-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0e5d219b4204faf8fccd3de0d798e8f383e11f1b

* Moved php tests to phpunit
* Added CodeSniffer tests (YACS)


2011-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dc27487bca745dfe067067df80eb29e749ca97bc

* Added padding header for control structs


2011-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e7f711aa2e3aa91ccf6d419bc149be6345a10768

* Part 1 of code styling


2011-05-25 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d95bb41b5ab8ba34dedc5073883045d1d3182a83

* Principals now work with wildcards


2011-05-23 jmosshammer <jmosshammer(AT)debian(DOT)int(DOT)netways(DOT)de>
           Commit: 07a5c02d2632065414aa4d377e18d30a3ba343fa

* modules and actions for ext.direct export can now be marked in the routing xml


2011-05-23 jmosshammer <jmosshammer(AT)debian(DOT)int(DOT)netways(DOT)de>
           Commit: 4ce95c2a8db3eab27eae5eda17410aa40f803afd

* Replaced AppKitModuleRoutingHandler with AppKitRoutingHandler,
* Routing XMLs can 
  now be modified via their own xsd/xsls
* Added own xsd/xsl files for routing and put 
  routing in a own icinga.org context


2011-05-23 jmosshammer <jmosshammer(AT)debian(DOT)int(DOT)netways(DOT)de>
           Commit: bb48870b4e393e8f0a24861ad176ebb3ed59b1c9

* Fixed compatibility with PHPUnit 3.4.x (which is default in debian squeeze)


2011-05-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 617469fbbc4b618bc711ef7f2b255f0d54d3fa69

* Added testing toolkit (bootstrap, properties)
* Changed agavi bootstrapping test
* 
  rel #1568


2011-05-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6fca3280cf916ab4290593ee98b41d3e78f46b62

* Reorganised structure for php testing ref #1536


2011-05-18 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6f1f45eeb8d6a9759562dff69486d35cf39bfda3

* Fixed postgresql scheme


2011-05-17 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ac5cbfc5b7c39762f592773a1340589ea383aebc

* Added bp bugfix for editor


2011-05-17 root <root(AT)debian(DOT)localhost>
           Commit: fe60941e024d98f176da09c8e659bc9a01baa628

* Fixed #1525, fixed #1513, fixed #1509


2011-05-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c1a6364738e2361e853f925e9d0ad4c65f473dac

* Provided IE JS fix


2011-05-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fdefb8b9bf7c2548ef5a351a5aeddd5ccfe14b56

* Prepared 1.5 dev trunk


2011-05-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9bccb96fc684854e5095238f9ceb165bca9e0908

* fixed cache clearing in bp-integration


2011-05-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 003a1dc1ff5a6ea99f87c454994eb5479482ba49

* Chaanged delete icon in cronks, fixed preferences window not constraining to view


2011-05-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 07aa4a5f6b176704623c391ce03bf38527f4ae93

* Fixed log-cronk principal name in template


2011-05-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: dc73e978f6f13cf14702f94264a1eaa403cc4c33

* Fixed typo


2011-05-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: fdca745f4e20af723a9668079ccf48b458ff918e

* Removed id from update routine


2011-05-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9206ec49963edee9f459ba20250f138135fdc188

* added target id to schemes


2011-05-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 89ff2e3d29b3151f19aed47244826942b7513fca

* Added smaller bg images


2011-05-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d2c1d2a683b37871470ab8e9daf692ee90f6438d

* Recreated changelog


2011-05-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a3b38f6e600fc67dbf2a0c3edce0d4b2d1ab1443

* Added pending filters in grids
* Fixed overall status pending filters


2011-05-05 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: fc9f5361139abf518ed8e68fa67c23d8362a6e1b

* set version to 1.4.0


2011-05-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 76d6dc0ad030db9c583d1bbc4fe7bda4c95d609f

* Quick fix for view and counter (pending state)


2011-05-05 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 2235f044fb24f4f1e0cb20728780df2f4515baf4

* update icinga-web.spec for 1.4, rpms build ok #1455fixes #1455



2011-05-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 392e96db31eaae2939f4e2b3367b89a60b61c2cc

* Tagged new dev version


2011-05-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 72514d7357912975fe2eb19bf7c010924b974b7c

* Added r1.4
* Changed version meta data
* Added new about layout and images


2011-05-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 49442845b612ef45bbaf208e94471697cb94b928

* Merged and fixed translations (fixes #1400)


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: cb989dc5199dd0ddaf685cb817e120bed334bc6d

* Updated bp-addon


2011-05-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 57d8eab7d9c616cf0d5e283c036c9902571e0cdf

* Fixed typo (ref #1167)


2011-05-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 39e9ab5c1cf2fd59447bb8e2926d1306891a5ec6

* Added instance name configure flag (fixes #1167)


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d49ac98f22f8cb4d2b9940924c6c720931b5e168

* Updated module installer, added update sql schemas


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 568f4406debb59041ac38ea2f6f3f2b726403c4b

* Removed exception in AppKitExtJsonDocument triggered by Doctrine-relations, fixed 
  tests


2011-05-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 538ea163c56d705d21e6afc88be8ba0a2f6f1561

* Added additional object meta fields (fixes #1001) NEW API IS NEEDED


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f1f67ebfb0b3723e38b907bb37a5bf9a0a54b443

* Added long_output to host/service details, check_multi should no work (fixes #958)


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 50a68a6c2377aea03a6626a90d269ef1e2b0bd19

* Readded unknown state constant


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3606583bce436111ad31fe436f1601e72f7c8911

* Added output length as parameter (fixes #1360), fixed and extended pending state 
  handling (is now in statussummary)


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 5b990819384d39722f7bede3ac54a4b6eb3ab507

* Added configurable logout path (fixes #1017)


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8c61fd6aac97f06bbd35173e201095fc5bdbada0

* Statusmap now recognizes pending hosts (fixes #961)
* Also removed a debug message 
  in PortalViewSuccess


2011-05-04 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: da9e2b06ce980b88d75ffdce33324950141071ef

* Autorefresh will now occur everytime a grid is shown (fixes #919)


2011-05-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1d46a3acab7a5a68e37d1c37a8378d98b8735e40

* Added HOST_DISPLAY_NAME for searching (ref #1011)


2011-05-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 857cccb226aca4bb54206441c25740527db39897

* Added more fields for search (fixes #1011)


2011-05-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: eb7ee76946660ccbc58eb58c5ba96237a8ca9dca

* Fixed apache conf.d guess (fixes #754)


2011-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 4528718f365b0fabe5fcf3d8fc290db5872e77bf

* Added fullscreen view to cronkPortal (fixes #493)


2011-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 082963e0cd578d97d074c4e06d1b1cfba54b1714

* Added config_type to icinga-api configurations


2011-05-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 67f20caf7b8540e572b0279a08fc6847a818e708

* Check against the LDAP filter on existing user binds (fixes #981)


2011-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 4127e346767087a4461b1558420719760eb28b63

* Added custom icinga.site.css stylesheet (#fixes 1330)


2011-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 065535d8109c8f50d6d549477615a9368c267122

* Added action/module definition to cronk search result (fixes #1408)


2011-05-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a96526ac2604b33d31c0c2f1ea67ad3d9e001e60

* Removed site config files


2011-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 67bc3bb74ab71b47c01909c638e35479caf7586d

* Refactored autorefresh code, added autorefresh changes to grid state (fixes #1348)


2011-05-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 16e7f10f5015bc08a2bd0fee05edf81dbae967ed

* Added new interval for status refreshes (fixes #978)


2011-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 62515c1844dc99cd522c2a0ae9130a0ffaa08611

* Added force service check (fixes #1318)


2011-05-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f1757976eee9a05f19b8763589f851e10234e022

* Search now always creates a new tab with the current result, added ignoreDuplicates 
  paramter
* to InterGridUtil (fixes #1292)


2011-05-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 76c2375928ca53191b979da7469d4e292f0693de

* Duration field in commands will no be hidden when a fixed-downtime is selected 
  (fixes #1280)


2011-05-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ade8e897ac497c4dea61d9e07ba6063212098996

* Added images/icinga as image basepath for custom icons (fixes #1068)


2011-05-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3acda9eb3c62b251dff0f22957b24eda69675ceb

* Fixed host/servicedetail hide/dismiss timers only being resetted (instead of 
  stopped) on mouseenter
* fixes #998


2011-05-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e5edb3609924cf4a424e48a56e519ca4bb7e0e6b

* Added principal restriction support for cronks xml and added log principal (fixes 
  #983)


2011-04-14 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 57de2149529700246c60cf313e0699a95c62c8dc

* Fixed wrong shebangs, removed superfluous getopts.php (#fixes 1266)


2011-04-14 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 630e91d68c5f2a7912cd534198c15cef1a14474d

* fixes #1273


2011-04-14 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 496f6126f2dee3608ddd78b637d10e30b5420123

* Added auth_strip_domain (fixes  #1294) and lowercase to ldap name comparisons 
  (fixes #1293) thx to tgelf


2011-04-14 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c117001106fea7034dba7b2b54072e6a903af79d

* Fixed typo (thanks to yoris, fixes #1397) and added additional cache clean routines


2011-04-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 839c1b69b384d6840aef3278b7c42a65670d4b17

* Fixed wrong relation definition (fixes roles not showing proper users)


2011-03-30 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: c9d945c16b6d80d4ebbb3a6e2bac15f37d2d55aa

* fix icinga-web spec file does not perform %pre functions #1288fixes #1288



2011-03-30 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 6fe903ce3d46df6ebee823c7bff326f0e0634b2f

* fix schema updates not copied to icinga-web installation #1339


2011-03-24 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: c6fc3ff4236f3e1692bc18cc9d92759b489f8f62

* Fixed API to not return metaData if parameter "withMeta" is not set or set to 
  false/0..
* Fixed runSuite.sh


2011-03-23 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 5fe73f3470a67906a8422f847b414223be484eaf

* Removed vim - 'i' in code


2011-03-23 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ed917d154754a334b126905085d7c3fceff4648f

* Fixed missing parameters in drag->portal action, fixed saved portal cronk 
  restoration


2011-03-23 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: a79e9b0a5b85202b88c708f5e4dfc46993382949

* Fixed API to not return metaData if parameter "withMeta" is not set or set to 
  false/0..
* Fixed runSuite.sh


2011-03-22 Eric Lippmann <eric(DOT)lippmann(AT)netways(DOT)de>
           Commit: 5d413ac1d003d641ec075514207addacc643115c

* Added missing principal creation on user import via external auth (resolves #1326)


2011-03-20 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2f88b420da4e60660df9f6d91e5698a1de4762a9

* Icinga icon now indicates if XHR Requests are ongoing


2011-03-20 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 40ea52a2f5669e5c1bd431d9578a63b02e88c3a8

* Fixed crashes when switching tabs quickly


2011-03-20 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9cb9e502bd644cb070c4b76018a8e08134a826fa

* Fixed updated agavi (#fixes 1307)


2011-03-20 root <root(AT)debian(DOT)localhost>
           Commit: 2eaff9434af079df37f8edc144f68ddb3d62057d

* Added sitecfgs


2011-03-11 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: b83854a14d0187e91978f994ed1bd11714490b53

* copy sql script for upgrading in icinga-web.spec


2011-03-01 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3afc188a77055ac3aaea8c6e6a5c687686e7a9a7

* Fixed wrong filter in openproblems, fixed statusmap in portal


2011-02-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8390f7437fed275276b07e4712f8b0f4d1ac4ec1

* Added module specific routing xml


2011-02-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f251a3eb179dfbe6048357f6828dbcde2cb26b9e

* Readded agavi binary


2011-02-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fa4d22455cdf33ac53ed79b9a3d1e1be8ecbf051

* Added missing principal for initial stage


2011-02-16 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: deab51579453698aaac163cab044f6dee69f4393

* fix date typo in icinga-web.spec #1220refs #1220



2011-02-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 372910af7d08ba951204381ba3097d54dfeaea7e

* fixed new module target site config


2011-02-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d0ad9ecd11426a1a3da4c8163bd91da63cca5f67

* Menu seperator fix


2011-02-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cd69ac9f357fa8645058e7ad747e881460329f7d

* PGSQL double result fix


2011-02-14 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: e2cfa5ada3e3a4b92b86b295ee24414805d999d2

* fix missing semicolon in mysql schema


2011-02-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dd270274729e1c6de50a8f2e97f7776149b9378d

* Added release dates


2011-02-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5b49293b28477bae453daa069cabfa53b5e52d5f

* Added bottom space
* Removed submenu from save cronk entry


2011-02-13 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: aa642838abec4c0e5df8ec465c1f921ec0e6f41f

* Removed old files from cached tree


2011-02-13 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: face98ac4168b6b7d1aba829d0c722cce60d97dc

* Added updated oracle sql schemes


2011-02-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 489137a6eed5bed1cf308412d99c545441225222

* Updated makefile for sitecfg


2011-02-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8eaacf7547abdf47993c33ac4fbde9c9d33be6e3

* Little change in module.site.xml


2011-02-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6b266a9592a68ab0d9e44f79c6139c553a81fd50

* Business process cronk now uses new module-specific configs


2011-02-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3a38f14dca99ae92443ba18dc65d32b1fb89b292

* Added additional siteconfig (#refs 1187)


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2c5b8bb612c481fd4b55e57bb81627f3bcabfd40

* Login triggers only one message (fixes #1204)


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b3fe39b80b343e62fd8959293df2602ceaa59a72

* Hide instance column (fixes #1195)


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dc9f72010775c161e6d7b7fee3adde2ec044ec7d

* Recreated pgsql schema (fixes #1099)


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2f26a2fe9dc03f55f36c205801822c6be5618683

* Portal custom cronk possible


2011-02-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 917d28383db7c69ff9e6fb0429caf94de5b06c7e

* Added changed PortalViewSuccess


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f2d2562c5b9c12ba1a67649960ca678be0852204

* Added compiled language files


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b264ecfb2c0b3f248440d667c0edacb6b51ec861

* Added language changes (submodule pointer)
* Added new languages: cs, pt_BR


2011-02-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e6f51b738fd59f6a9d729c8c6d07d334f0e81043

* added address6 field to new (yet unused) db api


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 68622ff43937199018a88a2eb1628d2ebd43daf0

* Added changelog


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 024f39262f1a0a7bab07cd5dc7afe96a3ca7a9a7

* Added asterix before and after value if no asterisk given (fixes #1213)


2011-02-11 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 15cfc9a11bc137a811cd4990fb51b9f66336d72e

* Merged with master


2011-02-11 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: b2b9fc81c221cc7cc979836673b70b64693e1bba

* Added address6 field, cronklisting now doesn't reopen already existing tabs


2011-02-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 12d694c888c4792d53ccdb28f4bb82fea18d0b3a

* Added module and action params for js cronk processors (fixes #1205)


2011-02-10 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 108f1be04299532470e6159605ab3ab019526fdf

* Fixed store error on closed tabs caused by refresh


2011-02-10 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 6c72910efae21903aae50a86bced3544a83d94e3

* Session timeout is now 0 per default (#fixes 1197)


2011-02-10 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: cbf1979d949125c5a639bfcb6357070edf7638e6

* Reuse open tabs if title is the same (#fixes 1211)


2011-02-10 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 8473cad994d84725a5f07c8f49dd917126263aa0

* Fixed several portalview issues, changes cronks open from dblclick to click (#fixes 
  988)


2011-02-10 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 023b109f91b53b11c76dca54f56f4e946a6524f6

* Added tbar existence check


2011-02-10 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 27e51e86bfd886e16111f23f5d36f00501612163

* Fixed #1205 - StatusOverview cronks now supply their action and module


2011-02-09 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 9701bf580c8e782d2e8eb83779b426bf758fb72a

* Removed COL_ in db results and removed debug snippets (#fixes 1194)


2011-02-09 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 5d06db9d5a7a8960fbe235ffe8e9c550aaf12f4d

* Gridpanel now uses paginationbars for refresh if available (#fixes 1193)


2011-02-09 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 13add6e8ff4f0c9eb9bdffe7efcfeaccfa079e37

* Added icinga.control credentials


2011-02-09 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 95eeecd0b4841ed50d8a6c06d4baec84ba1d35b1

* ErrorHandler commError now only reacts on communication errors


2011-02-09 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: c5a894f541ad6f664a261dbe2c8d2e093a0c2806

* Made access.xml more generic


2011-02-09 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 3fc0536c4569fdad2fab423edcdb73610cbaf4a0

* Added icinga control panel based on new console api


2011-02-08 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 38ba7b8c91ee0e288c5623088d835f9c7a952be4

* changed location of api


2011-02-08 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 59edcb70265519881832d40efe365884b1291bd3

* Started adding console access for icinga, moved lots of files and added tests


2011-02-07 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: aaddac87b2f163ab98feab54f9c6f9ab58518330

* Added servicegroup filters (mainly the same as hostgroupfilters)


2011-02-07 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: edeac8373c6e02dbbf1d3e01404b321c6ed709de

* Added hostgroup callers and tests


2011-02-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 077b084419683a0961685091e69833eac60a7f90

* Removed some method declaration errors


2011-02-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9f9a253e407043d9c9bb49ce9dcf14ddb2168df2

* Command for remove acknowledgements


2011-02-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 13e097c66677dea2c627fe285e43e5caf3b6c076

* Removed dom saveXML bug with php5.2.6


2011-02-03 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 8dba36a0654eeba1cba0f302f9fd7902a7e11b87

* Finised service retrieval tests, custom finders for contact and Service 
  requesthelpers


2011-02-03 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: e3b25f278dd137ae3809ea9908ce143713c09741

* Removed deleted files from branch tree


2011-02-03 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 67402421d5473bdebabdd6ad3d2409effa81edaa

* Added service retrieval tests and fixed bugs


2011-02-02 Marius Hein <mhein(AT)waheela(DOT)(none)>
           Commit: 69c9dd4485e6792c1e04a161496d29e635f68526

* Fixed mysql dump


2011-02-01 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 887e376314999fe5c56abe61eba3f79fdd09931a

* Added several service tests, added some service request presets


2011-02-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f415a89b947e2702e92bc772a8240f0267877b3e

* Level changes (cause pg errors, ref #1153)


2011-01-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0c641cec828032fd154d61be5ae7025c3aff1f12

* New background images for 1.3


2011-01-31 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 84cfad1aa66593f80c4e1ed4e9e8ad25a25d4777

* Finished SSH setup (pubkey auth seems to make problems in the libssh2 wrapper), 
  started service db retrieval tests


2011-01-30 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: e172c0466dc449c62f912ff53d19ad0ec745e3c7

* SSH Console Interface with password authentification works now


2011-01-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0bece37cdeb4b8b70367126ae6b676199db84984

* Missing css classes


2011-01-28 jmosshammer <jmosshammer(AT)debian(DOT)localhost>
           Commit: 84de8bde01e2d2f295315bb45a32146ad44966b8

* Added Console Interface to Api and implemented LocalConsole


2011-01-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 71167f2d9b091126b9b040b2316ec88678e41e16

* Readded json decoding of parameters for cronk loader


2011-01-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 33b75dde51045b16e8134ed7a508cae142c2b1f9

* CronkBuilder better window organized


2011-01-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 775b2ecd40a36b1450c997c450a112915db73a6c

* Tooltipdelay (fixes #1025)
* Multiline grid columns css class
* Meta col renderer 
  (style, class, ...)


2011-01-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d6ef3c2b7ce594da4630283eb9f962edc9b9a522

* Agavi upgrade to 1.0.4
* Style fixes


2011-01-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fa878fb5a4ad5614c31303822aa63d5c75d8e7f6

* Sencha ExtJS upgraded to 3.3.1
* New tab overflow controller
* Style overwrites
* 
  CronkTabPanel generalized


2011-01-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7825485dcbafd9aec04237e95f9990570fa4f916

* Added link to host in services (fixes #984)
* Better link values for interlink 
  templating


2011-01-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a174136604cdc36aa36c644cbaf3122303dec7cb

* Added index file (fixes #1055)
* Removed debug statements


2011-01-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f7466dfc1a27e694a1995b987ea9c7439cff9d84

* Removed default bottom log view
* Readded log cronk with filters


2011-01-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 10f83cd411d27dcfd2c6fada4817d302838053b4

* Global ajax timeout fix (for heavy load)


2011-01-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 291c291ce1fff1d1fee40d68a128a4b66de65547

* Removed deprecated libraries


2011-01-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aac1d73136f6ef466cf3102fdb2b7c5cfe68ba5a

* Removed custom search box with standard elements


2011-01-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f97b321700b288ea5d94ed2c62b11aacb23aa955

* IE7 bug


2011-01-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 572bb970efadfe19535837501b3363c95d708ac1

* IE fix for tab closing


2011-01-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4e720350e7b352bc66df77e043256ec87561d0fa

* IE fix for DOMParser


2011-01-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c3ec21f6a96e6331684dc36e504911e38fc884dc

* IE7 quick fix


2011-01-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 93a8800fe969fc6f49ff18fb6466367c44bd44a2

* Added more agavi site config files (ref #970)


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ffc89e5eae568bf885831bc3bd20d823e1bae73e

* Moved persistent implementation to grid object
* Implemented basic stateful col 
  model (ref #988)


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 78e513c76f8c99fcb0cc0e9921b7394bda6b4665

* Portal column fixes for new cronk struct (fixes #1070)


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 37625a4351a2f23c2218e171d576004c97cc0077

* get url view fix


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7edc7208bd4a765332b79eea6a6e9edb2ec7288a

* Category -> Cronk match fix


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5d53e7c6de37620e8d205b6a7035f19a46670ab2

* Added missing iteration in principalEditor


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 02699af47fffb83648f9d648dda57550801eb74c

* ICINGA ACTIVE check constants (fixes #1130)
* Internal auth provider available fix 
  (fixes #1131)


2011-01-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 29a145ea3e28f294cde2bdb62779a0c5c6ba07fd

* ICINGA ACTIVE check constants (fixes #1130)
* Internal auth provider available fix 
  (fixes #1141)


2011-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bfc56b9720f8aa3172d0f980694e23885fc05e41

* renameTab: method name typo


2011-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bc269e4eb235b2a060a9871f4248d0d50373ab09

* multiple principal targets possible display fix (principalEditor)


2011-01-19 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: e855d29534d96d999a13ccad43464af60c41c99b

* Finished host overview test


2011-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 64ac3736e1128259899a59e9a1ea7df4267d9308

* Removed GlobIterator
* Works again with PHP5.2.6


2011-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3fbd27eddcf834eb079ae89e4c0afc5f0bd8c8a5

* Removed GlobIterator
* Works again with PHP5.2.6


2011-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b0ad433bc434299e5588a7882b4e5bdd323de5e9

* Removed GlobIterator
* Works again with PHP5.2.6


2011-01-18 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3f52e5ed900e1193075964f066a1e4fe9237942b

* Added HostOverview classes and predefined API requests


2011-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8269f9b2e8400abb3fb0c639bf10dea92c7f082b

* Added cronk icon to anonymous filteredGrids


2011-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f5568034c769e34aac44e01668f6a5ed104e388b

* Added iconCls to url view params


2011-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 540ab5a2004672879146dcd73a11f37d4f7861be

* Fixed url persistent views


2011-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 77532c32471a86a23d0e6ff9c8557b6af23fcf45

* Added iframe doc cronk


2011-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fbe7deb67a746b395c26fd8a1a5c719adc6da4f1

* Added more config options to iframe cronk
* Implemented iframe url as model


2011-01-18 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 48a6f514669b6c5cc702f2e43ff7bda9427adbce

* Added queries for contact groups, finished HostDetailTests


2011-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d46d621d8925bf15f5472680ed236bbfd291364e

* Changed icinga downtime template for new target


2011-01-14 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2de78d883a2cb94c708884ac4bd9b034f24b1f34

* Create icinga proxy classes for doctrine access, added tests, started handlers for 
  messed up db relations like contacts


2011-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d1e51e8f88d6aa7a009841ebf7600fb2ea38625c

* Added downtime template (final, fixes #675)


2011-01-13 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: a9aa1b616e50741eb68febb5db43574008a65be9

* fix icinga-web.spec for sles 11 #888fixes #888



2011-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5429c7b56633db53cd515a2232986e7bc6bbb25b

* Added downtime grid xml template


2011-01-13 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 83d4567fba39d6127fc123d402b1fd3b158acba0

* Some more documentation updates


2011-01-13 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 21d00132b7a4cfb4d5ddc902b6b5abcc413f8cf1

* Switch to www-user/www-group (as specified in the test.properties file) using 
  seteuid/setegid (fixes #1109)


2011-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 164cdff95021251aa869d2da3ee485a63dfafe6e

* Added new filters (fixes #573)


2011-01-13 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3b58582e361ec8d7286f7b3b1ff13f895e2024e4

* Implemented Host_detail tests, started adding helper functions for hosts


2011-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 898a1657a2c23ac3df400034089036594bf486b7

* Form validation downtime (fixes #1062)


2011-01-13 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: e02b8921a4f2f3610aa991c0952f90887a13200a

* Don't use su when running the tests as an ordinary user (fixes #1115).


2011-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1241fbf804af4f07dc9f8ebe087753e1939fdc12

* Added command file switch (fixes #944)


2011-01-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b459d1ae7885ee2ed5662b1f9bf72fb9267d7fb8

* Added simple duration field (fixes #771)


2011-01-12 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: bf0fcbcd8d1359ee7bd4eb50fb3f4cf1fe31e378

* Updated documentation.


2011-01-12 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 8a74dd5bcdf89d7dd1681c5d9ff425f4225578e2

* Removed hard-coded target_id and pt_id


2011-01-12 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 916c3917b7cd137461ba1c5477209092fe6d33db

* PostgreSQL update script for 1.2 -> 1.3


2011-01-11 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: b2cd6d86a0f86ff87a96bfda828a4c2eb723ad48

* Added filter implementation


2011-01-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 25c265dbbadacfe07ebdb3490edbab260acacc2b

* CronkBuilder stateful object IE bugfix


2011-01-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2d0100d3b40b26a592b4a73e03a59889b2eb19a1

* MySQL updatefix (dynamic)
* Cache fix for installer


2011-01-10 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 16a20b47ec6be89247d454e765b0cdb30ac0578c

* Wrote api tests


2011-01-10 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 51a71ebf6f86b1e45e4deea86995058376dd7dd8

* Fixed typos


2011-01-10 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 925f400f1d06ce4c20b63cc9e3014bb1ae013fde

* replaced passed fixture with shared fixtures, removed repair functions, fixed 
  interface tests


2011-01-10 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 1911e865485ce21063df52a3f40d45fc5eb56049

* Detailed host/service info popup shouldn't close while hovering over it (fixes #998)


2011-01-10 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 43aaeffd3f957d597a8fbde65efbdbe820063c23

* update icinga-web.spec for 1.3


2011-01-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2382fb2cb50630742da28c1ff6a68279d5368ade

* Updated testing framework to only use phpunit so errors prior to agavi::bootstrap 
  can be detected


2011-01-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f0c636847feee47557fe2997de9f20a6e87772b0

* Update db-relations (intermediate commit)


2011-01-08 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: c3945d649b4f99a5429fea0ba51020fcad02d3e0

* Intermediate commit, db relations


2011-01-08 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 468f319d495ed356f952075bbbc6f480586b9926

* Added db-relations (intermediate commit)


2011-01-07 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 44e4634ed8c60b1b990e821e041c62dd22dffb77

* Change focus back to the search box after refreshing search results.


2011-01-07 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: d6e6fa0fa82e76eac948de86be197d1b76e47ba4

* Minor changes to the README and INSTALL files.
* Added a paragraph about how to 
  enable mod_rewrite.


2011-01-07 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: ae85bd44c57fd69187cb420cc3ce31398259fdd8

* Fixed translation: 'to to' -> 'to do' (log-out confirmation message).


2011-01-07 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: ed7fd25db1ed28a3bd642e6d9c887dea2cc9715b

* Remove IfModule directive for mod_rewrite as this module is a required   dependency 
  and not having it installed causes some non-obvious issues   for new users (i.e. the 
  web interface is only partially working)


2011-01-07 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: 1cd709ea2d15f6fe7d7aefc8b4a18a670f3f1ee7

* Change default endtime for scheduled service/host downtime events to (current time) 
  + 2 hours (fixes #871).


2011-01-07 Gunnar Beutner <gunnar(DOT)beutner(AT)netways(DOT)de>
           Commit: d578cf8beb119518795597dfef4858ea4cf7f331

* Use {2} instead of {1} in the format string (fixes #1016).


2011-01-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9ca55981bf7020f36dcb8287ed98d6ae034b2c96

* Style changes
* Position of tab slider
* icon things
* cronk tab panel methods


2011-01-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e8980c605e7b41f7f8fca069a968bd97cd8d26da

* Added cronk-tab-icons mode


2011-01-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ea7ce98e317d44c5fe59a9b5df0bd07e68e7855a

* Some installation hints after configuration


2011-01-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7d8bac63b2c33b95b2699da36174436917dcb2fb

* DB model fixes
* Category editor ready stage
* CronkBuilder fixes


2011-01-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5b75c1cc8f508ec6c310dde788c31ce9e6674b8d

* CategoryEditor: Prepared working stage


2011-01-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3dc755589408ade43946891daff443c171077f42

* CronkBuilder form enhancements


2011-01-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 62b3840c7e7d54002d1e1c740e55bf3673f7104d

* Added tabslider


2011-01-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 64e3da437df974c7f780950ea2e62e373a5ad53b

* Delete FK fix


2011-01-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f94886c5aa8f0f014d210cada6bd0ce32b321e94

* CronkListing: Deleting cronks


2011-01-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ab00bf0eb1a0a2218d595068d68a0365a450a6aa

* Changes CronkInterface and CronkBuilder


2010-12-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 64ff175ed8c7a4cf46b8ec661be0a7acbd881107

* CronkListing: Reload fix


2010-12-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: df0450cc3acec387a45c18e8af4a828074f497b1

* CronkBuilder: fixes sql issues


2010-12-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5cafb36fce6d336f5519a6272b67fad4d7ee3838

* CronkBuilder finished working stage


2010-12-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fe0efe6769d2e7c109e38b4fad213a06990f2e8c

* Finished new cronk listing
* Recreating cronk state from cronk builder
* Enhanced 
  AppKitExtJsonDocument
* Parameter passthru for cronk loader
* Apply runtime 
  parameters to the cronk
* CronksRequestUtil to create json string for cronk env


2010-12-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 90fc7812e66a3842e4920b82953ff7156e5fb2ea

* Removed old cronk data model


2010-12-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 49c915d228ed8c774cd35bb80e0a2fb3059c8553

* New cronk listing panel (reloadable)
* Combined provider for cronk and categories
* 
  CronkLoader using the new model


2010-12-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c23947162b77f581e5868b6066f06e278b5702fb

* CronkBuilder: prepared rewrite listing
* Cronk datamodel fixes
* CronkBuilder: 
  creation process


2010-12-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 250359f81bfbd8671d11bfd0e131ca111a10a42d

* Cronk icons now orange


2010-12-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e4df67ec0bdfd29d50327c0bae84a12f9a15cb6e

* CronkBuilder form ready state
* Custom submit action to transmit property grid


2010-12-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9c8646f91215dd83825192314a773dc4a31243ee

* Prepared generic Doctrine output for groups provider
* Added Ext <-> Doctrine 
  mapper class


2010-12-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 955170c34197f0b6fcbe411947bf130fedebbeaa

* Groupprovider show own groups to all authenticated users
* Fixes in group model for 
  n:m relation


2010-12-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 55e1b2db0030d5a21a4e78853bf6338932f282ee

* Added more providers and fields to builder


2010-12-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: da69543d31809fe9a0670813cd4c51e7644a7ed0

* Fixed database models
* Prepared provider for cronk builder
* Added mysql schema 
  update
* Changed to model


2010-12-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8a32441879d9f277ccfe18c96c5c77abd63f313b

* Added cronks database models


2010-12-14 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 26af8042c84b9e20f052a334ffcd021f1394e575

* Extended doctrine and tables to support for multiple db instances and prefixes


2010-12-14 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2b6867c71ffe38f90a1f100c5636cec508e2f759

* Add fix in typesafe comparison of AuthKeyModel (thanks to E. Sobe)


2010-12-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7e620c98174b0d3580ec8f4fecff2b95215e28b2

* Added agavi debug to devel mode


2010-12-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f2bd6e17543efa644ad8c204af4c0dfa2699001e

* CronkBuild intermediate ci


2010-12-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b460b8e68161e74cb44ef0d982119b16241d8153

* Introducing: devel-mode (disabled js caching)


2010-12-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 636597b757b3694095203b9bbcdc42df5059f5b0

* Added image file


2010-12-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 45da621534698ccd59f632e2861b0ebd8d8790c6

* Adding -NO DATA- display to grids


2010-12-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b2bf71cf8db2f56050a834b1cfe9eaa4ed5f8684

* Added Javascript caching (squishloader)
* Removed applicationState fromsquishloader


2010-12-01 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: 4be93623b3a714c17f054b9f67a0b1b8e55e673e

* Removed trailing comma


2010-11-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1a5fb63e26450eb3d79fc86c5b897e4e05fe2f83

* Upgrade handling
* Typo


2010-11-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ab403585d185f29d3e515cfa440773b1f4035bd2

* Removed useless files
* Added some dev targets


2010-11-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7cbe44090db9df2d88831d03fc0dc098421cd731

* Added more common installer method


2010-11-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 397cbd93854550d613bf246fd5c62dd50a87f9e9

* Added switch to disable api-check (fixes #863)


2010-11-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 489bd5053e49ed43cc3971c9ede81685d0682c31

* added --with-web-absolute-path configure option (ref #863)


2010-11-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 590a2435ad7488d2cf6e4e31cbc7ddf2a9c55d32

* Fixed login procedure (fixes #1018)


2010-11-13 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: d59f48b45a194c4cc9f73148796a73243a3924e0

* Updated business process integration to new master


2010-11-10 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: 1020df157c283da36ccf4949aab18668b6653241

* Changed transitive property names from absolute to relative, added Host relations


2010-11-10 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: 0401d13d5fb3c77b5b63b9f54248ad28b02b09df

* Added service relationships - not tested yet


2010-11-10 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: 8bac9f7eb65c3b6e4217abd665ce2baa1df9e014

* Updated doctrine, started API integration. Relations must be rebuild by hand...


2010-11-09 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: 4178604265d48139d140db2acbd962bc6a3746d6

* Updated ExtJs Version to 3.3.0, added click buffer to statusOverall (#fixes 977)


2010-11-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7d58e7106474d6d8338dfde9e17fa6a59aa51fb8

* Added missing files


2010-11-09 jmosshammer <jmosshammer(AT)mojadev-VGN-NW11S-S(DOT)(none)>
           Commit: ca0419075c29cac09a9a53f88a640c5d18c7c79b

* Fixed namespace handling according to php version


2010-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 02c03dc62a028e372f3ed4ca15a0dd7588224fcf

* Avoiding 'silent' errors result in exceptions


2010-11-03 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 83dba5e412a84d704edefc3556c57fa8dbe215ac

* fix test.properties not being installed correctly for web tests


2010-11-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 313e712cc7fdf9224bbe151082e4237b2153c6fd

* Fixed PHP error


2010-11-02 jmosshammer <jmosshammer(AT)localhost(DOT)localdomain>
           Commit: b4546aec6109ee44d4574a404b5478519820688c

* Added missing accents to RegExp


2010-11-02 Netways <jmosshammer(AT)localhost(DOT)localdomain>
           Commit: 651ae661e6109857a25d32c1ae850f5efeffd720

* Accented letters will be ignored in command hash (js: Single letter, php: 
  Multiletter) (#fixes 932)


2010-10-31 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 1dd6dde2b953dffd3168ccb6555db275dd8a0137

* Added additional host_object_id in open_problems (#fixes 934)


2010-10-29 mojadev <mojadev(AT)mojadev-laptop(DOT)(none)>
           Commit: 64feba66605e758891ade220cb0789b01ffd846e

* Added dependencies (#fixes 938, #fixes 937 #fixes 930):# Please enter the commit 
  message for your changes. Lines starting


2010-10-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0475bf3ea0edd62904f5a51c2feb22091b5ef8be

* Added patch to using xslt files for api queries (rest)
* Thanks to William Preston


2010-10-22 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: d13fea01c4babaea4f549fa6a2b7432b7dae1017

* Fixed error window on invalid credentials


2010-10-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 356292323e8858d90c6025e0bcafb492b305f11d

* Fixed file list


2010-10-21 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: d643320fa52c6122383263aa04e397bc8b4cedbc

* Fixed starttime format in commands and interface freezing when trying to send an 
  invalid form


2010-10-21 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 18544f0cded2b94ce91147cb0b7588b31bba1a24

* Added extra check that prevents harmless js errors on tab close


2010-10-21 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: b5557175c98f412d274fdafed3e3d4ab40138e8c

* Fixed schema for postgresql (#fixes 915)


2010-10-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a9e77eda3a34e35a3c792d124caff0ecc9b91996

* Removed vim swap file


2010-10-19 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 3602796995e4c2814446ff0194690045facfd6e6

* Fixed wrong time format in date commands


2010-10-18 root <root(AT)localhost(DOT)localdomain>
           Commit: a22b2f61d6e59608d09c28fa966f4b2106b308c0

* Fixed psql schema and removed typeahead from filters (#fixes 885 #fixes 872)


2010-10-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d6987370089ea47ab5aca798a13e1d2623978b82

* Cache clearing from the webinterface (fixes #784)


2010-10-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5337f9eb737d8ede4de24294a2c5002fb4b08f11

* Disabled free edit of language preference (fixes #824)


2010-10-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8b93671922deb297a9610cca565458ebbc9e21b7

* Added favicon to template (fixes #855)


2010-10-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 85e05dab5620d59955a974e046bef4e37228d5f2

* Prepared 1.3 start
* clearcache script


2010-10-06 root <root(AT)localhost(DOT)localdomain>
           Commit: 4c54960b51fbefca289d4dd04215ee754259e61b

* URL Cronk now works again


2010-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d0429a365df5630c6da8ba137b745a389cd772f4

* Release babse date


2010-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6c27a95a896b033a2648445046b4904553b90c42

* Removed 'undefined' from openproblems


2010-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 33fbc8b27b1b2bc8f791f8c205c1c2cffd6eefac

* PNP integration, fixed urls and icons


2010-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 51e9e87ded60a888f815cf6f202fee6aaf81ab31

* Makefilefix


2010-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c8220626599be352c8ab8a9ff8290daf93322bc7

* Added new CHANGELOG version


2010-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 86dc291378ba388e727ab6fc50d9b75d348da4ef

* contrib install fixes


2010-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cb4935f0a846baf31a636776872a3cca0aac7994

* Added business process cronk
* Removed etc/contrib from install files


2010-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9b13b02fe07a0171a161b3b470eef5bbb777caa0

* Provided new translations


2010-10-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c7541887524b000e97de8f41e778acc45b40ad74

* Removed js log statements


2010-10-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3570f49e0486e2d217845787f85fc547e04f5df4

* Make space for top cronks (fixes #851)


2010-10-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a71eea12f9b8e207a79653118e92cc4a1b336e31

* Tarball md5 testing
* Remove db-upgrade target (fixes #845)


2010-10-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b45042ec55cb823ee055405377e1752956d77073

* Config search char trigger (fixes #846)
* Typo in commands (fixes #847)


2010-09-30 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 57d5e758987ba49d927622e200a038f079f3ab28

* A debug message made it's way to the commit


2010-09-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: a8f73b6d615b167a9528fb5ec4df740345aad2a1

* Fixed "open problems" problems


2010-09-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 56b1103090af5ab3dc16630e1562e9ec6842d7ea

* Doc update


2010-09-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 7c7b45574d3127a039129ec0dc8e9b625d0d1a2d

* Added PNP_Integration src folder


2010-09-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 940ed20cceab821489bd9696ecc178b572f63cf0

* Added command if and removed a lot of debug messages


2010-09-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)com>
           Commit: 0c086625026bbfce34c2fe2751f3ca252c8ed0c8

* Fixed auth problem (don't know where this came from)


2010-09-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d0ca13805ec303c21f7f9d3a329f0bccb41012fe

* MonitorPerformance typo


2010-09-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0ee49b4c12d2eb6d7e72742ca74f7f15f4455aa5

* removed debug message;


2010-09-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ac3ebd6e0345fb75eb5cef6eea31146940925e08

* Added autoRefresh by default (#resolves 820)


2010-09-28 root <root(AT)localhost(DOT)localdomain>
           Commit: 5c8037055515c697bb38f26a371bf619383fe5cc

* Updated tests


2010-09-28 mojadev <mojadev(AT)localhost(DOT)localdomain>
           Commit: 14ad6d86dfa2e2353ffcf2d1ddf49900e3224e90

* Fixed commands being send x times per item when x items are selected (#fixes 823)


2010-09-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fb924124bef2c92e3ff8e49a6f1e54f0b9078e56

* Install make special file fix


2010-09-27 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: fab4fc898c5fe8ad1a6602be97c51e9f5cdc9428

* Open problems now doesn't show acknowledged, add object icons (#fixes 817)


2010-09-27 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2b3ed001422dbe25c2336228959ed1a8dc803fa6

* Added some icons


2010-09-27 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f4f17cf4e3e98995c241778e688fe272695cd750

* Added bin folder to special.mk


2010-09-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3fc069a15c6b568d3869d91131f8e99577e7a960

* Principals are now one-click editable (#fixes wq816)


2010-09-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2fdb543dd53d68e32f6c9d9d944889342322e769

* Added mark for active filters (#fixes 810)


2010-09-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: c98c685e7b713b075bc5f785c7a3eb605d1fca59

* Fixed filters not being saved (did anyone notice? :) )


2010-09-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: da4b8121dcbca04794cdd6277885330f3780043a

* Added missing command schedule downtime for host and all services (resolves #818), 
  removed console.log


2010-09-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 21d4f67c1cd5b77680d738fb134811b2aecab1d5

* Makefile fix


2010-09-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7a857adbd2cb350d2dafa85b7f53eeb6f4aedde8

* Monitoring performance cronk (ref #796)


2010-09-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: eab9210876154c9305e5e0823201a1e093587ea7

* Total view is now also available as XML


2010-09-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 06a7b2e018f02915c162eef6a58beab504bcdf83

* Overall status only coloured if objects available


2010-09-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 34e39a63ad39f46c0fbf9ec0633aaea43fdf096a

* Accordeon cronk list is persistent again


2010-09-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 887535cd959cc2b528b43b7fc6d0ab86d595025a

* Tagged new release branch


2010-09-22 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 335c80daab77e53d50b7c1cd32d2f27af42f2a06

* Fixed PNP_Integration in files.mk and cleaned up unnecessary files


2010-09-21 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: b5c082d52ebe34073e867322022d498475931dde

* Fixed files.mk again, removed wrong icons


2010-09-21 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 86eadda533b70a33a92dc619670ed9de038f5809

* Updated files.mk


2010-09-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 74c3dd2eba4ea5ec3281ba8b603cef56d040c105

* Added new api options for GET Request, enabled CORS Requests


2010-09-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 625cca21e41d87f76cea1445879f0e183801e01b

* Removed host from pnp4nagios


2010-09-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 98137a5a92e20a95911c19ecd633bf762b99fe3a

* Status map is now move and zoomable


2010-09-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 97fce677d387fad62afee7e735b6a11ee8b2e1a0

* New icons, stylesheet cleanup, iconRenderer can now be used with parameters


2010-09-17 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 6684ad58080dd6bcc2bd38c83e7f1579f33e858d

* Error handler doesn't complain about 200 status anymore


2010-09-17 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 171e095881aaf3b2f1f6ecc38c4f9ae54bc7be4f

* Updated jit library and portal tabs now change title (#resolves 795 #resolves 798)


2010-09-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: df7da1d951ff3e458e95da065f5722c7d38a4b5e

* Wrong datatype in comment


2010-09-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 28f9f9c8e502557e795f9e3029e37ec8e387be0d

* Added uniform handling of user preferences and a scroll and sizing mechanism 
  (#fixes 797)


2010-09-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e128947954273bf344d0f9f5e826c41197bd388f

* Max search results 200 rows starting with minimum 4 chars (fixes #791)
* Adding 
  bin/clearcache.sh to gitignore


2010-09-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: cc93e17ddd31d4126920a367b38b923b68a010fb

* Removed home, Portal in Menu (#fixes 793)


2010-09-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 84beda62a4ab28a5db3000d61a4c7949935836f5

* Prepared for intermediate release 1.0.3-1


2010-09-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: ae7da0abc6e4acb6b3f19574a62b0413ea94935a

* Fixed grids not showing when js load delay was to big


2010-09-14 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 8a97eeb36c956f27f5d8ba6b10f6589509229235

* Added script to clear cache from console (#refs 784)


2010-09-14 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 0dee1e728f54ccf34ff53422901455192983e243

* Fixed TO -> Cronk bug


2010-09-14 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 39a347eeae5efdf900f32af6d9bbdc2fab0a490f

* Had to change status condition in order to let StatusSummary work with api changes


2010-09-13 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 032db15c642a2bfaa20ebc37eee2ff74344a6e19

* Fixed typo in servie status display routine


2010-09-13 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: ac15553ff70f5ba6d95d27f5d048328972f07a9f

* Added pending states (#ref 371)


2010-09-13 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: e5de7a11f4ba8b807a3fc405e0e5980f9d5fad8e

* Added new icinga-api target to routing.xml


2010-09-13 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f0a63537a9a2bcdb30adfbb672908be3ccb4bece

* Updated PNP Integration (iframe view added)


2010-09-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6fbca3107b1b521d45256e2122215c4498c1caee

* Adding alternate cronk template file system (ref #621)


2010-09-10 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 44ce8b3e7b4e462610a3a9c93d1e2f97b04904d2

* Readded original host-template.xml


2010-09-10 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a46fd610ed6d9628fc475d141a60ddefabfcc150

* Removed accidently added db models


2010-09-10 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: bc6fc28562af552ac2912be1dee27bc59e49730d

* Added basic open problems view


2010-09-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 703d42ada29d59b0596a4b8228c774ad4f487598

* Renamed schema files to match the upcomming version


2010-09-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b7a5806513ed4518e628c4f55dd83330bd51b71c

* Added upgrade steps to Makefile.in (ref #621)


2010-09-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 16538ff184292911fdd5f42d96bf264169a4e71a

* Prepare for custom config (ref #621)
* Added new site configuration files to be 
  installed
* Fix for method signature


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: d4dbd5b89177495772cb16ca309d0847b22733d8

* Added NoScript tag (#resolves 774)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: d523c7eda0d5cf851c854a984f47c9cf8c090741

* Added additional filter fileds to hostgroup and servicegroup (#resolves 748)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a6b70b3234b87c95a1f866d61fc67c98bd07d79d

* Service comments now don't show host comments, added hidden field with defaultValue 
  (#fixes 554)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: cfff78c7e73b05374e5914a71b39eafa906211a8

* Stylesheet change in cronklisting (funky CSS3 transitions)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 5c40e9c67b377b595dd81249e0712c00d1e07423

* Added default limit to Logview


2010-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 76c480a5640bc20e5ec9c56628e8d739b43f0808

* Removed deprecated classes
* Prepared for user configuration (ref #621)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: dae3fd29fc863a95c1f5fa6b17a8b33335c6006f

* 'contains not' filter now works (notice the new icinga-api version)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 8247c834e1440161c2025d47ebe3d623ba170020

* Changed command message when no options are available, added minwidth for ack 
  window (#fixes 727)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 535a34c26b22781da68df46e4cd527594821a28a

* Server sends now 403 (Forbidden) instead of 401 (Authorisation required) if not 
  logged in (#fixes w#740)


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: d20c968767a96907b953e52714f5b49b6b2c5c6d

* Added the cache directory again


2010-09-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 003a73544634b401f88a94674f6fce07e7b2fe48

* Updated files.mk, moved modules folder


2010-09-08 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a57cc1bfb64ed1534f5d48d34cbe153d31fa60d0

* Error handler now notices if the web server is down (#fixes 775)


2010-09-08 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: c66264c60a9ed12ac0130397cfb6ffe379e06fc3

* Added contrib folder with pnp4nagios integration package (resolves #769)


2010-09-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e4c054184966a9175539c62bc003a61794c74263

* Added socket option in configure (unused)
* Fixed some E_STRICT problems


2010-09-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5abf7f3a0b5f98dfc3a488256cac0fed1678ce6e

* Added better design for comments display
* Finish implementation for comments 
  (fixes #697)
* Make files fix


2010-09-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 71d2964317162ca6c8f1a3eddcb45dc45973ef45

* Added logging to the command sending process


2010-09-07 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: e40daa540a99642a475aad7534f9e6e017792406

* Added new log view


2010-09-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 09d32ddebae5f78147ed317ce0ab64fdc0b72040

* Commit before merge


2010-09-01 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 53e84d30d83c9e3f9d8865b73ed95c2d73e6d814

* Api desc validator is now case insensitive


2010-09-01 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: ef2957eff935475ca66c994a589361134e40dba9

* Nested oarameters in cronks are now json encoded


2010-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 98de60347ff7f6d545c5069ad63d20bdda6d6f84

* Typo, added missing images


2010-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 50ddfb77ac7e2620e60ccf4fa373190fbe14080d

* Added image include methods
* Some more images to include


2010-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5ae17ac7a42d08f6bfe1dfb414b99fcb23f57abf

* Fixed installation for installing phing and the build properties


2010-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: db9af45b2df20c9e0819e6efa7230154280f82e8

* Added fix to open a new iframe cronk and activate on click


2010-08-31 Christoph Maser <cmr(AT)financial(DOT)com>
           Commit: 28f090019bea61032530a6596b0c4e6ec7188f2b

* - add icinga-api as build dependency, --with-icinga-api wil be ignored otherwise - 
  change icinga-api path to value used in icinga-api-rpm - set defattr - set ownership 
  to apache for log-dirs


2010-08-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b2289e0622220077bda445e43a2aa53fe53aa7ee

* Added all available translations from pootle
* THANKS A MILLION TO ALL THE 
  TRANSLATORS AROUND THE WORLD


2010-08-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aa499627be1f956c2b7386cae4b50e39e349f0c5

* Adding new translations


2010-08-30 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: dfe7f47e35c8fc49714f6a42b8a02042965f355d

* Added possibility to give filtergroups via filters_json, allowing or/and groups


2010-08-30 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: e7990ae6c080679ee89ccb6d8af93981982027a1

* Fixed Exceptionhandler


2010-08-25 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 659de6c05f655f3141dba14d850324cfc62ac45e

* Fixed GridFilter not appearing sometimes, fixed TO Grid Link ( fixes #726) 
  Preferences window now scrolls on overflow


2010-08-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6354ff9b558527853902222f32771d53bb2edd09

* Added comment data to the grids (ref #697)


2010-08-25 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 70049dfdb934454ce2a836f55502507f319d7d58

* Fixed database and IE Error in Acknowledgement Window


2010-08-25 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 168fc9a00c5039688722899cca4f6fadece5623b

* Fixed fixed files.mk


2010-08-25 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: d8f0bd94b8fcba6275a1772970497be40a82b4f4

* Fixed files.mk


2010-08-25 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f3625af20163ed90c5a3d7ee027519e46f026f3a

* Custom error templates


2010-08-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 1246bc61aea885ac6116520aed4e3547e08182a8

* Exception now only logs @, added dynamic iFrame, fixed CronkURL Export


2010-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 47d5e0665586ec076aaa105bee163009b735eb40

* Fixed formatting II
* Disabled the basic auth provider


2010-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1d8aeb0e97a37751404b98ffb2499665ab2daef0

* Fixed formatting


2010-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 81ddcaf4b18f9ad0f3b005dc8732506007858c78

* Fixed method declaration


2010-08-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 69f3f533a33b34aac47094662b39e128d0116620

* Added possibility to enable and disable bugTracker and errorHandler via preferences


2010-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: df91955c16ee26abbf6b49715c3610c7d4f5b46b

* Make files fix


2010-08-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 6c3e915d5c6c5b1c6d6691c9a8a6eaeb5729cf33

* Fixed Make (fixes #744)


2010-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ed883b9f39979837362d278c741e44fab0c64e7f

* Fixed method declaration errors
* Added own exception and error handler


2010-08-24 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: cff6da2d34ae019913d167310c922ed86af514b6

* Fixed Principals (Fixed #729)


2010-08-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 02d1994fa10026e2f3ccfb6818b92af226c01036

* Changed exception template to use HTTP Code 500, so every php exception will be 
  noticed by the client


2010-08-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a81d529dd9b24e1cf6df06c49725606d39a659e1

* Error handling setup for Ext.Ajax and Ext.data.DataProxy (all XHR Exceptions that 
  return status != 200 trigger a sensitive message now)


2010-08-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3fb0933fe4ba654f247967fb28b7ceb12b9a458e

* Added icinga.demoMode principal and added sql update schemes


2010-08-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 38499a7fa4fe4836651c8f9a1ba8807dc36913c9

* Exception handling


2010-08-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 211c540742ae0ef18ba2a807c270d4d1fba07918

* Fix in error report


2010-08-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 98c532623feaf1058f7c21590c2520c691d02a6e

* Removed IE hating comma


2010-08-22 mojadev <mojadev(AT)localhost(DOT)localdomain>
           Commit: 86d0ac89470fb7b8b2ca88572ab5a94b54606d36

* Added scrollable cronk list, fixed latency bug in StatusMap that prevented it from 
  being drawn if the cronk request is too slow


2010-08-22 mojadev <mojadev(AT)localhost(DOT)localdomain>
           Commit: 8f2d7270f8b5b34ebfb8b0aadc11c9b723e67b53

* Removed unavaiable services


2010-08-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a63e4b3af53d906c728bec6d3ceae536c1ccd5dd

* Changing detailed status handling and formatting (Thanks cdoebler)


2010-08-22 mojadev <mojadev(AT)localhost(DOT)localdomain>
           Commit: 516259f33ef320f62f784d6faaef2fc57d7b44c8

* Started client side error handler


2010-08-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ff10263512cb9454e31f1bedb7753ead25fb3c05

* Fixed apache config, allowing js directory


2010-08-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 4c5e1875fea39b71f5e492b1624390af8a97f55f

* Started Icinga Error handler


2010-08-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: d4b9cfdc97c7f10b4786b367860cc4424d1e64ce

* Updated authkey provider


2010-08-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 91674a2bed3a9a47bd0a9513c59317011b9019e9

* Auth.Dispatch logs work to debug log (ref #738)
* Auth.LDAP can update profiles
* 
  Auth.Dispatch can delegate authentication to other provider


2010-08-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 8f1d5083420b125bcbf0a39bfad98fe8baba2453

* Catch console.log to absolutely prevent any crashes due to logging


2010-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 13b529098e0d60dc265872ab62d3e9ad43e53d59

* Removed caching (again). Fixing persistence app state.


2010-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3a19a8305749cc7fd8f797cc09c18994a3721acf

* Disabled HTTPBasicAuth to avoid unwanted side effects (ref #738)


2010-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ee10a9fe5b7df5e57218ae675e446da15794202a

* Garbage content removed from sed operations (ref #735)


2010-08-19 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: fc60cdad2cc9ff223d68ca273d93ef25279e7844

* Added possibility to manage usergroups in group panel (Fixes #725)


2010-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4ad16d35a91be67ee86b52b599798e16019026a1

* Make file list fix


2010-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 33b7cd15434d961c5799ad1a6e436088d5ce4171

* Cronk filter window object is now dynamic
* Adding log handling (methods, files, 
  declaration)
* Switched back to dev environment for exceptions
* Logoutput for 
  BasicAuth provider
* Generic welcome at login


2010-08-19 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3db17e1c3d1b025eab8aa615fe8ec19cc68966c3

* scheduler and console context updates


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5d68a1a49a0e57ea915456f0ad0591dbf3daf458

* Removed unused class


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 59df6651e263ece55bde86f67ac48f5942c96116

* Disabled default login message


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a9fcd07b995e4dcca8987fd70df10d007b060428

* Adding ballon for login notifications


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d7ed7d85600b612b2a02099afab0486b6a683ecf

* Create new version of changelog


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c963821663dea6148e0dcf1e072701ed46f92fcd

* Changed package version and release date


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ee51d1006505edbbf8af13cfc8ace7d5d9c7bfee

* HTTP basic auth III (fixes #333)


2010-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6544e87f395c4e1e956736e73064ae733a127552

* HTTP basic auth II (ref #333)


2010-08-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2831ce069cc03fed5eecc17fc346317ba7d06c78

* First implementation of http basic auth (ref #333)


2010-08-17 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 53c1e83adae21893cba49541f57bc655090f349d

* update spec file for 1.0.3* remove fix-priv fix-libs, they are moved into special 
  make targets :)* add install-apache-config target

* updated Makefile for apache config target install


* fixes #732




2010-08-17 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 81754397a770bdb1d6f5339286bc0de6361dc05e

* TO fix with short_tags


2010-08-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 59ef2330c0d03dd253012c0a2dbb6ee3fd5e15df

* Make files fix


2010-08-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0664248432c1619becb6aeabaeb5195ff490cc62

* Added init method for loading IcingaApi class
* Constants working at startup
* 
  Removed old factories


2010-08-17 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 78c448d0d591efda9810fb63114a681ba77a8758

* Admins now automatically hava api access


2010-08-17 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 4a3a616fa43b2eefd8ce7b1d5132fb1969b05226

* Several little bugfixes


2010-08-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5db00eaa523814381c0ce21d71ec06ff09c39f73

* Added some new translation domains for date formats


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 0bf89dab3b83724ff9825c729340343e54aca566

* Fixed integer being interpreted as string in pagination (fixes #709)


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 08b6a4e5a48546302ca3d4513bb804646e00ac0d

* Updated testcases


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a664f1fdf234322572f7f09374efcf23c2440b2b

* Fixed bug in edit group due to new root property in data


2010-08-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: deea376715949e28cf11e5b9e82b4dd80704f862

* Adding new installation method (fixes #719)  
* No symlinks needed anymore  
* 
  icinga-api substituted in Web module config  
* special install target to install 
  with other permissions  
* apache2 config file in etc/apache2/icinga-web.conf  
* 
  Target to install this file (make install-apache-config)  
* js/ext3 web path is done 
  through an alias
* moved make-tarball to bin directory


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 144096a02097c23f72be0c707aff7062ec583539

* Fixed Pagination of groups


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: fe51c975c2953e3cb3b51638d9563932fa50f384

* Fixed width handler in column portal (Fixes #694)


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 183c908e6eb331f72da9d29541cf3cc9e177d3ec

* Added icons to host/status labels (fixes #695)


2010-08-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dc1ab38bd05c67eecfaf4062094ba1e336df1cee

* Adding javascript squishloader caching (TEST)


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: e0d737697d9f11457a3e0527350350add22f5b59

* Removed jsmin


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3b830480c4b7272f6a933cc80d6ad37967a90e4a

* Fixed strange console behaviour (it's defined, but undefined?)


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: cca8e438c6141b31cf1c3ea946ddeba063ee1b58

* Fixed weird console.log behaviour in IE (js says it's defined, the browser 
  doesn't...)


2010-08-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c5fa0abc91f7ec379c5cb211d1aeffc2eec1036b

* Make target to reset passwords (phing task, fixes #628)
* Added line break to 
  confidential inputs (PHING)
* Moved icinga version file to etc/make
* Rebuild 
  configure


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 9e23d53dced8005eb95ecad56099e5b27bd2cf61

* Fixed module.xml.in


2010-08-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: c93aec87024ad8a9151010b6b22a996cb2417049

* Improved IE Compatibilty (everything should work now)


2010-08-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2e291e5ef87ec8f4aae06b61a649f9185a349369

* Changed clean target names to suite more gnu


2010-08-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 365c2f1cc41b2e4018cd0ba028d93423507b6c32

* Added changelog


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a947c284b7f9607c7d8e0cfff50c1322125a0b83

* unify configure switches
* make files fix


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8085977754cfa0c7c528fe2fb6b6be44f7c65e41

* unify configure switches


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 765601125eb880c764d4296804cb1b93a7078667

* Added external version for configure


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c2eb74b50ad3713892bc10eba5a451fa43f4c259

* Change login text to some more 'generic' (fixes #714)


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c50f0563d3d991f4fcdf88896d4bc568f360860a

* changed icinga-api path to unify icinga style (fixes #445)


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 455c289f0bf2de2d3ef78c591d437596c811ce00

* Simplified js redirect


2010-08-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4c3295e17e423f2673cc26ccb94cc4012919042f

* Adding apache configuration for including
* Adding api connection switches (fixes 
  #630)
* Fixed release string in HelloCronk


2010-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ec507a39cb726628fb5e71350a0d86d825feaa42

* configure handling (ref #630)
* version handling
* substitude web:module.xml for 
  api connection
* doc for icinga-api connections


2010-08-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2714c9c0344db37e4baaa538e7d86dd152d1ddaa

* Cleaned up namespaces
* CLeaned up icinga.xml
* Prepared version in configure
* 
  Added normalizing in module configuration
* Files for Makefile


2010-08-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1ac40714fcc6c30842a65d291f43332fff0880f8

* Fix for 'groupsonly' cronk filter (Thanks to Birger)


2010-08-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 93b23ef869c6b5d7ae42479ccf3333e46cf69eda

* Modularized css and js files
* Global module namespace for module config


2010-08-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9f47228cf7473b5af4b99203c03a1224307c5282

* Rewritten config namespace


2010-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b18112261995971eef2775f469d330efb31ae909

* Reorganized the icon structure III


2010-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ebd2fd1157d47037214ec9d2d4357dbd1932bbf7

* Reorganized the icon structure II


2010-08-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 31c47b3cbc5a76ff4ad5d7a3189a74a0e0b81859

* Reorganized the icon structure I


2010-07-30 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2a8dc4162640739ad780f85c1b97e02c2fc5a424

* Some IE Fixes, Added JS minifier


2010-07-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 63d455875d011ae3228f7d5c10ab556b79e0839f

* Fix for preferences settings for not logged in users


2010-07-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e3ab6e4237a0cb1a63b1497bccb8731849e4e511

* Readded stateful log cronk


2010-07-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b6381f1c6b1c9fd9f9e635a87831f9f63572d35d

* Purge userprefs from the userpref screen
* Adding default welcome cronk setting


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dbb35fd768c3093b8d4d588d1109d4deefdbf2ca

* Added hideable bars on cronk portal


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4f9fb2445518f7073ddc2b603d72662f37081050

* Fixed portlet resize issue (fixes #383)


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a2335d67f294ecee45fd29f5090210282ca81040

* Better commands, sending commands to its instances (fixes #561)


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8821735ec6c95fa0e9cd307985f52430f0235946

* make fixes


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 399c62609cc0ca63edc3d17a6ceb99ffd67f7d7a

* Adding per user grid attributes (fixes #567)


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1401a6174c348b8819de4b757909e68d602f425d

* Check valid session and warn if expired (fixes #606)


2010-07-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 851f9220a3dcc00021a5e1a40babf2b3d6078c5f

* Changed process of grid count queries


2010-07-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 48c5a0f8b75fc6f679263bf7c60684b9ad4da846

* Activated clean traget (clean->distclean) (fixes #634)


2010-07-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8c4ed499461924c97fad35acbdb42ed0bd2faeaa

* Bugfix for AM/PM times (fixes #360)


2010-07-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7d344822993527990904ba1312b286b4f2a177f6

* Adding xtype to Cronk.factory to avoid empty cronks after reload


2010-07-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f4a76b03b4465f3fc31211322e0601f8ffd6fa93

* adding new icinga-throbber (Thanks Karo)
* Styles


2010-07-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 168513656d36c19e11b94b3e37fb5ef1d70c38f1

* TO colors fix


2010-07-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8814d3082c6749c90358a34939ba5c0dcf25824f

* Makefile


2010-07-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dd5f554ae22aec58ee2a81e7418423a898b052eb

* Finished TO implementation
* Typos
* Styles


2010-07-23 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 02fa26c50416b1dc3e31a8fe592006a4efe04e0d

* Added position field for categories


2010-07-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e6e1dcc1ed7b768df73f4fbbb9ae88984e75fa5f

* TO implementation


2010-07-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bc9567a63233ad8db3c598b6cf8a30b9a8e401f4

* Bugfixing TO implementation
* Adding charts template based on Ext pie charts


2010-07-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2c388e88b04edc5539111e29b6558f4fa3770d38

* Makefile files regeneration


2010-07-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cf1dd2122f4cc6adde8dd6fd7591bf1afc9f8b21

* My fixes


2010-07-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8d446c9b380e12c39c52a75f1a46552db7e781e6

* Splitted the xml templates into directories


2010-07-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 266e3d1fbb40082905b2df8fb48352ea8875c497

* TO Implementation (splitting templates into reusable parts)


2010-07-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c25e0a0cadff28320105ff9e85d765ee4f5dcfb3

* TO implementation
* Finished host- / servicegroup to template


2010-07-20 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 5849a39548e8991381f7e26fff8a9ab0a6648d91

* dded missing folders...


2010-07-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 079d500cd4ade12ece7e3c7277e46b2410f4ee54

* TO implementation
* Splitting templates into parts
* TO Hostgroup template
* Icinga 
  stateinfo class text wrapper
* Makefile fix


2010-07-19 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a8ad6cf7f623fda2ea383e76820cf51461892013

* Fixed Makefile files.mk (#fixes 619)


2010-07-19 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 41eb9bccaa9e800277153252084589d70d6fc8c2

* Several IE compatibility fixes


2010-07-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8f14e0531ec3fdb6a1c21c2ca4567984632a0864

* Adding new TacticalOverview template implementation


2010-07-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 641f71b0d57ad234af26f05262e5637b691fbea8

* Splitted the xml templates into directories


2010-07-15 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3789100067f21cb28924c1b81099999966c7f490

* Fixed Portlet view. Maybe persistence problems can occur


2010-07-06 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a491803e55de8998c01b3bfd1a0789fe9de26aeb

* Added disappeared validator in User/Edit.xml for altering connection types


2010-07-05 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 9df636392aa57aa3e1714a4223add4da1529a067

* Removed premilinary (and forgotten) email RegExp with a real one


2010-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e8f76faac37d1d3ca8c1a22a62c262ba6fd22ba7

* Added makefile patch
* recreated configure.ac for new version


2010-06-30 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 199d2d6d455156debc9cab91cd5994fbe40b70bb

* add make-tarball for make create-tarball(-nightly)it is now possible to create git 
  archived tarballsincluding submodules.


* make create-tarball

* - creates normal tgz+md5


* make create-tarball-nightly

* - creates tgz+md5 with $date-$shortcommithash




2010-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: baecf95fff8b83b58d2a5a2b3f1d532717bc370f

* Disabled cache for squish loader, works better at the moment


2010-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 573ed90aaf6abe5f325621024d857b4749fc7c93

* Fixed installation files (make)
* Added changelog data


2010-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8fc89019ef6abce06a01bf0017b5a0e28842d9c7

* Added just simple words for preference window buttons (fixes #530)


2010-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 904926503c87e18ff7bdb906eb42f11d02f48303

* Added netbeans again. I can't see any files without


2010-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 95db31da67359b14192986e6a7cb14c2e15152a3

* Changed version (fixes #544)
* Added detailed iso date format (global)
* Removed 
  html encoding from truncated text in grids
* Fixed toggle button for auto refresh in 
  grids
* Added real title for title-less pages in template


2010-06-30 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 04eb2818a500889773bbfb66501d670cacb45101

* Added icinga-web.spec


2010-06-30 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 51dfd9702b26b321ac23fda01bb9894151e329da

* Added sql schema to etc and ignored netbeans folder


2010-06-29 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: a980ff128ee7e7ba072b80064f25393f57d35fcf

* Changed user_name character limit (critical /w ldap) and cleaned up a bit


2010-06-28 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 4816ec28fe528aa6280d7dd6aade6584ac14a378

* Added possibility to disable squishloader cache


2010-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7fc8d511b61abcf36022f5a00e4c975754b68dd6

* Ldap AuthProvider fixes (thanks to birger)
* Configurable user uid attribute


2010-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 244061e11a1836e8ca4d5fa89febe3c5eaf119a5

* Changed session name


2010-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 73a57d4d7013e64f96e049be20dd878baae2e231

* License of changelog creator
* Honour the original from Marcus D. Hanwell


2010-06-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8c1745dc72fc66ad18e65beb03dbbd782bbe7906

* Fixed make file list (fixes #539)


2010-06-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a13f0076ff42f84e43d3d5c6cea2159ceb76bb71

* Changelog creator
* New changelog


2010-06-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9362d378bc04e8ae36a8621befb180e40b69b100

* Removed useless doc file ;-)


2010-06-23 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 9c4058315d88a22ee329ff705b857947f67bb0e5

* docs update (in sync with the official docs)this is synced with updates for 
  docs.icinga.organd those provided with the core package.


* refs #513




2010-06-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ac221b9122f3574a749e6d378b842b77ee65e4a0

* Fixed typo (fixes #528)


2010-06-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f3854209e45d65b1632ee7a81233f0e01d15a485

* Removing php short tags part II (ref #487)


2010-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d690017875d712d45cdc5153b76962ca6906efbd

* Fix installed files


2010-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8ab09eb26e4de020a09662d64268f02eb4f0d8b2

* Restored old portal throbber


2010-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f724a24c55a108b17a9dafe9c87d0ef71f3e11ca

* Allow text filter in combos
* Added key handler for filter forms


2010-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1bd2c1fc36937df640ab4c913fe4d4004f0591bf

* Removed old status summary structures (honour christian for the old one, merci)


2010-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 222942ea4938932ac68c20e823f49224480c10cf

* New overall status cronk ready
* Single click for search results
* Simple text 
  status through the JS API (Icinga.StatusData)


2010-06-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9cd74b5b8364964ce7f10b3e1f40531bc161ef57

* Removed short php opening tags


2010-06-21 Marius Hein <mhein(AT)ws-jmosshammer(DOT)(none)>
           Commit: 451261825904ddd5ceeec430b6451a8826c746fb

* New status throbber


2010-06-17 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 0c9f52cf7cbb7ca830504f571b373d0c7cff1bb9

* Added PostgreSQL specific updateSequence function that updates the sequence counter 
  on installation


2010-06-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 09ea56c585320f19ebe344d7b2255948cc1b82c1

* Updated test cas


2010-06-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: d1e815769b0feda283dc633066f1c1e07b21c902

* Changed wrong id


2010-06-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2b90d691cfe2b97283167f1580909f4530d956b4

* eadded doc2sql


2010-06-16 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 3d6f64a2b5ab7069b7b22e787f90a6ac4580c059

* Re-added Ids of initial data, although Postgre won't auto update its seqeunce


2010-06-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f0fb3a2d0fb7a3c69cbde283f31adc0f362f358a

* 
* OverallStatus Cronk renew 3


2010-06-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f1a5592b8ea8cf9a4d07b80f49205c9dcb770b3d

* Added external link example for xml templates (icinga-host-template)


2010-06-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 19d75d482d3e2d9c59efcb160d46bc1ef21dbc4b

* Added external link example for xml templates (icinga-host-template)


2010-06-15 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 45bc95e5cda72aeaf2cfc565566f4cb2f2f94c99

* Bugfix in DBBuilder


2010-06-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7449c542a68aae9ac29cfb94d2cebf71230083d9

* OverallStatus Cronk renew 2


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 65d7a8cb33fc1d8331b7d3b4e631fc65feedf4d7

* Fixed make files


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5a57ced5a11ebc53dceba81d7cdb7cb44d2f33d1

* Fixes extjs upgrade problem


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f9167a9c0d25d7ea48b7d106356890352ff98f44

* Changed extjs debug version to generic


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 054dbba25a367a8532d679083e43cad4d5c06cb6

* Upgraded Ext to version 3.2.1


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fa6385be9a480ea8c0a7e42476770db454be2cae

* Well, added again!


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0930a9f80a1ecfb902388226619063eec95a3543

* Compiled language files


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6692523f09140e8f8994e841a20db7982849623a

* Removed green success splash (fixes #494)


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d369ef1f01fc3cf1fc9f52bffa5a258f6ca98c13

* Pff readded cache folder


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 751e250a387d8a2e39edf952a884b68efff170d4

* Reset changes before


2010-06-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2fee88c36a6c0bef986a2bd343469ba47ccae598

* Removed overall cache from gitignore
* Readded appcache


2010-06-14 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c5b32e56cacd506b86d2f469a255503685e7a808

* Cronk view doesn't truncate html anymore


2010-06-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 632da4d63b756362cffcd547fa00856e94d1a9d6

* removed unused


2010-06-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 75d5ec52643c86669c05b84015dfd9317c885d5f

* Prepared new OverallStatus cronk


2010-06-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8737a077360e29d5d04d307ea6a6deddee4d5d58

* SOAP fixes


2010-06-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bc4946fccf8c64f335f7b3d7199595f5ed6c851b

* regenerated configure


2010-06-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6b106e23f587e9aee460c7b722d2217e3598fff1

* Changed icinga-web binding style to rpc/encoded


2010-06-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 991238ff5d8bc0dbbd8938df220e5ec56eab165a

* Complete ajax filtering within grid cronks


2010-06-10 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 1a57a3b0ae4ff28d0f291d7e391cd33bd1a67ed1

* Added premilinary soap interface for icinga-api adapter


2010-06-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a9655f689dd87af9d2f9af110d5afd550b284038

* Added ajax-driven filter implementation
* Added ajax filters to host template


2010-06-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 94f8df23552cffd3f1b4b05af9f7253026c63c18

* Added AuthKey Auth provider, Authprovider selection in user backend and key-based 
  api access


2010-06-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 86594176388b6b88a21a6ca96ea904eeb7092cc5

* Role editpanel is now like User edit Panel (Pagination, buttons, ctx)


2010-06-09 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a33dc51da330aa7d7f954272e51506185ffcbc00

* Added "Get URL for Cronk" function


2010-06-08 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9bfdf05f9876db1e5370aeb8444328c9cb96ef6e

* REST Interface changes. Not finished yet


2010-06-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1160c320c8f1ae050ea856da07df9e656c79a02b

* Fixed wrong click lables (fixes #485)


2010-06-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7a32441214d5d271996e138eea8c5fe780b50dca

* Adding instance filters (fixes #288)


2010-06-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 5864b7ca3b546900068f64beb41944a3c26bf100

* Invalid session are now closed automatically and cause a login screen after refresh 
  instead of showing the Exception endlessly


2010-06-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f338b9a88f95af84ea43c70eb5d0bb3ca7f47f87

* Added support for oci8 (Oracle) db driver


2010-06-02 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c2e4207107b6dcb7cc849a5e77fda9c0329fb066

* Cache headers for squishloader
* Some netbeans test
* Added nb project dir


2010-06-02 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 736f2096d135a494186295303aace62a329b3db8

* Postgre Test and Model fixes


2010-06-01 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f928e6b9e56c3f5844f6a690680f2833a1d4c1a7

* Added several post-installation testcases tha


2010-06-01 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8e5df928223127f7ccf6edfba7d58add6351f1df

* Removed getPrevious from AppKitException -> didn't exist in prior PHP Versions


2010-06-01 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 5f35317187071fe1789222b759a0546aea5d565f

* Added generated models to databases.xml and removed doctrine autoload definitions


2010-05-28 Michael Friedrich <michael(DOT)friedrich(AT)univie(DOT)ac(DOT)at>
           Commit: 90f7a1e2b73caa43b8c77f7edc608bdb8153e0f5

* fix creating app/cache during make installrefs #464



2010-05-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 44b751ed99581d12197687890a58183e938bb924

* Removed static class path of icinga
* Make files update
* Added new base classes to 
  autoloader xml


2010-05-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d3103c616fcc98d24fcfd2906f2d6845685413a8

* Dimensions for preference editor


2010-05-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d370018a76296604a48ede712828961b4c4f8018

* b-update and db-purge-userpreferences added


2010-05-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 1907dbd419cdd98cd43a75d1edc868447e956924

* Base Models are now loaded first


2010-05-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: de248ff3a29399fbaf2359fb03b5b6a0faf39e44

* PHP 5.2 compatibility fix, fixed wrong reference in NsmRole


2010-05-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 333f5e245f2ff95cf86144ffcfb3891182156f57

* Implemented window mode for the preference editor


2010-05-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ea322bf89660db5bfc0049932915744dafcca9f9

* Updated build.properties.in


2010-05-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9f60e03de366e303d87ed8ce23fd64b716fbcdd7

* Postgre support and more graceful db-setup process


2010-05-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 948ada4f7d6b5772bd679863bb704d031e28d513

* Added make doc2sql and updated models


2010-05-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aee8b390732db8afa24b493a17021595dc4b8a6c

* Fixed login reference problem on dispatch model


2010-05-27 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c32e759373b3477cfd062565b3a7cced01b04418

* Removed doctrineDeployTool - models will now define structure. Added new 
  db-initialize task


2010-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3826417ad3a7a9e4aa745a2ac03fdf487cc07efb

* Real ajax logout feature (a simple dialog)


2010-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ba2f5f43981c99c71c9644ea1e8d3f84736c4461

* Removed magic quote test (#icinga-devel 16:37 <VK7HSE>)


2010-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 58e40a29dca0b423775cd58baa483bce7af74832

* Added new and updated translations


2010-05-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 980548dfd5ed5cd51467a744efbbcfb4ad5787d3

* Removed the doubleclick (THE DOUBLECLICK, save the doubleclick)
* Fixed deprecated 
  issue on the api container model


2010-05-27 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 44a7db82d046f3b747a6f12b899ba05f7cb97661

* Updated DB models (added index and foreign key constraints)


2010-05-27 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 6c191240a121fdb2a073504c9f5937c8cfe577d1

* PHP 5.3 NS fix and updated doctrineDBBuilder


2010-05-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fbf99564e0825ef8f56b89bf343d98c123e4cec7

* Added .htaccess to gitignore


2010-05-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aa3cbb6fd8125010b5038fb92a29498e0f52f202

* Added --with-web-path to Makefile


2010-05-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3b85a8f0d6d61eb64b0bb5060a4234a1f097dd9b

* Makefile fixes II


2010-05-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cd2f0c0cdd9bd4fed6dfaa97e63d40dd7b6cfb62

* Fixed make file list


2010-05-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 201d9c03f9664ce0fccfaea415ce82a7d8e2c655

* Added clean fix for make file to remove all useless files
* Removed logfiles on 
  devclean


2010-05-26 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 952b43ab6eee4cc577e3338118381ed89cd54a28

* Updated icingaScheduler to work without AppKit bootstrap


2010-05-26 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 4d97b35b5ed79d1eeb913e97e02fe6031f6517ef

* Added several bugfixes fixing incompatibilites occured after AppKit restructuring


2010-05-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 97668e54446e5472ce3555abcb2956c0b5f6a78c

* Removed unused files
* Changed principal security handling in xml template worker
* 
  Removed useless security parameters in xml templates


2010-05-25 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: b6d1126b1880e962fd6e6289c9a1ab072677f411

* Privilege bugfix and Static content model bugfix (invalid offset...)


2010-05-21 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f1e727b5f9e3e7b5db77cc377bc1f459c5ed15b1

* Modified AppKit paths


2010-05-21 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 5e820b02684ba840ada253eef20a83e8f447822a

* Added language switch support on TranslationManager level


2010-05-21 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 0584aa136719b983ae7065e05f44442ad73df8d8

* Removed old plugin folder


2010-05-21 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 2aa220103a85f3c1b0b8323cfb3a029bf794ec89

* Fixed installation issues, routing and Admin panel


2010-05-21 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: d30b99d75be4cd7589d6e928ceb9b7a740ace2ca

* Added base64 encoding to BLOB fields (PostgreSQL workaround)


2010-05-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a820639486f67e215ec63baa59ccf2590b27d8c3

* Removed old stylesheets


2010-05-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aec2db918edb119e0ecf7a0af820bed1c11f22b3

* Fixed doctrine model loading
* prepared install routine
* prepared to merge with 
  master


2010-05-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5d76ada6516a98f0b036f8d3bdbc61711e889c56

* Moved the appkit libs into the module
* reduced icinga.xml -> 
  app/modules/appkit/config/module.xml


2010-05-20 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: c5c2975cbef4cfdfecc75c9721fba91a3274b43b

* Added "defaultTarget" property for principals


2010-05-20 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 169aadeed95b10ec7af6a64fd0dba01d8bda8d08

* Added ajax driven principalentry proposal for PrincipalEditor (fixes #438)


2010-05-20 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a493817046965038030af0daa2e75ebf7ea29f8a

* Added output type: image, update icinga search API for ExtJS DataProvider support


2010-05-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 317086cd0a5f95aad6e5a34c546d17ef577fe8bf

* Removed dangling factories
* Added new auth provider concept
* Added AuthLDAP 
  concept with import


2010-05-19 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 7532e360bfedc52a0394f21dad9a644971adb688

* Added routes and changed parameter names


2010-05-19 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 8b84d8200e65dbd7b49594a461a19fd3ff19af39

* Changed paramete


2010-05-19 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f62674cf5a3d9af7647e6ca99e7c9d9ae541ac67

* Added URL-driven Icinga-API requests (resolves #305)
* API Requests can now be done 
  via GET or POST requests


2010-05-19 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: bd1c977a340a5fd7c5cc60acaf3cd0abc7b1d341

* Updated principaltool to check for target columns that need to be restricted itself


2010-05-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6b75697a8612eb9cc19092e27059d6d725ce042e

* Creeate new rescue schemas
* Changed the models to doctrine base creation
* Added 
  base modifications to models
* Added fields for auth provider
* Updated the WB 
  model
* Created new doctrine migration init package


2010-05-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aed5c70d574ea2da050ebd68f0f0eaa09638e6e5

* Restored old model relations
* Added base implementation for models


2010-05-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1c72148ad9737bb33d0b050aebd451968b587b1a

* Added new models


2010-05-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9ce80fcf7ec55a202ca9d62047141e48664c660a

* Added scheme file to makefile exec target


2010-05-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f1887ae340486b575174bc4aac09078ceddf687c

* Cleanup database changes


2010-05-18 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: fd1cb3fd1d4d358b45b1c83d5060f37e37b93e7b

* Updated build.properties.in


2010-05-18 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f14672e73ab3e911d43011096c4da46243c41dfc

* Minor compatibility fixes


2010-05-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a1eddc526e7ebb474101b8dde27b2a3ac1e523eb

* Added new auth privder stub


2010-05-17 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0b40e569a2122f832e3f1a010a81df7d8623e157

* Added basic database check via unit test


2010-05-14 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 453adf234dffcbd80689626d930d9977c9420be5

* Doctrine based DB Deployment, PostgreSQL support, Doctrine core patches
* Started 
  implementing Database manipulation testcases based on PHPUnit


2010-05-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 10d5d411084b4ecbb883166176e0366e44ea0252

* Cronk environment implementation (init methods)
* Rewritten existing cronks
* 
  Example cronk implementation
* php5.3 issues


2010-05-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fbc901cb33e41c4c2c6b61b2712be5ffc25de4ac

* Added log directory to gitignore


2010-05-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5b605c32b4484a1bc8adb866ded4d0eeccd0ed3b

* php5.3 issues


2010-05-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 9f993f45e43952a8a0e816afcf29d0c7a273a0f2

* Added doctrine deployment toolkit (not yet implemented in the build process)


2010-05-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3bf7f1372a340a67d7afe0e93127b9086bc18b8c

* Removed critical bug in remove-module script


2010-05-11 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: bcd456059417a75b655c36c0f6d9363e334ce2a0

* Doctrine update 1.1 => 1.2


2010-05-10 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2af5ffdd79be66549e3b7612dff0ffdfe7de0398

* Doctrine dependency fix, snippet RegExp fix


2010-05-10 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 082d4d050a6c0caca45fcd6c920674d750f00140

* Revert "*Documentation of module installer, DBBuilder dependency fix"This reverts 
  commit 5e5fe216d6e1b8e330006bb97b80c325657ce777.



2010-05-10 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 5e5fe216d6e1b8e330006bb97b80c325657ce777

* ocumentation of module installer, DBBuilder dependency fix


2010-05-07 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e8e2ca1ff3b9646e5b8a218ba1ccab2f712a43f2

* Code Documentation update


2010-05-06 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 59e695e7eb483adaa79e17a3f3d8cd84b553ef9c

* Updated doctrine to 1.2.2


2010-05-06 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 83122b2e6f125432302a898a7c281736e032e24e

* Compatibility fixes, added "Change password" to preferences


2010-05-06 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 1fe4e1d9a3370acda3812dc6fe1ce0001983f1cd

* files.ml


2010-05-06 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 26f05cd903476144e0b41aa23d8802be56808a1f

* Module installer cleanup


2010-05-06 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 3d6c3c1df93470386e4420d18861ebe5b52d48c8

* updated module installer, now uses unique merge/diff actions for xml
* rollback 
  support added


2010-05-05 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 94496c00678625408e8676782f09bc4a856b784b

* Fixed compatibility issues with PHP 5.3.2


2010-05-03 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: ad463bd63dd4cd66fa861cacf7c69c064b4705ed

* Scheduler time fix


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 30f383bf44bd8a6d6c509d97716be18efdd41285

* Support for new icinga-structure/cleanup


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 88226769bc814259424b84b3e60f32dcc0d43a04

* Stylesheet update


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 0726f556b6c382e598e9ba931618b9ad4d714aa0

* resolving merge


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 485d7ca27528a9e0a08221f9b4d16c000debd5f3

* resolving merge


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: cf3228c03ffd070d3d006af7c1a9e316ac25d992

* Scheduler documentation


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 03474da12f6761515526930c311c12a690751cfa

* Added Pagination for Admin.Users


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a1c46cd0d25b7bcd8ff22e6d8f53b1b8a19a4bfc

* Fixed change Language and validation bug


2010-04-29 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: e2de3f85264e862f68bff4ac35d2eda07ff0955a

* Simple Preference editor added


2010-04-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 4f14dbf02eec800db987dd8b7dfc9cac502549c0

* Finished Principal, User and Group editor; Added Preference Editor and Language 
  selection


2010-04-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: f86c34b23e33395e33ad2d6c47f7229bad4f9ae2

* New Principal Editor


2010-04-28 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: cbbaa822cf31d2b3f48f6e6ecc108188bb6a50e8

* Plugins are now modules


2010-04-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3ba76bd7f4c2508d2e25c5635d03a5b47818fe3f

* Implicit match_types for Static content cronk (match_type in datasource xml)


2010-04-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 24eba9ebf2e113b3b7fdafb8de0d5f252176ad54

* Changed the default log interfaces* Finalize class renaming* Removed some view 
  implementations

* Fixes language file creation

* Updated Agavi to version 1.0.3

* Prepared renaming of classes

* Sed renaming of base classes

* Renaming files

* Rewritten routes.xml to match module guidelines

* Rewritten sub namespaces in templates

* Added multiple filter columns for static content templates

* Rewritten the dom parser for node index creation

* Moved icinga libs to module

* Removed IcingaApi factory (New interface provided through model)

* Changed the APi handling within the consuming cronks

* Removed Icinga from Autoloader Spec (becomes deprecated)

* Prepared to remove all AppKit factories

* Changed the exception template to plaintext for ajax debugging

* Removed deprecated viewextender stubs

* Added temp early module initialization (web) for autoloads




2010-04-23 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 2711645817b06927dfc877ca4aa402e9fd825b93

* moved rbac_definitions to db


2010-04-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9a97c52ab5b195c9d00fa771c47093a31512effa

* Removed icinga-api from installable files


2010-04-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5859e3a1c40146bfcce3cca755976e1fbf1bc1b8

* Make files fix for install


2010-04-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b2b76714e2130dd2eb3f9f56cf3c7a93ff423ed5

* Added multiple filter columns for static content templates* Rewritten the dom 
  parser for node index creation



2010-04-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2f70567aba866becb19f267bc3cb6dab88dd2521

* Implicit match_types for Static content cronk (match_type in datasource xml)


2010-04-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 955c61162aa24cc577a328b4d2013862a61696e0

* StaticContent output type problems
* Cronk id persistence problem
* Cronk request 
  type problem fix
* Reduced requests for the listing cronk


2010-04-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2e4842df57b0fdd084b72a4c6489b086e32f9444

* Cronk loading
* Layout handling


2010-04-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: aadcdefe598be174513d44c90dca779c69903c05

* Cronk handler
* Layout handling


2010-04-21 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 45242a1bd82765e8df477b723fde65386031ab5f

* Removed "config/plugins/", modules must specify configs in module.xml now


2010-04-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 675cf558519167e7e8f6c6dd701f30701c1e6af5

* New layout, extjs viewport
* Seperated layout and cronk implementation


2010-04-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 066afb0653eea735d948c27189cc1ff74d7d3b60

* Moved and cleaned up libs and namespaces 7


2010-04-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 17c8d78f2f449fe9beafeaf251a0013343b3c428

* Moved and cleaned up libs and namespaces 6


2010-04-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a3b121064382d826095d850c0d37506946325b8d

* Moved and cleaned up libs and namespaces 6


2010-04-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a73fe975d93fd5e2e97b422d884502e84bc7c6fa

* Moved and cleaned up libs and namespaces 5


2010-04-19 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: a8de5917da848d12a19168f3214b7888d2a5625f

* Changed import-cronk and export-cronk to fit to the new cronk structure


2010-04-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5c8aed1a087df8ed249156167d1a1c2fb0329112

* ved and cleaned up libs and namespaces 4


2010-04-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 250f57b57286fbf75b2b7707d31b79c87c9ce29d

* Moved and cleaned up libs and namespaces 3


2010-04-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bbe3f47bdf19aafd6f8319203aa477a8a9127615

* Installer fix, which installs all *.in* filles now


2010-04-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2317b4ac151f29581b8e9bb54d8684485f81f669

* Moved and cleaned up libs and namespaces 2


2010-04-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f56a80322c2fe15a4faa7a58e99e0cbb97394247

* Rewritten templates, seperated modules


2010-04-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0a312dfafc8d6184a006fa0f8a18679ea56c738c

* Moved and cleaned up libs and namespaces


2010-04-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 26eb9bf47199a70250f513af7efc254dfb9b124d

* Reduced cronk inline script code
* Reduced manual object instances
* Prepared ExtJs 
  menu rendering


2010-04-16 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 84cbfab10d4b23c721253a988fb8c05c8e6694db

* Plugin translation fix and documentation


2010-04-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5bec9d119bad6d345afcd1b7ea488a45691c6e5b

* Checking for the api on configure


2010-04-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 75a8f7237d6f07795fe679283d91259b0fbc92d3

* Fixed install routine to match git submodules


2010-04-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 13439212a83495e30fc3412587cb5a5efd611de8

* Reduce libs to the modules


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9023fc3585cf3a7cdc0662a372f10739afc9cdd0

* Started cronk xtype implementation


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 47ec8ccae00fbfa803f33c12980166c5dd398424

* Added language files


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d5a202b56f7548a33e0508514a06d7776efadfbe

* Added new translations


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: be1a9d48b0bfef66b314067596bff3f7edf85450

* Added translation submodule


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8d1b42f1f0643d9baaf068c594c21a6a1222a6c9

* Removed the po files


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 836c16330dd34bcdd99056662257d17b00f07e9c

* Seperated git for the i18n files


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b4844ba5087d75501802c78105f00972e7f5781a

* Welcome cronk show the real base version now


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3b3745e5d82da29a1268e5fc61486c0b3c9b3f34

* Fixed reserved word issue (fixes #370)


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: be773af8e68ee5d26184cc69c82739349589e5cc

* Added files for install


2010-04-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5aa85f877a5397a4aafa600ae307dda28fa4148b

* Create new layout to support viewports


2010-04-15 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 7cc6a573451d8332ddd5b02f2be685f4c0221b8e

* Added schedules.xml


2010-04-15 jmosshammer <jannis(DOT)mosshammer(AT)netways(DOT)de>
           Commit: 15142ffab308441c24ba75ec9eedb6a71f612a90

* Added changes of jmosshammer/default (plugins and icinga.xml)


2010-04-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 54766df17f8400909fe9b584594f46a3e86e1ee7

* make scheduler install (jm)
* make install files
* removed res from source files


2010-04-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 845bdb3ef3a2fe3a898923aae5fff0ef5225e3c7

* WTF?


2010-04-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 28cf8e662bdc0cf1e8d19b83bbc5b6c7d0a331a1

* Make fixes II


2010-04-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d6306294581e03ec8be79244a05850457a72ac39

* Make fixes


2010-04-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ad90a327566663c5e1cdc246e4a89b1b83ab7a20

* List of install files


2010-04-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 29cea7c71b260606d997c528d4801d56c53b43e3

* Release sub modules from global scope  
* Moved configs  
* Changed config ns
* 
  I18n structure rewritte for pootle
* I18n parsers for structure
* Make targets


2010-04-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7af70f5710e6f78acd2b6a9b14df235518c401f4

* style fixes


2010-04-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 70e90b37c104ce9ea3ce4d0b4dd45ea79ecd61b6

* gettext fixes


2010-04-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 53c065ed4031fc9baacdc9b4f361732db05e50de

* Updated languate catalog


2010-04-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0a9c9710da59659b1664578503bf6d8abc408c9f

* center height fix


2010-04-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8500628d3acb29e9ddfe8f5922b605ebb24b25e3

* About menu (fixes #372)i
* Template cleanung


2010-04-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9b0dfea30ebc7f6ab7acff3b4a5480e459e4b29f

* About window
* Some actions for about


2010-04-09 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f90647ea86869218f9b58c87f5d51bc32dec341f

* Added IcingaScheduler


2010-04-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dc1cd1841f3a3433ec0169ba0311a4c81246936f

* Removed IE7 confusing cdata sections


2010-04-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e31b64675bc8b7477932d9097448bef3a26c2b6e

* Adding a loading mask to reduce eye pain ....


2010-04-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3caad1a72556354352b290a37c0f00e0ee8f6dee

* Fixed missing files for make


2010-04-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 088be685b26abcae0624ca21ec8b797c96b1ebd7

* Sliding tabs to reorder cronks by drag'n drop


2010-04-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 28faeacb7f3ea20b8fff04a2c9ffb8972f31620d

* Api sync fixes (match constants mapper)
* Drag and drop tabs


2010-04-08 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 11845cfded7275154a1080ea09ec0ecc54e9c73b

* Removing the last route of a context doesn't screw up the routing anymore


2010-04-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7586cb49ea67d9eb4188adf5a56347c96ac0a820

* column renderer fix


2010-04-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9ce0d428ffe78bd6ea9d7fb24269aa43b3262996

* Fixed files


2010-04-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fb2a6573855beda5fb02f4a9af121b7ff12cce12

* Makefile changes (targets, output)
* Doc files for translation


2010-04-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: df396f38a8c40aab9e6c8629cf7c5ad40f660e95

* Added missing files


2010-04-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6ee9ca1e34ccbca44d0d8718a9c3f5c0b717b867

* Finished translation template (fixes #279)
* Added tools to handle translation (po, 
  mo and json files)


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 13370f4ae351fdb43d3183fd16c945c45fb36de1

* Disable cmd window on sending to prevent doubles


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c482172c77d933d88f6e30d8890c6a4470fc7a2c

* Command window width (webkit)


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ff132b2eb79d0a6dc8a8b97ea26a9ce0b7bf6e2c

* Merge with plugin architecture (rel #338)
* Search object status (fixes #339)
* 
  Global icinga status js object


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 226d01e41688a013bfa36b0f1e1474cfff806c18

* Merge fixes


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 43f7c08a332165c34c90122bdd7f944b37d64d51

* Removed old session provider


2010-04-06 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: f24278c8b4d88b997794493439f7f24d4d6c188d

* Changes for console-context compatibility


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: df4f3cb556ea4e4138963044cad90c17e97b61b1

* Database model, newer version


2010-04-06 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: ea0f4de93e69f7f2252fb04dd74c3276cfb6763b

* Icinga.xml Eventhandler now distinguish between context


2010-04-06 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 66b07b30f41ee865dfbac087ef8dbfc97b4294dd

* Icinga.xml Event-Handler now distinguishes between contexts


2010-04-06 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 8f5986c816ce38b5d01820765f2323a0ed9e1120

* Plugin folder created


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0c479308a5ed9a58e5e7aef9d5afc0ba8933baa2

* i18n
* user preferences


2010-04-06 jmosshammer <jmosshammer(AT)ws-jmosshammer(DOT)(none)>
           Commit: 13f9eecf5fb633ee0a775f47ca8e7e66924332bb

* Plugin-wizard added, minor changes in AppKit for console context


2010-04-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c5ede02d7a24eec47a42443bc9dfab3c32dc1a7a

* Filter window height sync fix (finally)
* SQL constants for 'not is' (API patch 
  created, fixes #362)


2010-04-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5f5520dee88e984c937a06c33082b7150e14c667

* TO data in a seperate container


2010-04-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0bd5a18f251e71b486704fb975ef820f47558a03

* Removed link classes on to


2010-04-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1e11ca76441d0c7d6de57ad9d8b076c27a3ff9d3

* Tactical overview link fix


2010-04-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ab50a6186b2ba5205f811ac7a4cc7f62a88c002f

* Installfiles fix


2010-03-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 73aff0e782a7b080280941e7f85df5ba74c8d500

* TO changed layout for hostgroup tactical overview (todo: links still missing)


2010-03-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bac9137b26f6187b4103a7010310f4038efa7239

* Tactical overview cronk rename


2010-03-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 494df893df7f38a66e39b6043f0e484559f4d4b3

* Tactical Overview with CustomVariables
* Changelog


2010-03-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f4e0814dfcf373699bdbda81ba02d9299e14c0ef

* ExtJs update to 3.1.1
* Tactical Overview with CustomVariables


2010-03-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5be9d7d23983ac81f06fee594d4233a28c92b881

* Fixed content-type for application state


2010-03-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 784e0756433701eddbe8745b8bc6bcc06f5fbb97

* Rewritten persistence handling of grids because of prefilters and userfilters


2010-03-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ecda9ce2e54c4ad3527e998e96bb6d0af5368aa8

* Fixed files 3rd


2010-03-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7b38dfaf4c525d845c48f86bc0bf01de0dea8070

* Fix files 2nd time


2010-03-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cb4d65d2032f141e7fe2cf35254302d72307bbb3

* Added missing files for make


2010-03-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4377b08f69e0cab8b70794f7c941dace129eed24

* Proof of concept, http state persistence
* Buffering http state persistence 
  provider implementation (fixes #184)


2010-03-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5ba7688bae8afb7d0e5e820b128ace37e3190c47

* state provider case study


2010-03-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 424905719f36b61bd4adb3cedf6b297402968630

* Removed ugly files from jsgettext


2010-03-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f8080c186f2451bc21325dcbc17c22dc684851c9

* Added new files to the installer


2010-03-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7a631fc5dfd9057f0c47b602f8f0a6ea12ec01d1

* Agavi update (v1.0.2)
* New library jsgettext (see VENDOR for more information)
* 
  Probe i18n with js
* I18n javascript implementation (refs #279)


2010-03-22 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: adead8fdd273a3e5d5bfbf2e6c9d8763c929ef79

* Added columnRenderer to display status information
* Round corners for status info 
  in grids
* Concept testing gettext js


2010-03-19 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 08e8ec7c27a1c96e7bf2de04a0d2af890f8ec225

* Jumplinks from host-/serviceview to hosts and services (fixes #334)


2010-03-19 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 99c7a0c2baf312ecacabc0a8237961783b028161

* Template filter for multiple fields (Intergrid links)
* Typo (fixes #335)
* Added 
  js temp storage class (used for grid templates)
* Customvar filtering for host- / 
  servicegrids (fixes #336)


2010-03-08 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: a216139199b4907d9e28aa9da3130cec3cc15477

* Fixed initial layout (some cronks are missing)
* Fixed history relations for links 
  (using the name as criteria)
* Cleanup the initial event firework a bit


2010-03-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: da47cd26796890286d00844607be269724b284b8

* Typo


2010-03-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 0839fe93f98adc5be8c79ad1f3f7e3a062adeed8

* meta


2010-03-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 331c4f933f506398dd011a37c0c95846f1337f7b

* Fixed webpath errors with querystring and without rewrite
* Fixed #304 wwwrun user 
  was missing (fixes #304)
* Group / users activation
* Version


2010-02-15 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 9c702535cb9218571cf95dbb0d52618c9b5d24ad

* Removed old YUI body class


2010-02-05 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 93cf81f81d23d684fdbe8ce2d6e60499fc22b79d

* Fileslist


2010-02-05 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: c2c200b15ce5f6962f25cf469eddaef72937f9ed

* Filelistcreation fix
* layout tests


2010-02-05 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 5c861cedff653c27dc874f3de37c6a2dc6e2f378

* Install files made clean
* Seperator for the top items


2010-02-05 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 5271c1119c244360f0ab76889a3994828bdc185f

* Cronk category default activate flag


2010-02-05 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 2a7cfd387b2ce4139739b71098351ca341f8b059

* Adding security principals to status summary


2010-02-04 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 038c4bdfdf5e7e70718bd374475d231b5e441c94

* ExtJs components


2010-02-04 root <root(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 99216cdf14d1ba951a0807a88000a85c375e636b

* Reducing mass persistent data of filter settings
* Implemented categories for the 
  cronks
* Cronks for usergroups only (fixes #262)


2010-02-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: e537dd472a44afd0c31b928d341dc2e14af7423c

* Component testing


2010-02-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: ed5bbf6f74e474bcaf406d01261b6adb31e8d28c

* Meta data (CHANGELOG)


2010-02-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 881544a394b0e0f8e7d19b5133b2f3655a36b657

* Debug information for persistent data


2010-02-03 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: d2e87b47ecd0dd8f6a195e8768e408c9ded67e01

* Changed the auth framework (multiple providers possible)


2010-02-02 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: b157d9a9f264f8be585416c73dd756ec949aef4b

* Changed menu (monitoringResize)
* Default style
* Head items stuff


2010-02-02 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 3a69638885f84cdb3e325e09720838c5e6ab8c51

* Rewritten component handling, top components still missing ...


2010-01-28 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 929b9a2984d4a8ff1ef287d4804c068944566ff6

* Modified INSTALL text file


2010-01-28 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: de75d227e976ed7674b46bfde3b019a9c16f7670

* Wrapped the static content view into a div


2010-01-28 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 1340175d0eb9d3398c9aa9e9c509621c04ee1faf

* Settings for grid panels
* Auto refresh for grid panels


2010-01-28 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 66b3f4e88306ac91e7a4818bf3e99d95ba4b03b3

* static content - additional template: modified styles of status tables


2010-01-28 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: a3ffb0e821ef3b8e432f7299f0881955a2021838

* static content - additional template: modified styles of status tables


2010-01-28 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 63c01c392aa56b0781d16cdf8acd12cca0c0c046

* static content: implemented if-statements for templates
* static content: added 
  conditional display of rows to additional template


2010-01-28 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 8c5dbeb32aa22213417e6248194fa99be8fc1353

* Fixed cookie writing failures
* Fixed bug restoring the filter elements


2010-01-28 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 1048056e9b8cf4c6923fe37b1a136f45c75a9ac3

* fixed queries and variables in additional template for static content


2010-01-27 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: a32d8d7609ddfc9b8b1e748d251cbd7e0c883fae

* Grid filter are persistent now


2010-01-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: d8485fa992007b59cee176e264ebbbb36f612a52

* added system performance to additional template for static content


2010-01-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 8d4ca690b63573def19998ab99d72de5c720bbca

* added styles to additional template for static content


2010-01-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: eddada8827e63b33c50b118e271ffcc83ff410db

* static content: added example for service groups to additional template for 
  tactical overview


2010-01-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 51c5dbc5bdb8939e78d13afe55f3d6632ba41fbf

* static content: added inheritance of filters
* static content: added another 
  template for tactical views


2010-01-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 1d25578a6eb00dcc5038b593a37f9d4fe1dc7568

* static content: removed debugging output


2010-01-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 99a606e690f27f17a61168ac52a2ffe6a8400cc6

* extended static content by sub templates


2010-01-27 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: e780ff3289df6b94196a96048d552f35fed59395

* fixes some generic php bugs (fixes #256), thanks to Ixan.


2010-01-27 mhein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 46cef5af7404182892e6337eea5e7d89957ab8e2

* fixes some generic php bugs (fixes #256), thanks to Ixan.


2010-01-27 mhein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 867547f1803b6438d6f81d8d8114e62a74247725

* fixes some generic php bugs (fixes #256), thanks to Ixan.


2010-01-26 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: fe7752e8cfb0ec9459f3f29af7f2143f15d49357

* Made filter persistent


2010-01-26 mhein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 88c2610748be2b731ff8f66d545df2282bba1fef

* Added ContactGroup principal
* Added CommandRo principal
* Added with-icinga-api to 
  configure


2010-01-22 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: b7501ea350553a681cc9c67e651530275bef1d90

* Readded selection model for the service commands


2010-01-22 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 9450342aec8ac3646829d3336f1cbfe77860a622

* Weekend save


2010-01-22 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: f35bc915b532d2cc0b447980de93688a01c791a0

* prepared static content for handling of sub-templates


2010-01-22 Marius Hein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 3de453e4b619d5570cc7f3883b27528176033f2d

* Cleanup


2010-01-22 mhein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: c59e1fb6c8a7c584ced1dbd129c8a38c58ff1529

* ExtJs stateful persistence, finished the tab and the portal


2010-01-22 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: d407f0902ea87938fd1a0f5df5d9848cc265ef83

* static content: added access control to queries


2010-01-22 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 28f61e161d560aea03f4b81b761fbb416234a95d

* static content: added use of singleton methods for post processing


2010-01-22 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 2132d3ca8314b4d689c9b8c3cc516e166c38ea2d

* static content: fixed repeating templates


2010-01-21 mhein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 050d1d3717bf4e6174a74c9d45a3aa36edfaf86f

* IframeCronk is now height persistent
* Rewritten all of the cronk async update 
  handler
* Starting cronks hidden, after render displaying is faster


2010-01-21 mhein <mhein(AT)sasquatch(DOT)local(DOT)itsocks(DOT)de>
           Commit: 5413e6d3805c0b43b69f3a43607de111db805e71

* Defered creation of persistent cronks
* Added a make target to purge the db user 
  preferences


2010-01-21 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: e38bf36ba3ad62a756aaf52c7b82e26384b58cab

* added base for repeating sub templates to static content


2010-01-21 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 35bc0f5571a3e99cbceccfb02e794af3f9921ec6

* prepared generator for static content for repeating template definitions


2010-01-21 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: e59435fbe51e02b1146986d8336f4e1005b4ad27

* added base for tactical overview via static content


2010-01-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 2ebdfacbd457677d7dfeb39484883a01e552ea63

* added base base methods for static views
* modified template for tactical overview


2010-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4c40955b816e6318994b19532b5c7bb70109fe92

* ExtJS stateful persistence (ref #184)
* Portal may be persistent
* Started 
  persistence definition of template views
* Some extjs render bugs occures


2010-01-19 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 0514db8a267acffedc0f9bdf021c52105b538d80

* added base for static content


2010-01-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cc247176928dddd51e8ccd17bbb0536667b85226

* Added stub for a tactical overview cronk


2010-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0280679d910cc08094727756be5b08deef1c2337

* PortalColumns determind its column when dragging over


2010-01-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6be488d5967b8da1e7844fe0420581fd5a1f5aac

* Ext stateful components ready (ref #184)
* StateProvider encoding handling through 
  json
* Tabpanel is persistent now
* Cookie write filter merges the data now


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a438d4d0a72d90273797c2389393cdf44bc001a9

* Fixed some IfModule issue caused a 500


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 17574541f8fb425b88b85b9c818a9f554b1059e4

* Some doc for new install process


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8a0791b0df50fbf4fbe67469e320cbb125bca914

* Added a install file for the configure installation


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6dc493ac2088f95daa319e100575ecd3b6ef0b8b

* Added filter to ignore *.in files to copy


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 62cc2ce8b79c965108c7ddb3333e37f57f08e33f

* Some files to gitignore (files from AC)
* database.xml is now rewritten by AC


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6b9e0fcab1585474187f748fa2f3cd57921feef4

* Fixed up the mysql rescue schema
* Autoconf configure installer options for db
* AC 
  is rewriting the phing properties now


2010-01-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 138f2fa8ab8f72f150b6975a374409cdb9eb06b1

* Added some persistence ids
* Updated phing to stable version 2.3.3


2010-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dbb11b99099dbc556e11b38515dc935ab05c3789

* Makefile fixes: help, targets
* Install permissions (fixes #245)
* Drop decision 
  query for phing
* Adapted phing db targets to gnu make
* file catalog update


2010-01-13 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 2e76fddd9370842d977e3c37140760fab10c0425

* updated copyright string


2010-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6d5ce1271912f4a8eb2655cafb8a3320005d5f93

* Changelog
* More states to status icons
* Status icon files (from org icinga theme)


2010-01-13 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: ae85fe1424272035a32990cdcfe33eff9c4a17e1

* removed minor js bugs from status summary


2010-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 74f8c84b6c5d05924e4caf81b2314996cfb25eeb

* Fixed install files catalog


2010-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 73a3dbe1e966e100e2c8ca09f63edb09d2fd9432

* Added logic for status icons
* Removed some antiquated files
* Fixed a bug for zero 
  replacements in format parser


2010-01-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8da57b609bdf1b9b785eaa518811a4cbeb3beed7

* DB field for class length 80 (principal target)
* CVs different for host and 
  service (schema changes)
* make fix for files
* removed temp files


2010-01-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8195b669382218e1390f954cef2b05d58a73bff8

* Added status icons support
* upgraded extjs to version 3.1


2010-01-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4cb971eb941e0d454769354e4f2c6766368633a5

* Metadata


2010-01-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7dd68916f03a0870f90c61da105cca1baa7243d6

* Added host and service status filter


2010-01-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 80e8e89f2e5e827a2a3f57ade8bead4206e0b323

* Fixed filter window size bug (fixes #232)


2009-12-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 5d4862938c089617fb5654d511c686f766c94e4b

* modified rewrite base in .htaccess


2009-12-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a0c38968f629d0075b98b99ab775b89b60dc99d5

* Authority objects for object search


2009-12-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2aec9b1ba485cfac8b2c31f603180ace5ad3413f

* Added seperators for command entries
* Added full nagios conform command entries 
  (closes #183)


2009-12-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ff7467da2ab00c72def3665cc2012122d609051b

* fixed make construct


2009-12-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bba7fb57d30b5224ef2572859192dc7d47b0d096

* Finished principal authority models
* Moved icinga libs to a new home (more 
  generic)
* Refined xml templates to match the new security models


2009-12-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 7cd99d7c97c298c67603b2960a3fa8836507d13d

* finished principal filtering (ref #185)
* sql scheme
* xml worker filter for 
  principal targets


2009-12-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8b97e85cb0dd64a2ef97a848abc86767ba8ee334

* Finished principal editing of users and groups.


2009-12-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d795d3b33a465af2726f0a3e93ba2060ff8b232f

* Principle editor
* Fixed undefined on notifyMessage with three arguments


2009-12-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cbaf2ebd243a44ce24a554ec5566e62b57009650

* Fixed exturl (fixes #186)
* New api needed


2009-12-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c21c98607de55c12697317231eec96128d9b8d71

* Fixed missing notes url (fixes #186)
* New API is needed


2009-12-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fface55281bcdd6c86ad76aa76889a1d9e858e9a

* Fixed files.mk


2009-12-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 112ea2e5dbd69c1c3ed3f5c59398800b76b366af

* Peinciple editor (ref #185)


2009-12-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 80358dd49689bfbdcaa6b0d7d1a4a837e9a2b192

* Principal editor, ref #185


2009-12-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 51db049b38ed6dfcd8d31243e19ac33b506ed8cc

* Fixed symbolic link creation


2009-12-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a74a16488571cc135d62334b528051c3937689ad

* Implemented some principal and target shortcut methods
* ref #185


2009-12-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0cfec261b8d7d23a2276f4be0c7cacfe9d5fc93e

* Added some make targets (devclean, create-tarball)
* Release ready, I think.


2009-12-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 01ebf1d494786b6400df646d2bba45d73e2e99c4

* Prepared working with principals
* ref #185


2009-12-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 402c761ff9d38d95d33a28f67607c06439c6dd61

* Added principal creation
* Fixed er model and php models
* Fixed schema


2009-12-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f16934cc771ed3b3c20474133459c5641822e570

* Adding some target fields (ref #185)


2009-12-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 33e3588c4f2061b97d57841384848a87e5b664be

* Fixed dynaloader errors


2009-12-04 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 649dd5afa45cef02fa88e8bd88101e646e6146aa

* typo in i18n keys


2009-12-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0b0cb1f31410d76d53834f1ba76a44306d98f2ef

* New rescueue scheme
* Added new doctrine models (ref #185)
* Some notice fixes 
  within the html helper construct


2009-12-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dcc2b00dd6171c3563687cb1100aacc6d7435bc8

* Added the real files


2009-12-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ff43b7854ccf8713cd414d1af97d4d29a5e492ec

* Added install files into a seperated make file


2009-12-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 08177d51b4941491539a8129569e3e2311dbe563

* Database fixes
* Prepared multiple scheme support (oracle, pgsql)
* Added 
  principals for later privileges


2009-12-02 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2e5f1d88725fecb8301046faa021b835a645de92

* Make files (can not stat missing files), fixes #206


2009-12-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 8690838fec20812df53cde9543aab47257bda323

* adapted notification template to new columns
* added conditional row check to 
  template worker


2009-12-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 04fa070c6564ae0cfa20e5450e531fb290c8d12a

* further changes to status map because of new api columns


2009-12-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 058338ef13651c98d6fff8c46ef711bf0983779c

* further changes to status summary because of new API columns


2009-11-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 43bec3cd9ecf2ae29cbf61c3a7cf71866f68c224

* changelog


2009-11-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 92e505805f154e629c1180c09b4d6765fd45bcda

* Fixed opera array type problem
* AC auto detect web user and group (fixes: #205)


2009-11-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cfac0c1530d618cc82baddadd3cb3e30324db012

* Opera array concat fix
* Commands


2009-11-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 56dfb52ebbc7f3bfe55fae23fb3685484f2a4d43

* updated status summary for new columns


2009-11-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: beff212bc08323b163e256516f45614135f2e154

* updated status map for new api columns


2009-11-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 0fec1d664b5d58bf66601a7eb5a23428c6a9d7bb

* updated i18n files
* modified status popup for new columns


2009-11-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: b3deabeb6891e932983fc8ffcb102606e2b88953

* modified template worker to use new api columns


2009-11-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 4c728153c20914639106c20f081db22ea57e7afd

* udapted for changes on api


2009-11-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: f8f7ad44a4a6f40c3ca5c0bc2e03bea8635035d8

* typo


2009-11-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d01afe96037495e3397f12c440bc14f8b6e8943a

* Command sending


2009-11-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 704b9a0e7f42a6f60f0e1640d7688630b937c829

* Command interface forms
* Command sending and invoking the api
* Time, selection 
  and command based auth testing
* Added ripemd-160 hash algo to js
* Better xml 
  replacements (constants, fields and a fixed set of methods e.g. author, instance, 
  ...)
* Json form submitter for ExtJS


2009-11-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2a862f5e8092dde7daa9480c4100121b0640f5ee

* Updated ExtJS to version 3.0.3


2009-11-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3e8c64e7b3edc62247e0e05d3812fc25d16eeee8

* PHP notice errors (fixes #182)


2009-11-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 62906ca4f677101d5c4659b8cb00f9bca8005950

* Dev metadata


2009-11-12 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3c379dc2079dc55bb0b5bb19b2aa9ad74dce5f52

* Fixed no admin users menu extender
* added deflate to htaccess


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 229dba577f75b9b44c647859d129417a1d655fca

* Make adding fix-libs to add js symlinks


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dbd8dc6d3e7a90400972943721898b08a805cce0

* Makefile


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 78ea8e33297474091ec2bcbc0bd4844adb578946

* Installer libs fixes
* Added ExtJS example ux files


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 669d1827c12e0aef73c49f7bc60489d5651fc057

* adding cleaner script
* changelog


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b6924fc230bde57184222fa1a822ab47966c335f

* Changed create script
* Recreated makefile


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cd1b15b9571e4bbc3678d06ad43f11664e00a8ab

* Removed temp files


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 92bc23066822ff6381a221fceb47055bdb28dede

* Changed makefile sources


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a872371c3970463c5397a25d96e8cc1518a1a26b

* Added makefile howto in doc


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 10a327449c14f449e054c19aaddf5c853b66ac81

* Changelog


2009-11-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5600e1097b1a40edc18f24c8639e44e26b02526d

* Fixed installer
* Added bin owner and group


2009-11-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2168aa8c626a3b07780378377f32edc448e18c59

* Removed make garbage


2009-11-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 276036467deaa2e2c5e80bcf50b6eb954071248d

* changelog


2009-11-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: eefb0c211f289d722da018677016102f63ce0abc

* changed make toolkit


2009-11-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ffbf227d952e3fd7d12ed825d186579646941e1f

* renamed helper file


2009-11-03 Hendrik Baecker <andurin(AT)process-zero(DOT)de>
           Commit: 9ddded8bac8efb047da4fa9af834843902df0678

* Added first steps to configure / make install for icinga-webHints:The default 
  prefix is /usr/local/icinga-web/ (not /usr/local/icinga/)

* to not disturb a runnung icinga installation.


* make install will only create possible needed directories but not

* copy any files to the destinations.


* cherry picked the configure and install files




2009-11-03 Hendrik Baecker <andurin(AT)process-zero(DOT)de>
           Commit: 84c54865ed9d36f0cb12405387b5349d5ee56b79

* added configure cache dir to gitignore


2009-11-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c6a68ee063a27c02d3f28c9649cc2ba05d701261

* updated helper + Makefile.in
* removed deprecated libsConflicts:

* 	Makefile.in

* 	helper-scripts/icinga-web-helper.sh




2009-10-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5bd2e9dc5b3a0b928291f68bdeaa325886e801e0

* top nav style fixes


2009-10-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bfbc559913b7ddc771a10b40411875fa249da51a

* Make the topmenu ie safe


2009-10-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 95622256a9a47f7236771a89d1574357eab755eb

* Really removed yui


2009-10-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0f926eb0686965ff105602bb276a771a0c14a8ab

* Removed YUI
* Changed the header menu


2009-10-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3fbddd6701cfe2d1037208e2a32556db0b5cb3cc

* doc changes
* cronk config error removed


2009-10-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8cc8e94194189257f429e9dd38cf3b3986f1cca2

* Changed version
* Testdeps displays required tests


2009-10-27 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: af77c6ca1d911b5bac132bd615b01807e6b474e6

* added automatic updates to status-summary bar


2009-10-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: f4f8e342087c86767c34db2ae2ef817b1ab87a23

* South frame
* Login redirect
* Padding hello


2009-10-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 576f5ce01671c4efabaf6cda46a2776768e223e3

* Change prerelease number


2009-10-23 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 4fe4079931355a2aa52b1228e5edfb5090938e17

* modified welcome message


2009-10-23 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 348265d6211814ba0b0a1f7667fc018cdabde30b

* added colored nodes corresponding to host status to status map


2009-10-23 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 2d33735ba13554561f3fcd61e5147c778dbe258b

* added favicon
* changed app name (page title)


2009-10-23 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: e093ec1bf737cc7204d086a28be2a82b4eda7590

* fixed nodes and edges of status map for internet explorer


2009-10-22 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: b83a8a0109c7b5c119583faabf7cfee866d8c2d3

* cleaned js of status map
* added test style for nodes in status map
* added js 
  cronkTrigger to generate cronks by using anchors or other tags
* added link to host 
  cronk to status map


2009-10-22 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: ac430309487055ff9c8356b1a1f9cd23aa5b3c08

* fixed status-summary charts for Internet Explorer


2009-10-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0d2d1358766819624ccf570d8840d258727f4d97

* Service summary view


2009-10-21 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 7241bcbe113b6dfac040cf221b042336d97b34c9

* changed positions of status-summary popups


2009-10-21 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: f2a52b2e81b9713353dc54b739fa906ae1ba1617

* disabled unselectable and static cronks
* sorted cronk list


2009-10-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cabde37227d584c55bb31013087868eeaca64b4f

* Hostgroupsummary template


2009-10-21 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 423bc86ddfbba16526be6decd14c55b49b577211

* added popups to status-summary charts


2009-10-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b36ec68316d84fba2319929c27b137d5278cc09d

* Hostgroup summary view
* Changes on the template worker
* Added the view to cronks


2009-10-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 6320242ef44721119ca0ebc7a9e260672a639435

* fixed left margin of west frame if log viewer is open


2009-10-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: e0fff159ad3a4471fe55530e8f84559da8af05e1

* typo in cronk list


2009-10-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: b3dcaf16faf6fee712485d01da9471627e512ff0

* changed icinga at footer to lower case


2009-10-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 690f14a0764c8e69e688874e0d8eb37a9765d09d

* prepared upper bar for link modifications


2009-10-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: d9286512d2389a00f08344ebb2d9aae08c76ec17

* fixed Javascript in status summary for IE
* fixed base of status map to work with IE


2009-10-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 40f8495abeac62254cd6bc6b3cb62d8d8e3809c4

* Added additional filter fields for the xml template
* Altered the xml templates for 
  hostgroup and servicegroup fields
* Searchtargets for host- and servicegroups


2009-10-20 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: ea6866239f41dd40a218834d0ef1b27bb1ae4493

* fixed Javascript for Internet Explorer


2009-10-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ec70d93682e894552fcfd5045330a5380235e64c

* CSS changes for the body
* Better site title
* Login links and user logged in 
  notice
* Typo for portal cronks


2009-10-19 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 38d266bb8205ee83c94edab432d7c023c231cad0

* fixed ajax login for IE


2009-10-19 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 3ea38ce99083a72e902d956f6f8c4ab61ae7137a

* modified some Javascript files to make code work in Internet Explorer


2009-10-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8c44c428c71a2b1d5a97a6850e2b85af7ef80aa0

* Added phing build tasks for db's and dependencies
* DB update path testing


2009-10-13 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4067cccef901ca613a26ba82cb9ddce127d62203

* Added notifications template


2009-10-08 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 3f362df57d2c5d9ab2f39038bd6cae4345d4e22c

* recoded status-summary charts to use html elements


2009-10-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ef752672fb9c941362bf431b91f7e5c81b95f630

* Finished AJAX login
* Application starts with login (all actions are secured)


2009-10-08 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 38ea5b24684740767f78313482285265e1646d84

* changed styles of status-summary rows


2009-10-08 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: f90a0ef5a2ecc0bb121638e7b24143db16b67f2b

* added cronk links to status summary rows


2009-10-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: db130508214e4aa83eee8cea5f86baf16391887a

* Added ajax login action
* Securing the web (all actions needs a login)


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: d2c399916aa808617f65c5e986bde28ce832be08

* status map: increased depth of clickable objects


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 06c29bdc4bf3303c14bb99ab657b567b2af93587

* fixed refresh handling of status map


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: ec274236e72e943fb1b4570bacdb48d807ec5876

* updated status map for use of dyna loader


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 90d032d19abdf45c0f94f946cb7659b6da9efa53

* updated status map for support of multiple views


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 21b9cb4cedfc47801f7e547f5b7fdbc0d2bd33de

* added display of host- and service-status strings to status map and info boxes


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: b09df1d8bd293f6ec49744cf089e97241c23fdf8

* added loading mask to status map
* added mask to cronk selector and activated it


2009-10-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 319987661981ae88689100b22688ec202137803c

* added validators to status map
* wrapped up status map in dynamic containers


2009-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 5f506382a2ea82f41eebcbcce26efaefc5dcf1ec

* Remove old plain icinga actions
* Prepared ajax login action


2009-10-06 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 6ebf2604286cae79677f5bcf426b1018cf8fc86c

* added additional host info to status map
* restructured status-map js


2009-10-06 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: fdc115cdec1d7c0e4af215741c7b4a7b2c5189ad

* integrated static status maps
* added styles for status maps
* updated english 
  language files


2009-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ef51e1bba21bf2912f8482cdeac9cb3cc49ce945

* Fixed dynamic web path things
* Make project htaccess ready (no vhost needed 
  anymore)


2009-10-06 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: cbe8180348457ac657f460d070fdba42b8e9a34d

* Added doctrine 1.1.4
* Removed unused js references from icinga.xml


2009-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 6b74ccb9ee39fb74353b90b2a5666174c027c9c9

* Removed obsolete js frameworks
* Removed useless menu entries
* Removed old config 
  from the xml settings


2009-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 0d8f65f8f09e9aca57005491e3ab9536f4366822

* Clean up the config xml's
* Rearranged AppKit bootstrap methods
* Removed some old 
  parts, never needed


2009-10-05 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 98a15a88381fb24e9dd5eba667d113a8662bb5f8

* Agavi 1.0.1
* Cache (file and added to somw loaders)
* Meta text files
* Version 
  handling
* removed some unused files


2009-10-05 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 3c9f3f3e0b39cfb33afe73a737c480f6e730ee01

* added jit library for status map
* added base for status-map cronk


2009-10-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 61493256328ec1ab01af0ed3ca071ef223733d75

* running the web within a subdir


2009-10-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b748152d611113ed285f8b97b45cff6c0002e41c

* prepared cache interface
* simple file cache


2009-10-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: f172b4de696525f895fc77f3f386f321340c267e

* added service information to data grid


2009-10-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: f990ff5e58cb2e09b66e76e4421a0ac1d58d32b4

* updated some strings


2009-10-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 6c466e98f1d9736038e93d77b959251798dc92fd

* added base for i18n
* modified host popup to display tables


2009-09-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 78723d46233055d45c3eea2410591b05f45d97ab

* extended simple-data provider


2009-09-30 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: c91f6c16df2625e823b245aa27a99833803e6312

* changed styles


2009-09-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2f85e90b6fc2225b2b746be1e8fe3a464ee3438a

* Prepared global cache interface
* ObjectSearch landing cronks
* Better base 
  exception class to use formatted strings


2009-09-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a3a7c3289a16d1f5cd69137d8bc062f0eee31cf5

* Merge commit 'origin'; branch 'mhein/default'


2009-09-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ed012acb163a5fdd8eb303dff3c64000bc79d6c9

* last commit is valid, some files forgotten


2009-09-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2cf84eb9bd94df1b740a9561c8dfbe68916dafac

* grid xml templates
* js util for structures
* intergrid links
* disable autoloading 
  of grid stores
* column renderer util for parsing column metaData
* generic column 
  render namespace


2009-09-23 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 97b98ee7cdef253a31d2a3b8e7c264a1f7702990

* XML templates
* grid sublinks
* rewritten cronk container
* rewritte the grid meta 
  creator
* namespaces and typo


2009-09-22 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 63e72aa7b01f4430ff6f65be913f37dfecfcad81

* new template: host history
* xml template renderers and click events


2009-09-21 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d6ad6eb896d48e248e69e423b507ac8830ae3c49

* JS dynaloader to load libs for specific components
* Fixed sql operater handling 
  for filters
* added a column renderer model to implement renderers or custom events 
  to grids through xml template


2009-09-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a589843bcb148b46d8826a1b764ba80afd21174b

* js object structures (scope handling rewritte)
* AppKit.Ext ist now event driven
* 
  Added appkit bootstrap event register through config
* Moved some static includes to 
  events
* fixed loose app state data (namespace)
* renaming and typo


2009-09-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 06c10d829150daedbc1465c98fcf69df3f1555f8

* fixes hybrid application state provier
* cookie filter deletes ext js cookies
* 
  created the real portal view (better name for the action)
* prepared a new login 
  action


2009-09-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 53ae1592b510f354fcfb850d45af2e2edfd9f836

* Quick fix before merging


2009-09-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 07853a82cb1438315077967b32bd1ab95df7f668

* JS scope layout
* MetaGridCreate fixes


2009-09-17 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 3ceb15a06bd81511c7f59214c2f5360e28e4d011

* simple data provider: added js object, added basic query definition, extended model


2009-09-16 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b7410f2361b70e6a93a54402835e9d6f16c3e0c3

* Filter objects
* Layout fixes
* Window handlers (singleton)
* Search operator combos


2009-09-16 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: af5085f5d436a27dd233d7e57002588a9e672da3

* added base for simple data provider


2009-09-15 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 895db5a0a353a431c10b5ae314c3fbe4dd5b7577

* Grid toolbar handlers
* Grid filter handlers
* Dynamic filter windows
* 
  TemplateWorker filtering
* Generic SQL constants interface to work with api


2009-09-15 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: ca2d72307dc43e4c84cb8408a6829f05558eb2d7

* renamed dummy var for status summary


2009-09-15 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: edc6c7b4f91213da378693b9a0b91f8965960d52

* extended status-summary cronks
* replaced middle-north content by status-summary 
  charts


2009-09-14 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a69022e14d3c66ec70073cd0e6d8b8c1e43b61ab

* Added basic filter panels
* some typo
* mysql rescue scheme


2009-09-14 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 941d0eaa9b3857156245e07481d68fa39661fcb8

* changed style of status summary


2009-09-14 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 36eeaded90ff609d45757c5aebacdd5ca3a57a64

* extended state summary
* embedded state summary in upper right corner of portal view


2009-09-14 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: e56444769016fa971072b6f05306b00209824132

* status summary: added base for charts


2009-09-11 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 2eb43c592b4fa29fe5924f2dbbaedb16371a7d9e

* - layout fixes - added ext option in xml grids - redesigned cronk directory layout 
  - single cronks.xml configuration file


2009-09-11 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 085777336688a021f989d0152d1967db5b317ca3

* restructured view and model for status-summary cronk


2009-09-10 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e43b300de974fae259fc57e9dcf43fa1c377c1b2

* - rewritten ajax grid handling - added fam fam icons views and icon classes


2009-09-10 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: d12dc0a2f91673738544930d7a79cf2781f496ca

* updated status summary view


2009-09-10 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 88bcec0a54e67169b5618669ac870d1b2ed4721e

* added model for status-summary cronk


2009-09-09 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dc8f62463e2d3367aded5192ee699e4be50c2492

* - Extjs persistence layer - layout fixes for child container and cronks - Extjs 
  rewritten SessionProvider - Long blobs for user preferences


2009-09-09 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 5b11f77f96adf051f53990f9887a242df922789f

* added base for state-summary view


2009-09-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 40932967ed85b06d87ce393c736e381ed52abc45

* - added persistence provider stub - added limit statement to the xml templates - 
  limit log template to 25 items (performance)


2009-09-08 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 4791377a1a4c208f02041258ed3041a8a4f39611

* moved extjs file for fancy-text field
* updated config


2009-09-08 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 9e5a5c785b9a15866903da85ca82fdaa1cd72c45

* added reset to fancy search


2009-09-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fef3bb50cdb7c9c93600908ea13b04534ca9ebcb

* - commit local changes before merge - moved ext js appkit helper to new dir - added 
  http state provider for persistent component handling


2009-09-08 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: bbd21c116e6f7bcf77e5a120914f482b7cba0c54

* - Fixed layout issues (using fit within borders) - resize portlets listener - 
  cleaned testing ext2 framework


2009-09-07 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ef2c28bb24e80163e566d7f275974d0ded0e5134

* - icinga log xml template - default params for the cronk loader - dynamic ajax grid 
  height - south component for logentries


2009-09-07 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: ca1dd99ea4386d74121ce484ac39aa9ad6adf50e

* added fancy search (no reset, yet)


2009-09-04 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 21c2a05cd5e92396cbbdda48d0ced510a3f99beb

* Implemented dynamic object search


2009-09-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: e19eccd2b1221dbbd7aabfd4c1586cc9969393ac

* different store for the object type for global search


2009-09-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3787f39a053b09936e7ae13b8163d77b1299979b

* Added global search


2009-09-03 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 3228166455fd6bfb5c487e7715539ea69012cc1e

* removed unused css classes from west- and center-frame


2009-09-03 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 56f9f52e2eaba0b91120b2210680e648bcf8ad29

* removed margins and borders around north frame


2009-09-03 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 2b919d2ec1ae8ef1bde9c87013557d0af0c1a986

* removed extjs2


2009-09-03 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 7a7e1151e1184e29e0e7be3121fa98eddbbfabb2

* removed top-view bar


2009-09-02 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 24637f580bdb5ccb994f953513cd35f0d1ab3f81

* Added js message stack (growl like) and search cronk


2009-09-02 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 87d6dc7d31ddfd12e4aa56cf7aac93678624f54d

* prepared layout and style for top-view area


2009-09-02 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 27265a9a94f4884d64659d17262b8b865c8ef008

* added space for links and rss icon to top bar


2009-09-02 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 38bc939c3c113de8112aa447ea71c883a9104c68

* changed colours of main content


2009-09-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 0679f2e0e351f95884b19a6ca2e1689f233bd6e0

* fixed style of main view


2009-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 64912ab5e4f23458c9be157a88f66658ba8a6193

* Default accordeon panel


2009-09-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: d305bb0b356ee4dfb53c1b665f1956c2784cfdcf

* updated styles and logos


2009-09-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 86c4edd10a6586bc4dae77804e61815c1d886a1b

* Ext startup and container refreshes.


2009-09-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 27f1659a169bda31cb08dd3682918507ef9bdaf0

* added background images to left navigation and main content


2009-09-01 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 584cb9f52df53263d136aa328ccc5139acf50cd2

* fixed cronk js


2009-08-31 Christian Doebler <christian(DOT)doebler(AT)netways(DOT)de>
           Commit: 1d270f8b11ffdcee57f5f3560627f6160c830122

* added styles and images for extjs


2009-08-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 33a162660ed6973fecb810044fe6cdf7d2400d89

* Fix the welcome screen!


2009-08-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a7558533fcae68c3fa72e038e7f134451d42c69a

* Layoutfixes


2009-08-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 36ea967b6d7236a4bdf2a1c77092282470de480e

* Friday CI!


2009-08-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dbe60f0f82280765831b8a1200c006a8198d73e6

* SQL


2009-08-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4570e714b2ff1107819fab04673145aa593c5e39

* quickfix sql data


2009-08-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: a5eb9d2c01894e13457aa884fbcd65133c843eb9

* temp commit


2009-08-27 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 150fba7bd36a175358961946ad0e1c7f047a87fa

* First running and preconfigured portal versionImplemented host view (xml) added 
  some  helper classes



2009-08-26 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 8c72766c5b14623a961ff3f0f5b4f07387901f84

* added helper cronk, structs to preconfigure cronks and the real portal with portlets


2009-08-25 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4789159a32b37c950ace6f3a7c6e8b1eedb92e48

* Changed application main layout, added cronk listing


2009-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 009f5cdef411508e84e9494d43a64620b9402f0c

* Tested ext versions and fixed safari issues


2009-08-24 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 730e7019a6fe8d48418f35c785f21568f3ab1acd

* JS bulk loader (agavi made for safari)


2009-08-20 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 9fccee91d965820e4b1e4004508621d363f07bcb

* Implemented portal structure and testcase


2009-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 81efa8bf3af8fc0f96abb2890a73f5a06d3c3ae5

* Added a ext component autoload scenario into some tabs!


2009-08-19 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 172eb58053ecdff747704f3199a8747bddf690e9

* Changed path structure, introduce the name cronk (widget), added cronkhandler and 
  testscript


2009-08-18 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 145e92ff098193800147481b92b8bfaabd19be9c

* XML configure for grid view, grouping, sorting


2009-08-17 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: b36184ca808ef98767dc32b7276b49e0f0ac18d8

* Ajax grid changes layout on meta information


2009-07-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: dae18f0572610c2df542d2cf3c31e38323953614

* Added files


2009-07-31 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: c5a3ae15f398859b710090c4b2f2dcc594de77c6

* AjaxGridLayout


2009-07-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3a7f63a06ffcbd7ec0463185382e147b743d2523

* Added AjaxGrid and basic template parsing features


2009-07-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 233a16100175c063c6bf764f7d45323f31688bb3

* Added missing files


2009-07-29 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: d43571a1187be85d3c7cae576359556fb089def7

* Template driven views, first steps


2009-07-03 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 873019d39fb4aaa35b31c56507b6929c43aae97f

* Implemented a better host list and detail view


2009-07-02 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 89980c5ee512eed5745ffc8740e8627b34f809ed

* Added agavi translation manager, date formattings, date tools and generic tables 
  (tested on host detail)


2009-07-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 338ae1fa6582eee9843750b9474cc61d2794f409

* Adding state base classes


2009-07-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: ad4faa67e790ff988acc78cf4410d391afb9895b

* Added changes from mhein to master, added missing factory


2009-07-01 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 21e9b01ce8db1422c47fec15aa8bd47c7c2c2575

* Added icinga-api and icinga-api-config and displayed some information


2009-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 1140640d168cf9c8f906dc5fad76d87901b3eff3

* added second stage changes (register view links)


2009-06-30 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: fcfdc60b30247b6e92554053e3fc2305a926eafa

* Added api to register custom links into views (also corresponding objects)


2009-04-28 Michael Luebben <michael(AT)luebben-home(DOT)de>
           Commit: 31d2a612b7748dada89c09a0bec9c30ea0fbb843

* Add new lib pchart


2009-04-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 369a0b12d7736f5f7358fc190b70b4c7e4f9aef7

* Merged back into the master


2009-04-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 4bd58db2352e2faba8313377cef16be2913e8755

* Fixed navitem overwriting


2009-04-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 22c086e8f4b132e08a111577e19e84ebf998563e

* Added insertBefore for the NavContainer


2009-04-28 Marius Hein <marius(DOT)hein(AT)netways(DOT)de>
           Commit: 3f0950cb5fc792797c00986027c25922394bc674

* Added a web dummy module!


2009-04-24 MiCkEy2002 <mickey2002(AT)devel(DOT)(none)>
           Commit: aa90947f1c088c1713ea158de0d32fb17a0fb1dc

* Correct copyrights


2009-04-24 MiCkEy2002 <mickey2002(AT)devel(DOT)(none)>
           Commit: 4d04882641af47341f580350554e551d616fb0b7

* Delete backup files from Quanta Plus


2009-04-24 MiCkEy2002 <mickey2002(AT)devel(DOT)(none)>
           Commit: bed557dc6c1d9fb0ad531094005ac961ce5864b9

* Add new meni itims


2009-04-23 Hendrik Baecker <andurin(AT)process-zero(DOT)de>
           Commit: d722c5b3e4dfba20600f8f7c796aa114efe71bf5

* Re-add app/cache/config/


2009-04-23 Hendrik Baecker <andurin(AT)process-zero(DOT)de>
           Commit: f56e36e98e5dd739b6e7038f4dcd1e022c3b5a72

* Added .gitignore


2009-04-23 Hendrik Baecker <andurin(AT)process-zero(DOT)de>
           Commit: 265cf9ed407c106975e47853689c835b6eb6a3b6

* Delete cachefiles from git


2009-04-23 MiCkEy2002 <mickey2002(AT)devel(DOT)(none)>
           Commit: 7208273c9881acc2637129fed54a2c8857ab4523

* Rename Netways to Icinga


2009-04-23 Hendrik Baecker <andurin(AT)process-zero(DOT)de>
           Commit: 5c4f10a97d02104e1f79d8a8d4e29b7f258c1247

* Deleted previously renamed files


2009-04-23 MiCkEy2002 <mickey2002(AT)devel(DOT)(none)>
           Commit: 3a56d775b72adbfc48d2951c3043d1b50dbf0cf4

* Rename files and code from NETWAYS to ICINGA


2009-04-23 MiCkEy2002 <mickey2002(AT)devel(DOT)(none)>
           Commit: 2108235a48f7d192d8c322a964606f45ecfed1ea

* Rename files and code from NETWAYS to ICINGA


