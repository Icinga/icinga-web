<?php
interface JasperI {

    /*
     * Reporting specific jasper constants
     */

    // Default XML settings
    const XML_VERSION = '1.0';
    const XML_ENCODING = 'UTF-8';

    // Descriptor attributes
    const DESCRIPTOR_ATTR_NAME = 'name';
    const DESCRIPTOR_ATTR_TYPE = 'wsType';
    const DESCRIPTOR_ATTR_URI = 'uriString';
    const DESCRIPTOR_ATTR_NEW = 'isNew';

    // Default jasper settings
    const JASPER_LOCALE  = 'en';
    const JASPER_SOAPPARAMNAME = 'requestXmlString';
    const JASPER_NS = 'http://axis2.ws.jasperserver.jaspersoft.com';

    // HTTP generic
    const HEADER_SPLIT = "\r\n\r\n";

    // Soap Content id's
    const CONTENT_ID_ATTACHMENT = 'attachment';
    const CONTENT_ID_REPORT = 'report';
    const CONTENT_ID_RESPONSE = 'direct-soap-reply';

    // Soap specific
    const SOAP_BLIND_ERROR = 'looks like we got no XML document';
    const SOAP_NS = 'http://schemas.xmlsoap.org/soap/envelope/';
    const SOAP_ROOT = 'Envelope';

    /*
     * Jasper provided constants
     */

    // resource wsTypes
    const TYPE_FOLDER = "folder";
    const TYPE_REPORTUNIT = "reportUnit";
    const TYPE_DATASOURCE = "datasource";
    const TYPE_DATASOURCE_JDBC = "jdbc";
    const TYPE_DATASOURCE_JNDI = "jndi";
    const TYPE_DATASOURCE_BEAN = "bean";
    const TYPE_IMAGE = "img";
    const TYPE_FONT = "font";
    const TYPE_JRXML = "jrxml";
    const TYPE_CLASS_JAR = "jar";
    const TYPE_RESOURCE_BUNDLE = "prop";
    const TYPE_REFERENCE = "reference";
    const TYPE_INPUT_CONTROL = "inputControl";
    const TYPE_DATA_TYPE = "dataType";
    const TYPE_OLAP_MONDRIAN_CONNECTION = "olapMondrianCon";
    const TYPE_OLAP_XMLA_CONNECTION = "olapXmlaCon";
    const TYPE_MONDRIAN_SCHEMA = "olapMondrianSchema";
    const TYPE_XMLA_CONNTCTION = "xmlaConnection";
    const TYPE_UNKNOW = "unknow";
    const TYPE_LOV = "lov"; // List of values...
    const TYPE_QUERY = "query";

    // These constants are copied here from DataType for facility
    const DT_TYPE_TEXT = 1;
    const DT_TYPE_NUMBER = 2;
    const DT_TYPE_DATE = 3;
    const DT_TYPE_DATE_TIME = 4;

    // These constants are copied here from InputControl for facility
    const IC_TYPE_BOOLEAN = 1;
    const IC_TYPE_SINGLE_VALUE = 2;
    const IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES = 3;
    const IC_TYPE_SINGLE_SELECT_QUERY = 4;
    const IC_TYPE_MULTI_VALUE = 5;
    const IC_TYPE_MULTI_SELECT_LIST_OF_VALUES = 6;
    const IC_TYPE_MULTI_SELECT_QUERY = 7;
    const IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES_RADIO = 8;
    const IC_TYPE_SINGLE_SELECT_QUERY_RADIO = 9;
    const IC_TYPE_MULTI_SELECT_LIST_OF_VALUES_CHECKBOX = 10;
    const IC_TYPE_MULTI_SELECT_QUERY_CHECKBOX = 11;

    // Structural properties
    const PROP_VERSION = "PROP_VERSION";
    const PROP_PARENT_FOLDER = "PROP_PARENT_FOLDER";
    const PROP_RESOURCE_TYPE = "PROP_RESOURCE_TYPE";
    const PROP_CREATION_DATE = "PROP_CREATION_DATE";

    // File resource properties
    const PROP_FILERESOURCE_HAS_DATA = "PROP_HAS_DATA";
    const PROP_FILERESOURCE_IS_REFERENCE = "PROP_IS_REFERENCE";
    const PROP_FILERESOURCE_REFERENCE_URI = "PROP_REFERENCE_URI";
    const PROP_FILERESOURCE_WSTYPE = "PROP_WSTYPE";

