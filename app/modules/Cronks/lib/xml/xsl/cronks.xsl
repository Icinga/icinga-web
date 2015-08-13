<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
	version="1.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:envelope_0_11="http://agavi.org/agavi/1.0/config"
	xmlns:cronks_1_0="http://icinga.org/cronks/config/parts/cronks/1.0"
>
	<xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" />

	<xsl:include href="../../../../../../lib/agavi/src/config/xsl/_common.xsl" />

	<xsl:variable name="cronks_1_0" select="'http://icinga.org/cronks/config/parts/cronks/1.0'" />

	<!-- pre-1.0 backwards compatibility for 1.0 -->
	<!-- non-"envelope" elements are copied to the 1.0 routing namespace -->
	<xsl:template match="envelope_0_11:*">
		<xsl:element name="{local-name()}" namespace="{$cronks_1_0}">
			<xsl:copy-of select="@*" />
			<xsl:apply-templates />
		</xsl:element>
	</xsl:template>

	<xsl:template match="envelope_0_11:route[@callback]">
		<xsl:element name="{local-name()}" namespace="{$cronks_1_0}">
			<xsl:copy-of select="@*[local-name() != 'callback']" />
			<cronks_1_0:callbacks>
				<cronks_1_0:callback class="{@callback}" />
			</cronks_1_0:callbacks>
			<xsl:apply-templates />
		</xsl:element>
	</xsl:template>

</xsl:stylesheet>
