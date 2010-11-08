<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="xml" omit-xml-declaration="yes"/>
<xsl:template match="/">
<xsl:for-each select="results/result">
<xsl:for-each select="column">
<xsl:value-of select="@name"/>: <xsl:value-of select="."/>
<xsl:text disable-output-escaping="yes">
&lt;br/&gt;
</xsl:text>
</xsl:for-each>
<xsl:if test="position() != last()">
<xsl:text>

--

</xsl:text>
</xsl:if>
</xsl:for-each>
</xsl:template>
</xsl:stylesheet>

