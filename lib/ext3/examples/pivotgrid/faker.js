/*
This file is part of Ext JS 3.4

Copyright (c) 2011-2013 Sencha Inc

Contact:  http://www.sencha.com/contact

GNU General Public License Usage
This file may be used under the terms of the GNU General Public License version 3.0 as
published by the Free Software Foundation and appearing in the file LICENSE included in the
packaging of this file.

Please review the following information to ensure the GNU General Public License version 3.0
requirements will be met: http://www.gnu.org/copyleft/gpl.html.

If you are unsure which license is appropriate for your use, please contact the sales department
at http://www.sencha.com/contact.

Build date: 2013-04-03 15:07:25
*/
//script used to generate data.json
(function() {
    function buildData(count) {
        count = count || 1000;
        
        var products = ['Ladder', 'Spanner', 'Chair', 'Hammer'],
            states   = ['CA', 'NY', 'UK', 'AZ', 'TX'],
            cities   = ['San Francisco', 'Palo Alto', 'London', 'Austin'],
            people   = ['Tommy Maintz', 'Abe Elias', 'Ed Spencer', 'Jamie Avins'],
            records  = [],
            i;
        
        for (i = 0; i < count; i++) {
            records.push({
                id      : i + 1,
                product : products[Math.floor(Math.random() * products.length)],
                city    : cities[Math.floor(Math.random() * cities.length)],
                state   : states[Math.floor(Math.random() * states.length)],
                quantity: Math.floor(Math.random() * 10000),
                value   : Math.floor(Math.random() * 50),
                month   : Math.ceil(Math.random() * 12),
                quarter : Math.ceil(Math.random() * 4),
                year    : 2010 - Math.floor(Math.random() * 2),
                person  : people[Math.floor(Math.random() * people.length)]
            });
        }
        
        return records;
    };
    
    function buildPeople(count) {
        count = count || 800;
        
        var colors  = ['Brown', 'Blue', 'Green'],
            decades = ['1960s', '1970s', '1980s', '1990s'],
            hands   = ['Left', 'Right'],
            genders = ['Male', 'Female'],
            records = [],
            i;
        
        for (i = 0; i < count; i++) {
            var iq = Math.round(Math.random() * 100);
            
            if (iq < 25 && Math.random() > 0.5) {
                iq += Math.random() * 30;
            } else if (iq > 75 && Math.random() < 0.9) {
                iq -= Math.random() * 20;
            }
            
            records.push({
                eyeColor   : colors[Math.floor(Math.random() * colors.length)],
                gender     : genders[Math.floor(Math.random() * genders.length)],
                handedness : hands[Math.floor(Math.random() * hands.length)],
                birthDecade: decades[Math.floor(Math.random() * decades.length)],
                height     : 5 + parseFloat((Math.random() * 2).toFixed(1)),
                iq         : 50 + Math.round(iq)
            });
        }
        
        return Ext.encode({rows: records});
    };
});