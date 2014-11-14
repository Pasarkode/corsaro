<?php
/****************************************************************************
* Name:            appvalidatearrow.php                                     *
* Project:         Corsaro/ryQuiver Extension                               *
* Version:         1.00                                                     *
* Description:     Arrows-oriented Library                                  *
* Copyright (C):   2014  Rodolfo Calzetti                                   *
* License GNU GPL: http://www.rudyz.net/cambusa/license.html                *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
function appvalidatearrow(
            $maestro, 
            &$data, 
            $prevdata, 
            $SYSID, 
            $TYPOLOGYID, 
            $oper, 
            $user, 
            $role, 
            &$babelcode, 
            &$failure){
    $ret=1;
    // CONTROLLO GESTIONE MOVIMENTI RELATIVI A PRATICHE
    switch( substr($TYPOLOGYID, 0, 12) ){
    case "0MOVIMENTI00":
        $data["TRIGGERSTATOID"]=qv_actualvalue($data, $prevdata, "STATOID");
        break;
    case "0ACCREDITI00":
        if($oper<=1){
            // DESCRIZIONE, DATA INIZIO
            $TARGETID=qv_actualvalue($data, $prevdata, "TARGETID");
            $CORSOID=qv_actualvalue($data, $prevdata, "CORSOID");
            $DESCRIPTION=qv_actualvalue($data, $prevdata, "DESCRIPTION");
            $BOWTIME="[:DATE(".qv_strtime(qv_actualvalue($data, $prevdata, "BOWTIME")).")]";
            
            $where="TARGETID='$TARGETID' AND ((DESCRIPTION='$DESCRIPTION' AND BOWTIME=$BOWTIME) OR (CORSOID<>'' AND CORSOID='$CORSOID'))";
            if(!qv_uniquity($maestro, "QW_ACCREDITI", $SYSID, $where)){
                $babelcode="QVUSER_NOTUNIQUE";
                $failure="Corso già presente in anagrafica";
                $ret=0;
            }
        }
        break;
    }
    return $ret;
}
?>