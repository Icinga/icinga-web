<?php
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

$template = $rd->getParameter('template');
$render = $rd->getParameter('render', 'MAIN');
$cmpid = $rd->getParameter('cmpid');
?>
<script type="text/javascript">
    Cronk.util.initEnvironment(<?php CronksRequestUtil::echoJsonString($rd); ?>, function() {
        var CE = this;

        var p = (function() {
            var pub = {};
            var panel = null;
            var pc = null;
            var template_name = '<?php echo $template; ?>';
            var url = "<?php echo $ro->gen('modules.cronks.staticContent.content', array('template' => $template, 'render' => $render, 'cmpid' => $cmpid)); ?>";

            url = url.replace(/&amp;/g, '&');

            Ext.apply(pub, {

                init : function() {
                    if (!panel) {

                        panel = new Ext.Panel({

                            border: false,
                            autoScroll: true,
                            id: CE.cmpid,

                            // Options for the updater
                            autoLoad: {
                                url: url,
                                scripts: true,
                                method: 'get',

                                text: String.format(_('Loading TO "{0}" ...'), template_name),

                                /*
                                 * @todo: make timeout configurable
                                 */
                                timeout: 600 // Very long for too much data
                            },

                            // Building the toolbar
                            tbar: {
                                items: [{
                                        text: _('Refresh'),
                                        iconCls: 'icinga-icon-arrow-refresh',
                                        tooltip: _('Refresh the data in the grid'),
                                        handler: function(oBtn, e) { panel.getUpdater().refresh(); }

                                    }, {
                                        text: _('Settings'),
                                        iconCls: 'icinga-icon-cog',
                                        toolTip: _('Tactical overview settings'),
                                        menu: {
                                            items: [{
                                                    text: _('Auto refresh'),
                                                    checked: true,
                                                    checkHandler: function(checkItem, checked) {
                                                        if (checked == true) {
                                                            panel.trefresh = AppKit.getTr().start({
                                                                run: function() {
                                                                    try {
                                                                        this.getUpdater().refresh();
                                                                    } catch(e) {}
                                                                },
                                                                interval: 120000,
                                                                scope: panel
                                                            });
                                                        }
                                                        else {
                                                            AppKit.getTr().stop(panel.trefresh);
                                                            delete panel.trefresh;
                                                        }
                                                    }

                                                }]
                                        }
                                    }]
                            }
                        });

                        CE.add(panel);
                        CE.doLayout();
                        //refresh
                        panel.trefresh = AppKit.getTr().start({
                            run: function() {
                                try {
                                    this.getUpdater().refresh();
                                } catch(e) {}
                            },
                            interval: 120000,
                            scope: panel
                        });
                        return true;
                    }

                    return false;
                }

            });

            return pub;
        })();

        p.init();
    });
</script>
