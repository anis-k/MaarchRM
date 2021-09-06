<?xml version="1.0" encoding="UTF-8" ?>
<!--
    MEDONA v1.0 XSLT display HTML
-->

<xsl:stylesheet version="1.0"
    xmlns:medona="org:afnor:medona:1.0"
    xmlns="http://www.w3.org/1999/xhtml"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:laabs="maarch.org:laabs:medona"
    xmlns:rm="maarch.org:laabs:recordsManagement"
    xmlns:org="maarch.org:laabs:organization"
    exclude-result-prefixes="medona xsl xsd">

    <xsl:output method="html" indent="yes" media-type="text/html" encoding="UTF-8"/>

    <xsl:template match="/*">

        <!--Sender/Recipient -->
        <div class="row">
            <xsl:apply-templates select="medona:ArchivalAgency" />
            <xsl:apply-templates select="medona:TransferringAgency" />
            <xsl:apply-templates select="medona:Requester" />
            <xsl:apply-templates select="medona:Originator" />
            <xsl:apply-templates select="medona:ControlAuthority" />
            <xsl:apply-templates select="medona:OriginatingAgency" />
        </div>

        <!--Message info -->
        <div class="row" style="padding:20px">
            <h4><strong><span>Date</span> : </strong><xsl:value-of select="medona:Date"/></h4>
            <h4><strong><span>Reference</span> : </strong><xsl:value-of select="medona:MessageIdentifier"/></h4>

            <xsl:if test="medona:DataObjectPackage/medona:BinaryDataObject">
                <div><strong><span>Data object count</span> : </strong><xsl:value-of select="count(medona:DataObjectPackage/medona:BinaryDataObject)"/></div>
            </xsl:if>

            <xsl:if test="medona:DataObjectPackage/medona:BinaryDataObject">
                <div><strong><span>Size (bytes)</span> : </strong><xsl:value-of select="sum(medona:DataObjectPackage/medona:BinaryDataObject/medona:Size)"/></div>
            </xsl:if>

            <xsl:if test="medona:ReplyCode">
                <div><strong><span>Reply code</span> : </strong><xsl:value-of select="medona:ReplyCode"/></div>
            </xsl:if>
        </div>

        <!--Authorization request content -->
        <xsl:apply-templates select="medona:AuthorizationRequestContent"/>

        <!--Unit identifier-->
        <xsl:apply-templates select="medona:UnitIdentifier"/>

        <!--Comments-->
        <xsl:apply-templates select="medona:Comment"/>

        <!--Data object package-->
        <xsl:apply-templates select="medona:DataObjectPackage" />

        <script type="text/javascript">
            $('.showDocument').on('click', function(){
                $messageId = $(this).closest('.dataObjects').attr('id');
                
                $.ajax({
                url         : "/medona/message/" + $messageId + "/attachment/" + $(this).data('id'),
                type        : "GET",
                success     : function (response, status, xhr) {
                        $('#viewer').attr('data', response);
                        $('#viewer').attr('type', xhr.getResponseHeader("content-type"));
                        $('#viewModalDocument').modal();
                    }
                });
            })
            
            function utf8_to_b64( str ) {
                return window.btoa(unescape(encodeURIComponent( str )));
            }

            $('.showDocumentBase64').on('click', function(){
                content = $(this).attr('data-content');
                mimeType = $(this).data('format');

                $.ajax({
                url         : "/medona/message/attachmentsContent/" + mimeType,
                type        : "GET",
                dataType    : "html",
                success     : function (response) {
                        $('#view_modalContainer').html(response);
                        $('#viewer').attr('data', 'data:'+mimeType+';base64,'+content);
                        $('#viewModal').modal();
                    }
                });
            })

        </script>
    </xsl:template>
    
    <!--Unit idendifier-->
    <xsl:template match="medona:UnitIdentifier">
        <div class="row" style="padding:20px">
            <h4>Unit identifier(s)</h4>
            <ul>
                <xsl:for-each select=".">
                    <li>
                        <xsl:value-of select="."/> &#160;
                        <button type="button" class="btn btn-success btn-sm archiveDescription" title="Info">
                            <xsl:attribute name="data-archiveid">
                                <xsl:value-of select="." />
                            </xsl:attribute>
                            <span class="fa fa-info-circle">&#160;</span>
                        </button>
                    </li>
                </xsl:for-each>
            </ul>
        </div>
    </xsl:template> 

    <!--Comment-->
    <xsl:template match="medona:Comment">
        <div class="row" style="padding:20px">
            <h4>Comment(s)</h4>
            <xsl:for-each select=".">
                <div translate="no"><xsl:value-of select="."/></div>
            </xsl:for-each>
        </div>
    </xsl:template>

    <!-- DataObjectPackage -->
    <xsl:template match="medona:DataObjectPackage">
        <xsl:variable name="accId" select="generate-id()" />
        <div>
            <xsl:apply-templates select="@*"/>
            <xsl:apply-templates select="medona:ManagementMetadata"/>
            <xsl:apply-templates select="medona:DescriptiveMetadata"/>
        </div>
    </xsl:template> 

    <!-- DescriptiveMetadata -->
    <xsl:template match="medona:DescriptiveMetadata">
        <xsl:variable name="accMainId" select="generate-id()" />

        <div class="panel-group" id="{$accMainId}" role="tablist" aria-multiselectable="true">

            <xsl:apply-templates select="laabs:descriptionPackage">
                <xsl:with-param name="accMainId" select="$accMainId"/>
            </xsl:apply-templates>

            <xsl:apply-templates select="laabs:archivePackage">
                <xsl:with-param name="accMainId" select="$accMainId"/>
            </xsl:apply-templates>
        </div>
    </xsl:template>

    <!-- archivePackage -->
    <xsl:template match="laabs:archivePackage">
        <xsl:param name="accMainId"/>

        <div class="panel-group" id="{$accMainId}" role="tablist" aria-multiselectable="true">
            <xsl:for-each select="rm:archive">
                <xsl:variable name="accId" select="generate-id()" />
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#{$accMainId}" href="#metadata{$accId}" aria-expanded="true" aria-controls="collapseOne">
                                Archive <xsl:value-of select="count(preceding-sibling::*)+1"/>
                            </a>
                        </h4>
                    </div>
                    <div id="metadata{$accId}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <xsl:apply-templates select="rm:descriptionObject"/>
                            <hr/>
                            <xsl:apply-templates select="rm:document"/>
                        </div>
                    </div>
                </div>
            </xsl:for-each>
        </div>
    </xsl:template>

    <!-- descriptionPackage -->
    <xsl:template match="laabs:descriptionPackage">
        <xsl:variable name="accMainId" select="generate-id()"/>

        <div class="panel-group" id="{$accMainId}" role="tablist" aria-multiselectable="true">
            <xsl:for-each select="rm:archive">
                <xsl:variable name="accId" select="generate-id()" />
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#{$accMainId}" href="#metadata{$accId}" aria-expanded="true" aria-controls="collapseOne">
                                Archive <xsl:value-of select="count(preceding-sibling::*)+1"/>
                            </a>
                        </h4>
                    </div>
                    <div id="metadata{$accId}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <xsl:variable name="docAccMainId" select="generate-id()"/>
                            <div class="panel-group" id="{$docAccMainId}" role="tablist" aria-multiselectable="true">
                                <xsl:apply-templates select="rm:descriptionObject"/>
                                <xsl:apply-templates select="rm:lifeCycle"/>
                                <hr/>
                                <xsl:apply-templates select="rm:document"/>
                            </div>
                        </div>
                    </div>
                </div>
            </xsl:for-each>
        </div>
    </xsl:template>

    <xsl:template match="rm:document">
        <xsl:variable name="docAccMainId" select="generate-id()" />
        <div class="panel-group" id="{$docAccMainId}" role="tablist" aria-multiselectable="true">
            <xsl:for-each select=".">
                <xsl:variable name="oid" select="@oid" />
                <xsl:variable name="docAccId" select="generate-id()" />
                <xsl:variable name="cpt" select="count(preceding-sibling::*[name()=document])+1" />
                <div class="panel panel-info">
                    <div class="panel-heading" role="tab">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" href="#doc{$docAccId}" aria-expanded="true" aria-controls="collapseOne">
                                Document <xsl:value-of select="$cpt"/>
                            </a>
                        </h4>
                    </div>
                    <div id="doc{$docAccId}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                            <xsl:apply-templates select="/*/medona:DataObjectPackage/medona:BinaryDataObject[@xml:id=$oid]"/>
                        </div>
                    </div>
                </div>
            </xsl:for-each>
        </div>
    </xsl:template>

    <xsl:template match="medona:BinaryDataObject">
        <dl class="dl-horizontal">
            <xsl:if test="medona:Attachment/@filename">
                <dt>File name</dt>
                <dd translate="no"><xsl:value-of select="medona:Attachment/@filename"/></dd>
            </xsl:if>

            <xsl:if test="medona:Attachment/@uri">
                <dt>URI</dt>
                <dd translate="no" style='word-wrap : break-word'><xsl:value-of select="medona:Attachment/@uri"/></dd>
            </xsl:if>

            <dt>Identifier</dt>
            <dd translate="no"><xsl:value-of select="@xml:id"/></dd>

            <dt>Format</dt>
            <dd translate="no"><xsl:value-of select="medona:Format"/></dd>

            <dt>Hash algorithm</dt>
            <dd translate="no"><xsl:value-of select="medona:MessageDigest/@algorithm"/></dd>

            <dt>Hash</dt>
            <dd translate="no"><xsl:value-of select="medona:MessageDigest"/></dd>

            <dt>Size (bytes)</dt>
            <dd translate="no"><xsl:value-of select="medona:Size"/></dd>
        </dl>

        <xsl:if test="medona:Attachment/*[text()]">
            <button type="button" class="btn btn-warning btn-sm showDocumentBase64 pull-right" title="Show">
                <xsl:attribute name="data-format"><xsl:value-of select="medona:Format"/></xsl:attribute>
                <xsl:attribute name="data-content"><xsl:value-of select="."/></xsl:attribute>
                <span class="fa fa-eye"> </span> View
            </button>
        </xsl:if>
        
        <xsl:if test="medona:Attachment/@filename">
            <button type="button" class="btn btn-warning btn-sm showDocument pull-right" title="Show">
                <xsl:attribute name="data-id"><xsl:value-of select="medona:Attachment/@filename"/></xsl:attribute>
                    <span class="fa fa-eye"> </span> View
            </button>
        </xsl:if>
    </xsl:template>

    <xsl:template match="rm:lifeCycle">
        <xsl:variable name="accId" select="generate-id()" />

        <div class="panel panel-success">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#{$accId}" aria-expanded="true" aria-controls="collapseOne">
                        Life cycle
                    </a>
                </h4>
            </div>
            <div id="{$accId}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <xsl:for-each select="rm:event">
                        <dl class="dl-horizontal">
                            <dt>Event identifier</dt>
                            <dd translate="no"><xsl:value-of select="rm:eventId"/></dd>

                            <dt>Event type</dt>
                            <dd translate="no"><xsl:value-of select="rm:eventType"/></dd>

                            <dt>Timestamp</dt>
                            <dd translate="no"><xsl:value-of select="rm:timestamp"/></dd>

                            <dt>Operation result</dt>
                            <dd translate="no"><xsl:value-of select="rm:operationResult"/></dd>

                            <dt>Description</dt>
                            <dd translate="no"><xsl:value-of select="rm:description"/></dd>

                            <dt>Hash algorithm</dt>
                            <dd translate="no"><xsl:value-of select="rm:hashAlgorithm"/></dd>

                            <dt>Hash</dt>
                            <dd translate="no"><xsl:value-of select="rm:hash"/></dd>

                            <dt>Address</dt>
                            <dd translate="no"><xsl:value-of select="rm:address"/></dd>

                            <dt>Depositor organization</dt>
                            <dd translate="no"><xsl:value-of select="rm:depositorOrgRegNumber"/></dd>
                        </dl>
                    </xsl:for-each>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:ManagementMetadata">
        <xsl:variable name="accId" select="generate-id()" />

        <div class="panel panel-warning">
            <div class="panel-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" href="#{$accId}" aria-expanded="true" aria-controls="collapseOne">
                        Management metadata
                    </a>
                </h4>
            </div>
            <div id="{$accId}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Archival profile</dt>
                        <dd translate="no"><xsl:value-of select="medona:ArchivalProfile"/></dd>

                        <dt>Service level</dt>
                        <dd translate="no"><xsl:value-of select="medona:ServiceLevel"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:AccessRule"/>
                    <xsl:apply-templates select="medona:AppraisalRule"/>
                </div>
            </div>
        </div>
        <br/>
    </xsl:template>

    <xsl:template match="medona:AuthorizationRequestContent">
        <div class="col-xs-12">
            <div class="panel panel-success">
                <div class="panel-heading"><strong>Authorization request content</strong></div>
                <div class="panel-body">
                    <dl>
                        <dt>Authorization reason</dt>
                        <dd><xsl:value-of select="medona:AuthorizationReason"/></dd>

                        <dt>Request date</dt>
                        <dd><xsl:value-of select="medona:RequestDate"/></dd>
                    </dl>
                    <div class="row">
                        <xsl:apply-templates select="medona:Requester"/>
                    </div>
                    <h4>Unit identifier(s)</h4>
                    <ul>
                        <xsl:for-each select="medona:UnitIdentifier">
                            <li>
                                <xsl:value-of select="."/> &#160;
                                <button type="button" class="btn btn-success btn-sm archiveDescription" title="Info">
                                    <xsl:attribute name="data-archiveid">
                                        <xsl:value-of select="." />
                                    </xsl:attribute>
                                    <span class="fa fa-info-circle">&#160;</span>
                                </button>
                            </li>
                        </xsl:for-each>
                    </ul>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:AppraisalRule">
        <hr/>
        <h4>Appraisal rule</h4>
        <dl class="dl-horizontal">
            <dt>Code</dt>
            <dd translate="no"><xsl:value-of select="medona:AppraisalCode"/></dd>

            <dt>Duration</dt>
            <dd translate="no"><xsl:apply-templates select="medona:Duration"/></dd>

            <dt>Start date</dt>
            <dd translate="no"><xsl:apply-templates select="medona:StartDate"/></dd>
        </dl>
    </xsl:template>

    <!--Agencies-->
    <xsl:template match="medona:ArchivalAgency">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Archival agency</strong></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Identifier</dt>
                        <dd translate="no"><xsl:value-of select="medona:Identifier"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:OrganizationDescriptiveMetadata/org:organization"/>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:TransferringAgency">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Transferring agency</strong></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Identifier</dt>
                        <dd translate="no"><xsl:value-of select="medona:Identifier"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:OrganizationDescriptiveMetadata/org:organization"/>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:OriginatingAgency">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Originating agency</strong></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Identifier</dt>
                        <dd translate="no"><xsl:value-of select="medona:Identifier"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:OrganizationDescriptiveMetadata/org:organization"/>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:Requester">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Requester</strong></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Identifier</dt>
                        <dd translate="no"><xsl:value-of select="medona:Identifier"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:OrganizationDescriptiveMetadata/org:organization"/>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:Originator">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Originator</strong></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Identifier</dt>
                        <dd translate="no"><xsl:value-of select="medona:Identifier"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:OrganizationDescriptiveMetadata/org:organization"/>
                </div>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="medona:ControlAuthority">
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Control authority</strong></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt>Identifier</dt>
                        <dd translate="no"><xsl:value-of select="medona:Identifier"/></dd>
                    </dl>
                    <xsl:apply-templates select="medona:OrganizationDescriptiveMetadata/org:organization"/>
                </div>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>


