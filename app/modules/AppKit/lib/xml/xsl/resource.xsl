<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:envelope_0_11="http://agavi.org/agavi/1.0/config"
    xmlns:resource_1_0="http://icinga.org/icinga/config/parts/icinga/1.0"
>

    <xsl:output method="xml" version="1.0" encoding="utf-8" indent="yes" />

    <xsl:include href="../../../../../../lib/agavi/src/config/xsl/_common.xsl" />

    <xsl:variable name="settings_1_0" select="'http://icinga.org/icinga/config/parts/icinga/1.0'" />

    <!-- pre-1.0 backwards compatibility for 1.0 -->
    <!-- non-"envelope" elements are copied to the 1.0 settings namespace -->
    <xsl:template match="envelope_0_11:*">
        <xsl:element name="{local-name()}" namespace="{$resource_1_0}">
            <xsl:copy-of select="@*" />
            <xsl:apply-templates />
        </xsl:element>
    </xsl:template>

</xsl:stylesheet>
