<?xml version="1.0" encoding="UTF-8"?>
<xsl:transform version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:php="http://php.net/xsl"
xsl:extension-element-prefixes="php">

<xsl:output method="xml" />

<xsl:param name="description"/>

<xsl:template match="/flexchart">
	<flexchart>
		<panels>
			<panel>
				
				<parameters>
					<parameter name="title">
						<xsl:value-of select="$description" />
					</parameter>
					<parameter name="top">0</parameter>
					<parameter name="left">0</parameter>
					<parameter name="width">990</parameter>
					<parameter name="height">400</parameter>
					<parameter name="layout">vertical</parameter>
					<parameter name="refresh">600</parameter>
				</parameters>
				
				<charts>
					
					<chart>
					
						<parameters>
							<parameter name="type">area</parameter>
							<parameter name="subtype">overlaid</parameter>
							<parameter name="showdatatips">1</parameter>
							<parameter name="width">100%</parameter>
							<parameter name="height">100%</parameter>
							<parameter name="axehorizontaltitle">Time</parameter>
							<parameter name="axehorizontalfield"></parameter>
							<parameter name="axehorizontaltype">time</parameter>
							<parameter name="axeverticaltitle"></parameter>
							<parameter name="axeverticalfield"></parameter>
							<parameter name="axeverticaltype"></parameter>
							<parameter name="legendlabelplacement">right</parameter>
							<parameter name="legenddirection">horizontal</parameter>
							<parameter name="aggretype">none</parameter>
							<parameter name="start_diff"></parameter>
							<parameter name="end_diff"></parameter>
						</parameters>
					
						<seriesc>
						
						<xsl:for-each select="//rows/row[5]/pair">
							
							<xsl:variable name="series_title">
							
								<xsl:value-of select="./@group" />
								<xsl:text> </xsl:text>
								<xsl:value-of select="./@item" />
								<xsl:text>(</xsl:text>
								<xsl:value-of select="./@raw_key" />
									
								<xsl:text>[</xsl:text>
								<xsl:choose>
									<xsl:when test="string-length(./@unit) &gt; 0">
										<xsl:value-of select="./@unit" />
									</xsl:when>
									<xsl:otherwise>
										<xsl:text>n</xsl:text>
									</xsl:otherwise>
								</xsl:choose>
								<xsl:text>]</xsl:text>
									
								<xsl:text>)</xsl:text>
							
							</xsl:variable>
							
							<series>
					
								<parameters>
									<parameter name="type">line</parameter>
									<parameter name="form">curve</parameter>
									<parameter name="name">
										<xsl:value-of select="$series_title" />
									</parameter>
									
									<parameter name="color">
										<xsl:value-of select="php:functionString('AppKitColorUtil::generateRandomHexColor')" />
									</parameter>
									<parameter name="weight">2</parameter>
									<parameter name="alpha">0.5</parameter>
									<parameter name="xfield">t</parameter>
									<parameter name="yfield">
										<xsl:value-of select="./@key" />
									</parameter>
									<parameter name="axeverticaltitle">
										<xsl:value-of select="$series_title" />
									</parameter>
									<parameter name="axeverticalweight">5</parameter>
									<parameter name="axeverticalalpha">0.5</parameter>
									<parameter name="axeverticalcaps">round</parameter>
									<parameter name="depth"></parameter>
								</parameters>

							</series>
							
						</xsl:for-each>
						
						</seriesc>
					
					</chart>
					
				</charts>
				
			</panel>
		</panels>
	</flexchart>
</xsl:template>

</xsl:transform>