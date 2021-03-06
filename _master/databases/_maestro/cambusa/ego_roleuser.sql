CREATE VIEW EGOVIEWROLEUSER AS 
SELECT
    EGOROLEUSER.SYSID AS SYSID,
    EGOROLEUSER.ROLEID AS ROLEID,
    EGOROLES.APPID AS APPID,
    EGOROLES.NAME AS ROLENAME,
    EGOROLES.DESCRIPTION AS DESCRIPTION,
    EGOALIASES.NAME AS USERNAME,
    EGOUSERS.SYSID AS USERID
FROM EGOROLEUSER
INNER JOIN EGOROLES ON EGOROLES.SYSID=EGOROLEUSER.ROLEID
INNER JOIN EGOUSERS ON EGOUSERS.SYSID=EGOROLEUSER.USERID
INNER JOIN EGOALIASES ON EGOALIASES.USERID=EGOUSERS.SYSID 
WHERE
    EGOALIASES.MAIN=1 AND EGOUSERS.ACTIVE=1
