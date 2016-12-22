# Grids and customvariables

## Preface

With the release of 1.13 icinga-web can now add custom variables to grid views.
The customvariables appear as single database fields and can be filtered
therefore.

This feature is disabled by default. Variables are a highly custom tailoring
for every installation. If you want to use this you can enable the example
definitions and customize this to your variables.

## Approach to configure

### Activate fields in dataview

Every grid ist based on a dataview. The dataview handles the database query
and controls filter and paginating. In order to have our customvariables 
available here you have to add a configuration snippet.

1. Go to your icinga-web base folder and change the directory dataview
   configuration directory.

```
$ cd app/modules/Api/config/views/
```

2. Open the view you want to add your customvariables and create a new definition

```
# Search for view <dql name="TARGET_HOST" >
# Create a new view behind based on TARGET_HOST
$ vi host.xml
<target base="TARGET_HOST" name="TARGET_HOST_CV">
    <customvariables>
        <parameter name="list">
            CUSTOMER, CUSTOMER_NR, DESCRIPTION,
            IP_ADDRESS, OS, REALM, VLAN_ID
        </parameter>
        <parameter name="leftJoin">h.customvariables</parameter>
    </customvariables>
</target>
```

The snippet above create a new dataview with name TARGET_HOST and adds the variables in the list to the query. The variables are joined from the host customvariabled (h.customvariables).

### Create or change the grid template to use the view

1. Create a new grid template and add each customvariable to the list of fields

```
# Change directory to icinga-web root and go to
$ cd app/modules/Cronks/data/xml/grid/
# Create a new grid template from a an existing one
$ cp icinga-host-template.xml icinga-host-cv-template.xml
# Alter the template, change datasource and add fields
$ vi icinga-host-cv-template.xml
# Search for datasource and change to our view created in step 2
<datasource>
    <parameter name="target">TARGET_HOST_CV</parameter>
    <parameter name="countmode">field</parameter>
    <parameter name="additional_fields">
        <parameter name="hostgroup_object_id">HOSTGROUP_OBJECT_ID</parameter>
    </parameter>
</datasource>
# Add all fields in the following matter
<field name="host_customer_value">
    <datasource>
        <parameter name="field">customer_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">Customer</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">Customer</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=CUSTOMER</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>

<field name="host_customer_nr_value">
    <datasource>
        <parameter name="field">customer_nr_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">Customer Nr</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">Customer Nr.</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=CUSTOMER_NR</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>

<field name="host_description_value">
    <datasource>
        <parameter name="field">description_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">Description</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">Description</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=DESCRIPTION</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>

<field name="host_ip_address_value">
    <datasource>
        <parameter name="field">ip_address_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">IP</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">IP (CF)</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=IP_ADDRESS</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>

<field name="host_os_value">
    <datasource>
        <parameter name="field">os_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">OS</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">OS</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=OS</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>

<field name="host_realm_value">
    <datasource>
        <parameter name="field">realm_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">Realm</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">Realm</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=REALM</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>

<field name="host_vlan_id_value">
    <datasource>
        <parameter name="field">vlan_id_value</parameter>
    </datasource>
    <display>
        <parameter name="visible">true</parameter>
        <parameter name="label">VLAN</parameter>

    </display>
    <filter>
        <parameter name="enabled">true</parameter>
        <parameter name="type">extjs</parameter>
        <parameter name="subtype">appkit.ext.filter.api</parameter>
        <parameter name="label">VLAN ID</parameter>
        <parameter name="operator_type">text</parameter>
        <parameter name="api_target">customvariable</parameter>
        <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
        <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
        <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
        <parameter name="api_filter">CUSTOMVARIABLE_NAME=VLAN_ID</parameter>
    </filter>
    <order>
        <parameter name="enabled">true</parameter>
        <parameter name="default">false</parameter>
    </order>
</field>
```

Please be careful when configure the following settings:

```
# - Even if the field is in uppercase letters, it is converted to lowercase in templates
# - To keep the fields unique a prefix '_value' is added
# For example, customvariable name is 'CUSTOMER', the result column is customer_value
<datasource>
    <parameter name="field">customer_value</parameter>
</datasource>
```

```
# - Labels must be unique in the whole grid template (parameter label)
# - The api filter is a real database filter. If the customvariable
#   is in capital letters you have to filter for that (paramter api_filter)
<filter>
    <parameter name="enabled">true</parameter>
    <parameter name="type">extjs</parameter>
    <parameter name="subtype">appkit.ext.filter.api</parameter>
    <parameter name="label">Customer</parameter>
    <parameter name="operator_type">text</parameter>
    <parameter name="api_target">customvariable</parameter>
    <parameter name="api_keyfield">CUSTOMVARIABLE_VALUE</parameter>
    <parameter name="api_valuefield">CUSTOMVARIABLE_VALUE</parameter>
    <parameter name="api_additional">CUSTOMVARIABLE_NAME</parameter>
    <parameter name="api_exttpl"><![CDATA[{CUSTOMVARIABLE_VALUE} ]]></parameter>
    <parameter name="api_id">CUSTOMVARIABLE_VALUE</parameter>
    <parameter name="api_filter">CUSTOMVARIABLE_NAME=CUSTOMER</parameter>
</filter>
```

### Create a new cronk for the grid template

Change directory to icinga-web base directory go to your configuration directory

```
$ cd etc/conf.d/
# or if distribution package /etc/icinga-web/conf.d
# Open cronks xml and add a new cronk using your grid template
$ vi cronks.xml
<cronk name="gridHostViewCV">
    <ae:parameter name="module">Cronks</ae:parameter>
    <ae:parameter name="action">System.ViewProc</ae:parameter>
    <ae:parameter name="hide">false</ae:parameter>
    <ae:parameter name="description">Viewing host status and its custom variables</ae:parameter>
    <ae:parameter name="name">HostStatus CV</ae:parameter>
    <ae:parameter name="categories">status</ae:parameter>
    <ae:parameter name="image">cronks.Computer</ae:parameter>
    <ae:parameter name="position">105</ae:parameter>
    <ae:parameter name="ae:parameter">
        <ae:parameter name="template">icinga-host-cv-template</ae:parameter>
    </ae:parameter>
</cronk>
```

### Final procedures

Clear the cache and reload the whole interface. You will see the new cronk available

## Example file

A complete example is implemented, you can examine the following files and activate snippets

 - app/modules/Api/config/views/host.xml
 - app/modules/Cronks/data/xml/grid/icinga-host-cv-template.xml
 - app/modules/Cronks/config/cronks_grid.xml
 

 
