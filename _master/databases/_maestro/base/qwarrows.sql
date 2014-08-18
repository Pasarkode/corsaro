CREATE VIEW QWARROWS AS
SELECT 
    SYSID,
    NAME,
    DESCRIPTION,
    REGISTRY,
    TYPOLOGYID,
    BOWID,
    BOWTIME,
    TARGETID,
    TARGETTIME,
    AUXTIME,
    MOTIVEID,
    GENREID,
    AMOUNT,
    REFERENCE,
    REFARROWID,
    CONSISTENCY,
    AVAILABILITY,
    SCOPE,
    UPDATING,
    DELETING,
    STATUS,
    STATUSTIME,
    STATUSRISK,
    PHASE,
    PHASENOTE,
    PROVIDER,
    PARCEL,
    TAG
FROM QVARROWS
WHERE DELETED=0