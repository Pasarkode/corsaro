CREATE VIEW QWUSERS AS
SELECT 
    QVUSERS.SYSID AS SYSID, 
    QVUSERS.EGOID AS EGOID, 
    QVUSERS.USERNAME AS DESCRIPTION,
    QVUSERS.ADMINISTRATOR AS ADMINISTRATOR,
    QVUSERS.EMAIL AS EMAIL
FROM QVUSERS
WHERE ARCHIVED=0
