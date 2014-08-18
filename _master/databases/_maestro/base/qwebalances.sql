CREATE VIEW QWEBALANCES AS
SELECT 
EVENTTABLE.OBJECTID AS SYSID,
EVENTTABLE.GENREID AS GENREID,
EVENTTABLE.EVENTTIME AS EVENTTIME,
SUM(
CASE
WHEN EVENTTABLE.OBJECTID=QVARROWS.BOWID    AND EVENTTABLE.OBJECTID=QVARROWS.TARGETID THEN 0
WHEN EVENTTABLE.OBJECTID=QVARROWS.TARGETID AND QVARROWS.CONSISTENCY=0 THEN QVARROWS.AMOUNT
WHEN EVENTTABLE.OBJECTID=QVARROWS.BOWID    AND QVARROWS.CONSISTENCY=0 THEN -QVARROWS.AMOUNT
WHEN EVENTTABLE.OBJECTID=QVARROWS.TARGETID AND QVARROWS.CONSISTENCY=1 THEN -QVARROWS.AMOUNT
WHEN EVENTTABLE.OBJECTID=QVARROWS.BOWID    AND QVARROWS.CONSISTENCY=1 THEN +QVARROWS.AMOUNT
ELSE 0
END
) AS BALANCE
FROM QWEEVENTS EVENTTABLE 
INNER JOIN QVARROWS ON 
    QVARROWS.GENREID=EVENTTABLE.GENREID AND (
        (QVARROWS.BOWID=EVENTTABLE.OBJECTID AND QVARROWS.BOWTIME<=EVENTTABLE.EVENTTIME) OR
        (QVARROWS.TARGETID=EVENTTABLE.OBJECTID AND QVARROWS.TARGETTIME<=EVENTTABLE.EVENTTIME)
    )
WHERE QVARROWS.AVAILABILITY<2 AND QVARROWS.DELETED=0 AND 
      ((QVARROWS.CONSISTENCY=0 AND QVARROWS.STATUS>=2) OR (QVARROWS.CONSISTENCY=1 AND QVARROWS.STATUS<2))
GROUP BY EVENTTABLE.OBJECTID, EVENTTABLE.GENREID, EVENTTABLE.EVENTTIME
