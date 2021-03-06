CREATE VIEW QWXEVENTS AS
SELECT 
    QVARROWS.BOWID AS OBJECTID,
    QVARROWS.GENREID AS GENREID,
    QVARROWS.AUXTIME AS EVENTTIME
FROM QVARROWS
INNER JOIN QVOBJECTS ON QVOBJECTS.SYSID=QVARROWS.BOWID AND QVOBJECTS.REFGENREID=QVARROWS.GENREID
WHERE QVARROWS.STATUS>0 AND QVARROWS.AVAILABILITY<2 AND QVARROWS.CONSISTENCY=0 AND QVARROWS.DELETED=0
GROUP BY QVARROWS.BOWID, QVARROWS.GENREID, QVARROWS.AUXTIME

UNION 

SELECT 
    QVARROWS.TARGETID AS OBJECTID,
    QVARROWS.GENREID AS GENREID,
    QVARROWS.AUXTIME AS EVENTTIME
FROM QVARROWS
INNER JOIN QVOBJECTS ON QVOBJECTS.SYSID=QVARROWS.TARGETID AND QVOBJECTS.REFGENREID=QVARROWS.GENREID
WHERE QVARROWS.STATUS>0 AND QVARROWS.AVAILABILITY<2 AND QVARROWS.CONSISTENCY=0 AND QVARROWS.DELETED=0
GROUP BY QVARROWS.TARGETID, QVARROWS.GENREID, QVARROWS.AUXTIME
