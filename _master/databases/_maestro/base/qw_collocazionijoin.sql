CREATE VIEW QW_COLLOCAZIONIJOIN AS
SELECT 
    QW_COLLOCAZIONI.SYSID AS SYSID, 
    QW_COLLOCAZIONI.DESCRIPTION AS DESCRIPTION,
    QW_COLLOCAZIONI.REGISTRY AS REGISTRY,
    QW_COLLOCAZIONI.TYPOLOGYID AS TYPOLOGYID,
    QW_COLLOCAZIONI.TAG AS TAG,
    QW_COLLOCAZIONI.REFGENREID AS REFGENREID,
    QW_COLLOCAZIONI.MAGAZZINOID AS MAGAZZINOID,
    QW_COLLOCAZIONI.ZONA AS ZONA,
    QW_COLLOCAZIONI.SCAFFALE AS SCAFFALE,
    QW_COLLOCAZIONI.RIPIANO AS RIPIANO,
    QW_COLLOCAZIONI.COORDINATA AS COORDINATA,
    QW_ARTICOLI.DESCRIPTION AS ARTICOLO,
    CASE
    WHEN QW_UFFICI.AZIENDAID<>'' THEN QW_UFFICI.AZIENDAID
    WHEN QW_UFFICI.PROPRIETAID<>'' THEN QW_UFFICI.PROPRIETAID
    WHEN QW_UFFICI.RESPONSABILEID<>'' THEN QW_UFFICI.RESPONSABILEID
    ELSE ''
    END AS PROPRIETARIOID
FROM QW_COLLOCAZIONI
LEFT JOIN QW_ARTICOLI ON QW_ARTICOLI.SYSID=QW_COLLOCAZIONI.REFGENREID
LEFT JOIN QW_UFFICI ON QW_UFFICI.SYSID=QW_COLLOCAZIONI.MAGAZZINOID
