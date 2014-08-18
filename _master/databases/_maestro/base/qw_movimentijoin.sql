CREATE VIEW QW_MOVIMENTIJOIN AS
SELECT 
    QW_MOVIMENTI.SYSID AS SYSID, 
    QW_MOVIMENTI.DESCRIPTION AS DESCRIPTION,
    QW_MOVIMENTI.REGISTRY AS REGISTRY,
    QW_MOVIMENTI.TYPOLOGYID AS TYPOLOGYID,
    QW_MOVIMENTI.BOWID AS BOWID,
    QW_MOVIMENTI.TARGETID AS TARGETID,
    QW_MOVIMENTI.BOWTIME AS BOWTIME,
    QW_MOVIMENTI.TARGETTIME AS TARGETTIME,
    QW_MOVIMENTI.AUXTIME AS AUXTIME,
    QW_MOVIMENTI.AMOUNT AS AMOUNT,
    QW_MOVIMENTI.TAG AS TAG,
    QW_MOVIMENTI.STATUS AS STATUS,
    QW_MOVIMENTI.PHASE AS PHASE,
    QW_MOVIMENTI.CONSISTENCY AS CONSISTENCY,
    QW_MOVIMENTI.MOTIVEID AS MOTIVEID,
    QVMOTIVES.DESCRIPTION AS MOTIVE,
    QW_MOVIMENTI.GENREID AS GENREID,
    QW_MOVIMENTI.CLUSTERID AS CLUSTERID,
    QVGENRES.DESCRIPTION AS GENRE
FROM QW_MOVIMENTI
INNER JOIN QVMOTIVES ON QVMOTIVES.SYSID=QW_MOVIMENTI.MOTIVEID
INNER JOIN QVGENRES ON QVGENRES.SYSID=QW_MOVIMENTI.GENREID
