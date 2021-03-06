CREATE VIEW QWQUIVEROUT AS
SELECT 
    QVARROWS.GENREID AS GENREID,
    QVARROWS.BOWID AS OBJECTID,
    QVQUIVERARROW.QUIVERID AS QUIVERID, 
    SUM(QVARROWS.AMOUNT) AS AMOUNT
FROM QVARROWS
LEFT JOIN QVQUIVERARROW ON QVQUIVERARROW.ARROWID=QVARROWS.SYSID
WHERE QVARROWS.BOWID<>'' AND QVARROWS.DELETED=0
GROUP BY QVQUIVERARROW.QUIVERID, QVARROWS.BOWID, QVARROWS.GENREID
