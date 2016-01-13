<?php 
/****************************************************************************
* Name:            ego_validate.php                                         *
* Project:         Cambusa/ryEgo                                            *
* Version:         1.69                                                     *
* Description:     Central Authentication Service (CAS)                     *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         https://github.com/cambusa                               *
*                  postmaster@rudyz.net                                     *
****************************************************************************/

// Variabili utili al cliente del servizio
$global_lastuserid="";
$global_lastusername="";
$global_lastadmin=0;
$global_lastemail="";
$global_lastroleid="";
$global_lastrolename="";
$global_lastenvid="";
$global_lastenvname="";
$global_lastlanguage=$config_defaultlang;
$global_lastcountrycode="";
$global_lastdebugmode=false;
$global_lastclientip="";

function ego_validatesession($maestro, $ses, $info=false, $context="ego"){
    global $public_sessionid,
           $check_sessionip,
           $global_lastuserid,
           $global_lastusername,
           $global_lastadmin,
           $global_lastemail,
           $global_lastroleid,
           $global_lastrolename,
           $global_lastenvid,
           $global_lastenvname,
           $global_lastlanguage,
           $global_lastcountrycode,
           $global_lastdebugmode,
           $global_lastclientip,
           $path_databases,
           $global_backslash;
    if($public_sessionid=="" || $ses!=$public_sessionid){
        if($info)
            $sql="SELECT EGOSESSIONS.SYSID AS SYSID, EGOSESSIONS.ENVIRONID AS ENVID, EGOALIASES.USERID AS USERID, EGOALIASES.NAME AS USERNAME, EGOALIASES.DEMIURGE AS DEMIURGE, EGOALIASES.ADMINISTRATOR AS ADMINISTRATOR, EGOALIASES.EMAIL AS EMAIL, EGOSESSIONS.ROLEID AS ROLEID, EGOSESSIONS.LANGUAGEID AS LANGUAGEID, EGOSESSIONS.COUNTRYCODE AS COUNTRYCODE, EGOSESSIONS.DEBUGMODE AS DEBUGMODE, EGOSESSIONS.CLIENTIP AS CLIENTIP FROM EGOSESSIONS INNER JOIN EGOALIASES ON EGOALIASES.SYSID=EGOSESSIONS.ALIASID WHERE EGOSESSIONS.SESSIONID='$ses' AND EGOSESSIONS.ENDTIME IS NULL AND [:DATE(EGOSESSIONS.RENEWALTIME, 1DAYS)]>[:TODAY()]";
        else
            $sql="SELECT SYSID,DEMIURGE,ADMINISTRATOR,LANGUAGEID,COUNTRYCODE,DEBUGMODE,CLIENTIP FROM EGOSESSIONS WHERE SESSIONID='$ses' AND ENDTIME IS NULL AND [:DATE(RENEWALTIME, 1DAYS)]>[:TODAY()]";
        maestro_query($maestro, $sql, $r);
        if(count($r)==1){
            $sysid=$r[0]["SYSID"];
            $ip=$r[0]["CLIENTIP"];
            $demiurge=intval($r[0]["DEMIURGE"]);
            $administrator=intval($r[0]["ADMINISTRATOR"]);
            if($context=="" || $context=="quiver" || $context=="export" || $context=="mirror" || 
               ($context=="ego" && $demiurge==1) ||
               ($context=="maestro" && $administrator==1) ||
               ($context=="pulse" && $administrator==1)
              ){
                // CONTROLLO IP
                $currip=get_ip_address();
                if($ip!=$currip){
                    writelog("Function=EgoValidate|ClientIP=$ip|CurrentIP=$currip");
                }
                if($ip==$currip || $context=="export" || $check_sessionip==false){
                    if($info){
                        $global_lastuserid=$r[0]["USERID"];
                        $global_lastusername=$r[0]["USERNAME"];
                        $global_lastadmin=$administrator;
                        $global_lastemail=$r[0]["EMAIL"];
                        $global_lastroleid=$r[0]["ROLEID"];
                        $global_lastenvid=$r[0]["ENVID"];
                        $languageid=$r[0]["LANGUAGEID"];
                        $global_lastlanguage="";
                        $global_lastcountrycode=$r[0]["COUNTRYCODE"];
                        $global_lastdebugmode=intval($r[0]["DEBUGMODE"]);
                        $global_lastclientip=$r[0]["CLIENTIP"];
                        
                        // REPERISCO IL NOME DEL RUOLO E DELL'AMBIENTE
                        $sql="SELECT EGOROLES.NAME AS ROLENAME, EGOENVIRONS.NAME AS ENVNAME FROM EGOROLES INNER JOIN EGOENVIRONS ON EGOENVIRONS.SYSID='$global_lastenvid' AND EGOENVIRONS.APPID=EGOROLES.APPID WHERE EGOROLES.SYSID='$global_lastroleid'";
                        maestro_query($maestro, $sql, $s);
                        if(count($s)==1){
                            $global_lastrolename=$s[0]["ROLENAME"];
                            $global_lastenvname=$s[0]["ENVNAME"];
                        }

                        // REPERISCO IL NOME DELLA LINGUA
                        $sql="SELECT NAME FROM EGOLANGUAGES WHERE SYSID='$languageid'";
                        maestro_query($maestro, $sql, $s);
                        if(count($s)==1)
                            $global_lastlanguage=$s[0]["NAME"];
                            
                    }
                    // AGGIORNAMENTO EGOSESSIONS
                    $sql="UPDATE EGOSESSIONS SET RENEWALTIME=[:NOW()] WHERE SYSID='$sysid'";
                    maestro_execute($maestro, $sql, false);
                    return true;
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    else{
        $global_lastuserid="__GUESTID";
        $global_lastusername="GUEST";
        $global_lastadmin=0;
        $global_lastemail="";
        $global_lastroleid="_ROLEID";
        $global_lastrolename="NONE";
        $global_lastenvid="";
        $global_lastenvname="";
        $global_lastlanguage="";
        $global_lastcountrycode="";
        $global_lastdebugmode=false;
        $global_lastclientip=get_ip_address();
        return true;
    }
    // GESTIONE BACKSLASH
    $global_backslash=intval(@file_get_contents($path_databases."_configs/backslash.par"));
}

function ext_validatesession($ses, $info=false, $context=""){
    $ret=false;
    // APRO IL DATABASE
    $maestro=maestro_opendb("ryego");
    if($maestro->conn!==false){
        $ret=ego_validatesession($maestro, $ses, $info, $context);
    }
    // CHIUDO IL DATABASE
    maestro_closedb($maestro);
    return $ret;
}

function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }
}
?>