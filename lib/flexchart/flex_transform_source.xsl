<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output method="xml" />

<xsl:param name="key_seperator"/>
<xsl:param name="panel_width"/>
<xsl:param name="panel_height"/>
<xsl:param name="panel_description"/>

<xsl:template match="/">
	<ngData>
		<xsl:apply-templates/>
	</ngData>
</xsl:template>

<xsl:template match="parameters">
	<!-- do nothing with it -->
</xsl:template>

<xsl:template match="panels">
		<xsl:apply-templates/>
</xsl:template>

<xsl:template match="panel">
	<panel>
		<!-- <title><xsl:value-of select="*/parameter[@name='title']" /></title> -->
		
		<xsl:element name="title">
		<xsl:choose>
			<xsl:when test="string-length($panel_title) &gt; 0">
				<xsl:value-of select="$panel_title" />
			</xsl:when>
			<xsl:when test="string-length(*/parameter[@name='title']) &gt; 0">
				<xsl:value-of select="*/parameter[@name='title']" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>untitled</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		</xsl:element>
		
		<!-- <width><xsl:value-of select="*/parameter[@name='width']" /></width> -->
		
		<xsl:element name="width">
		<xsl:choose>
			<xsl:when test="string-length($panel_width) &gt; 0">
				<xsl:value-of select="$panel_width" />
			</xsl:when>
			<xsl:when test="string-length(*/parameter[@name='width']) &gt; 0">
				<xsl:value-of select="*/parameter[@name='width']" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>990</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		</xsl:element>
		
		<!-- <height><xsl:value-of select="*/parameter[@name='height']" /></height> -->
		
		<xsl:element name="height">
		<xsl:choose>
			<xsl:when test="string-length($panel_height) &gt; 0">
				<xsl:value-of select="$panel_height" />
			</xsl:when>
			<xsl:when test="string-length(*/parameter[@name='height']) &gt; 0">
				<xsl:value-of select="*/parameter[@name='height']" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>400</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		</xsl:element>
		
		<layout><xsl:value-of select="*/parameter[@name='layout']" /></layout>
	</panel>
	
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="charts">
	<charts>
		<xsl:apply-templates/>
	</charts>
</xsl:template>

<xsl:template match="chart">
	<xsl:variable name="ename"><xsl:value-of select="position()" /></xsl:variable>
	<xsl:element name="c{$ename}">
		
		<type><xsl:value-of select="*/parameter[@name='type']" /></type>
		<subType><xsl:value-of select="*/parameter[@name='subtype']" /></subType>
		<showDataTips><xsl:value-of select="*/parameter[@name='showdatatips']" /></showDataTips>
		
		<width><xsl:value-of select="*/parameter[@name='width']" /></width>
		
		
		
		<height><xsl:value-of select="*/parameter[@name='height']" /></height>
		
		<axes>
			<horizontal>
				<type><xsl:value-of select="*/parameter[@name='axehorizontaltype']" /></type>
				<title><xsl:value-of select="*/parameter[@name='axehorizontaltitle']" /></title>
				<field><xsl:value-of select="*/parameter[@name='axehorizontalfield']" /></field>
			</horizontal>
			<vertical>
				<type><xsl:value-of select="*/parameter[@name='axeverticaltype']" /></type>
				<title><xsl:value-of select="*/parameter[@name='axeverticaltitle']" /></title>
				<field><xsl:value-of select="*/parameter[@name='axeverticalfield']" /></field>
			</vertical>
		</axes>
		
		<legend>
			<labelPlacement><xsl:value-of select="*/parameter[@name='legendlabelplacement']" /></labelPlacement>
			<direction><xsl:value-of select="*/parameter[@name='legenddirection']" /></direction>
		</legend>
		
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

<xsl:template match="seriesc">
	<series>
		<xsl:apply-templates/>
	</series>
</xsl:template>

<xsl:template match="series">
	<xsl:variable name="ename"><xsl:value-of select="position()" /></xsl:variable>
	<xsl:element name="s{$ename}">
		<name><xsl:value-of select="*/parameter[@name='name']" /></name>
		<type><xsl:value-of select="*/parameter[@name='type']" /></type>
		<form><xsl:value-of select="*/parameter[@name='form']" /></form>
		<xField><xsl:value-of select="*/parameter[@name='xfield']" /></xField>
		
		<!-- <yField><xsl:value-of select="*/parameter[@name='yfield']" /></yField> -->
		<xsl:element name="yField">
			<xsl:choose>
				<xsl:when test="string-length(*/parameter[@name='ds_host']) &gt; 0 and string-length(*/parameter[@name='ds_service']) &gt; 0  and string-length(*/parameter[@name='ds_key']) &gt; 0">
					<xsl:value-of select="*/parameter[@name='ds_host']" />
					<xsl:value-of select="$key_seperator" />
					<xsl:value-of select="*/parameter[@name='ds_service']" />
					<xsl:value-of select="$key_seperator" />
					<xsl:value-of select="*/parameter[@name='ds_key']" />
				</xsl:when>
				<xsl:when test="string-length(*/parameter[@name='yfield']) &gt; 0">
					<xsl:value-of select="*/parameter[@name='yfield']" />
				</xsl:when>
				<xsl:otherwise>
					<xsl:text>!!!INVALID_KEY</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:element>
		
		<color><xsl:value-of select="*/parameter[@name='color']" /></color>
		<alpha><xsl:value-of select="*/parameter[@name='alpha']" /></alpha>
		<weight><xsl:value-of select="*/parameter[@name='weight']" /></weight>
		
		<axes>
			<vertical>
				<title><xsl:value-of select="*/parameter[@name='axeverticaltitle']" /></title>
				<caps><xsl:value-of select="*/parameter[@name='axeverticalcaps']" /></caps>
				<alpha><xsl:value-of select="*/parameter[@name='axeverticalalpha']" /></alpha>
				<weight><xsl:value-of select="*/parameter[@name='axeverticalweight']" /></weight>
			</vertical>
		</axes>
		
	</xsl:element>
</xsl:template>

<xsl:template match="rows">
	<xsl:element name="data">
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

<xsl:template match="row">
	<xsl:element name="row">
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

<xsl:template match="t">
	<xsl:element name="t">
		<xsl:value-of select="." />
	</xsl:element>
</xsl:template>

<xsl:template match="pair">
	<xsl:element name="pair">
		<xsl:attribute name="key"><xsl:value-of select="@key" /></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@value" /></xsl:attribute>
		<xsl:attribute name="unit"><xsl:value-of select="@unit" /></xsl:attribute>
		<xsl:attribute name="group"><xsl:value-of select="@group" /></xsl:attribute>
		<xsl:attribute name="item"><xsl:value-of select="@item" /></xsl:attribute>
		<xsl:attribute name="raw_key"><xsl:value-of select="@raw_key" /></xsl:attribute>
	</xsl:element>
</xsl:template>

<xsl:template match="comment()">
	<xsl:comment>
		<xsl:text>ORG COMMENT: </xsl:text>
		<xsl:value-of select="." />
	</xsl:comment>
</xsl:template>

</xsl:transform>