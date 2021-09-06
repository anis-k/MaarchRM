<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0"
	xmlns="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:m="maarch.org:laabs:medona"
    xmlns:rm="maarch.org:laabs:recordsManagement"
	exclude-result-prefixes="xsl xsd m">
	
	<xsl:template match="m:archivePackage">
        <xsl:apply-templates select="rm:archive" />
    </xsl:template>
    
    <xsl:template match="rm:archive">
        <xsl:apply-templates select="rm:descriptionObject" />
    </xsl:template>
    
    <xsl:template match="rm:descriptionObject">
        <xsl:apply-templates select="./*" />
    </xsl:template>
    
    
</xsl:stylesheet>