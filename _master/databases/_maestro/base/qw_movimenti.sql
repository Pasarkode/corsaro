CREATE VIEW QW_MOVIMENTI AS 
SELECT 
    QVARROWS.SYSID AS SYSID,
    QVARROWS.DESCRIPTION AS DESCRIPTION,
    QVARROWS.REGISTRY AS REGISTRY,
    QVARROWS.TYPOLOGYID AS TYPOLOGYID,
    QVARROWS.BOWID AS BOWID,
    QVARROWS.TARGETID AS TARGETID,
    QVARROWS.BOWTIME AS BOWTIME,
    QVARROWS.TARGETTIME AS TARGETTIME,
    QVARROWS.AUXTIME AS AUXTIME,
    QVARROWS.AMOUNT AS AMOUNT,
    QVARROWS.TAG AS TAG,
    QVARROWS.STATUS AS STATUS,
    QVARROWS.PHASE AS PHASE,
    QVARROWS.CONSISTENCY AS CONSISTENCY,
    QVARROWS.MOTIVEID AS MOTIVEID,
    QVARROWS.GENREID AS GENREID,
    ARROWS_MOVIMENTI.CLUSTERID AS CLUSTERID
FROM QVARROWS 
INNER JOIN ARROWS_MOVIMENTI ON ARROWS_MOVIMENTI.SYSID=QVARROWS.SYSID 
WHERE 
    QVARROWS.TYPOLOGYID=[:SYSID(0MOVIMENTI0000)] AND QVARROWS.DELETED=0
