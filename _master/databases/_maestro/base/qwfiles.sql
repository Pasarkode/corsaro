CREATE VIEW QWFILES AS
SELECT 
    QVTABLEFILE.SYSID AS SYSID, 
    QVFILES.NAME AS NAME, 
    QVFILES.DESCRIPTION AS DESCRIPTION, 
    QVFILES.AUXTIME AS AUXTIME, 
    QVFILES.REGISTRY AS REGISTRY, 
    QVFILES.SUBPATH AS SUBPATH, 
    QVFILES.IMPORTNAME AS IMPORTNAME, 
    QVFILES.EXTENSION AS EXTENSION,
    QVTABLEFILE.TABLENAME AS TABLENAME, 
    QVTABLEFILE.RECORDID AS RECORDID, 
    QVTABLEFILE.FILEID AS FILEID,
    QVTABLEFILE.SORTER AS SORTER
FROM QVTABLEFILE
INNER JOIN QVFILES ON QVFILES.SYSID=QVTABLEFILE.FILEID
WHERE QVFILES.DELETED=0