    // Datasource properties
    const PROP_DATASOURCE_DRIVER_CLASS = "PROP_DATASOURCE_DRIVER_CLASS";
    const PROP_DATASOURCE_CONNECTION_URL = "PROP_DATASOURCE_CONNECTION_URL";
    const PROP_DATASOURCE_USERNAME = "PROP_DATASOURCE_USERNAME";
    const PROP_DATASOURCE_PASSWORD = "PROP_DATASOURCE_PASSWORD";
    const PROP_DATASOURCE_JNDI_NAME = "PROP_DATASOURCE_JNDI_NAME";
    const PROP_DATASOURCE_BEAN_NAME = "PROP_DATASOURCE_BEAN_NAME";
    const PROP_DATASOURCE_BEAN_METHOD = "PROP_DATASOURCE_BEAN_METHOD";

    // ReportUnit resource properties
    const PROP_RU_DATASOURCE_TYPE = "PROP_RU_DATASOURCE_TYPE";
    const PROP_RU_IS_MAIN_REPORT = "PROP_RU_IS_MAIN_REPORT";
    const PROP_RU_INPUTCONTROL_RENDERING_VIEW = "PROP_RU_INPUTCONTROL_RENDERING_VIEW";
    const PROP_RU_REPORT_RENDERING_VIEW = "PROP_RU_REPORT_RENDERING_VIEW";

    // DataType resource properties
    const PROP_DATATYPE_STRICT_MAX = "PROP_DATATYPE_STRICT_MAX";
    const PROP_DATATYPE_STRICT_MIN = "PROP_DATATYPE_STRICT_MIN";
    const PROP_DATATYPE_MIN_VALUE = "PROP_DATATYPE_MIN_VALUE";
    const PROP_DATATYPE_MAX_VALUE = "PROP_DATATYPE_MAX_VALUE";
    const PROP_DATATYPE_PATTERN = "PROP_DATATYPE_PATTERN";
    const PROP_DATATYPE_TYPE = "PROP_DATATYPE_TYPE";

    // ListOfValues resource properties
    const PROP_LOV = "PROP_LOV";
    const PROP_LOV_LABEL = "PROP_LOV_LABEL";
    const PROP_LOV_VALUE = "PROP_LOV_VALUE";

    // InputControl resource properties
    const PROP_INPUTCONTROL_TYPE = "PROP_INPUTCONTROL_TYPE";
    const PROP_INPUTCONTROL_IS_MANDATORY = "PROP_INPUTCONTROL_IS_MANDATORY";
    const PROP_INPUTCONTROL_IS_READONLY = "PROP_INPUTCONTROL_IS_READONLY";

    // SQL resource properties
    const PROP_QUERY = "PROP_QUERY";
    const PROP_QUERY_VISIBLE_COLUMNS = "PROP_QUERY_VISIBLE_COLUMNS";
    const PROP_QUERY_VISIBLE_COLUMN_NAME = "PROP_QUERY_VISIBLE_COLUMN_NAME";
    const PROP_QUERY_VALUE_COLUMN = "PROP_QUERY_VALUE_COLUMN";
    const PROP_QUERY_LANGUAGE = "PROP_QUERY_LANGUAGE";
    // SQL resource properties (data)
    const PROP_QUERY_DATA = "PROP_QUERY_DATA";
    const PROP_QUERY_DATA_ROW = "PROP_QUERY_DATA_ROW";
    const PROP_QUERY_DATA_ROW_COLUMN = "PROP_QUERY_DATA_ROW_COLUMN";

    // Arguments
    const MODIFY_REPORTUNIT = "MODIFY_REPORTUNIT_URI";
    const CREATE_REPORTUNIT = "CREATE_REPORTUNIT_BOOLEAN";
    const LIST_DATASOURCES = "LIST_DATASOURCES";
    const IC_GET_QUERY_DATA = "IC_GET_QUERY_DATA";
    const VALUE_TRUE = "true";
    const VALUE_FALSE = "false";

    // Output formats
    const RUN_OUTPUT_FORMAT = "RUN_OUTPUT_FORMAT";
    const RUN_OUTPUT_FORMAT_PDF = "PDF";
    const RUN_OUTPUT_FORMAT_JRPRINT = "JRPRINT";
    const RUN_OUTPUT_FORMAT_HTML = "HTML";
    const RUN_OUTPUT_FORMAT_XLS = "XLS";
    const RUN_OUTPUT_FORMAT_XML = "XML";
    const RUN_OUTPUT_FORMAT_CSV = "CSV";
    const RUN_OUTPUT_FORMAT_RTF = "RTF";
    const RUN_OUTPUT_IMAGES_URI = "IMAGES_URI";
    const RUN_OUTPUT_PAGE = "PAGE";
}
?>