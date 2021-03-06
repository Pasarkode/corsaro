CREATE VIEW QW_WEBCONTENTSBROWSER AS 
SELECT
    QW_WEBCONTENTS.SYSID AS SYSID,
    QW_WEBCONTENTS.DESCRIPTION AS DESCRIPTION,
    QW_WEBCONTENTS.TAG AS TAG,
    QW_WEBCONTENTS.ABSTRACT AS ABSTRACT,
    QW_WEBCONTENTS.ICON AS ICON,
    QW_WEBCONTENTS.SCOPE AS SCOPE,
    QW_WEBCONTENTS.TARGETTIME AS TARGETTIME,
    QW_WEBCONTENTS.SITEID AS SITEID,
    QW_WEBCONTENTS.SETRELATED AS SETRELATED,
    QVOBJECTS.DESCRIPTION AS SITE
FROM QW_WEBCONTENTS
LEFT JOIN QVOBJECTS ON QVOBJECTS.SYSID=QW_WEBCONTENTS.SITEID 
