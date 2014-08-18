CREATE VIEW QWQUIVERS AS
SELECT 
    SYSID,
    NAME,
    DESCRIPTION,
    REGISTRY,
    AUXTIME,
    TYPOLOGYID,
    REFGENREID,
    REFOBJECTID,
    REFMOTIVEID,
    REFARROWID,
    REFQUIVERID,
    REFERENCE,
    TAG,
    CONSISTENCY,
    SCOPE,
    UPDATING,
    DELETING,
    STATUS,
    STATUSTIME,
    PHASE,
    PHASENOTE
FROM QVQUIVERS
WHERE DELETED=0