<?php 
/****************************************************************************
* Name:            qv_objects_delete.php                                    *
* Project:         Cambusa/ryQuiver                                         *
* Version:         1.69                                                     *
* Description:     Arrows-oriented Library                                  *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
include_once "quiverinf.php";
include_once "quiverdel.php";
include_once "quiverval.php";
include_once "quiverobj.php";
include_once "quiverext.php";
include_once "quivertrg.php";
function qv_objects_delete($maestro, $data){
    global $global_quiveruserid,$global_quiverroleid;
    global $global_lastadmin;
    global $babelcode, $babelparams;
    try{
        // IMPOSTO I VALORI DI RITORNO PREDEFINITI
        $success=1;
        $message="Operazione riuscita";
        $SYSID="";
        
        // RISOLVO LE INFO DI SESSIONE
        qv_infosession($maestro);

        // INDIVIDUAZIONE RECORD
        $record=qv_solverecord($maestro, $data, "QVOBJECTS", "SYSID", "NAME", $SYSID, "TYPOLOGYID,DELETING,ROLEID,USERINSERTID");
        if($SYSID==""){
            $babelcode="QVERR_SYSID";
            $b_params=array();
            $b_pattern="Dati insufficienti per individuare il record";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
        $TYPOLOGYID=$record["TYPOLOGYID"];
        $DELETING=intval($record["DELETING"]);
        $ROLEID=$record["ROLEID"];
        $USERINSERTID=$record["USERINSERTID"];
        
        // GESTIONE DI DELETING
        if($global_lastadmin==0){
            switch($DELETING){
            case 1: // Cancellazione protetta
                if($global_quiverroleid!=$ROLEID){
                    $babelcode="QVERR_FORBIDDEN";
                    $b_params=array();
                    $b_pattern="Autorizzazioni insufficienti";
                    throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
                }
                break;
            case 2: // Cancellazione privata
                if($global_quiveruserid!=$USERINSERTID){
                    $babelcode="QVERR_FORBIDDEN";
                    $b_params=array();
                    $b_pattern="Autorizzazioni insufficienti";
                    throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
                }
                break;
            }
        }
        
        // DETERMINO SE LA CANCELLAZIONE E' VIRTUALE O DEFINITIVA
        $oper=3;
        if($t=_qv_cacheloader($maestro, "QVOBJECTTYPES", $TYPOLOGYID)){
            if( intval($t["VIRTUALDELETE"]) ){
                $oper=2;
            }
        }

        // VALIDAZIONE PERSONALIZZATA
        qv_validateobject($maestro, $data, $SYSID, $TYPOLOGYID, $oper);

        qv_deletableobject($maestro, $SYSID);
        
        // GESTIONE DELLA STORICIZZAZIONE
        _qv_historicizing($maestro, "QVOBJECT", $SYSID, $TYPOLOGYID, 2);

        // CANCELLAZIONE RECORD
        if($oper==2){
            $NAME="__$SYSID";
            $DELETED=1;
            $USERDELETEID=$global_quiveruserid;
            $TIMEDELETE="[:NOW()]";
            $sql="UPDATE QVOBJECTS SET NAME='$NAME',DELETED=$DELETED,USERDELETEID='$USERDELETEID',TIMEDELETE=$TIMEDELETE WHERE SYSID='$SYSID'";
        }
        else{
            $sql="DELETE FROM QVOBJECTS WHERE SYSID='$SYSID'";
        }

        if(!maestro_execute($maestro, $sql, false)){
            $babelcode="QVERR_EXECUTE";
            $trace=debug_backtrace();
            $b_params=array("FUNCTION" => $trace[0]["function"] );
            $b_pattern=$maestro->errdescr;
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }

        // CANCELLO ALIAS, ALLEGATI, ALLOCAZIONI, SELEZIONI
        maestro_execute($maestro, "DELETE FROM QVALIASES WHERE RECORDID='$SYSID' AND TABLENAME='QVOBJECTS'");
        maestro_execute($maestro, "DELETE FROM QVTABLEFILE WHERE RECORDID='$SYSID' AND TABLENAME='QVOBJECTS'");
        maestro_execute($maestro, "DELETE FROM QVALLOCATIONS WHERE RECORDID='$SYSID' AND TABLENAME='QVOBJECTS'");
        _qv_clearselections($maestro, $SYSID);

        // GESTIONE DEI DATI ESTESI
        qv_extension($maestro, $data, "QVOBJECT", $SYSID, $TYPOLOGYID, $oper);

        // TRIGGER PERSONALIZZATO
        qv_triggerobject($maestro, $data, $SYSID, $TYPOLOGYID, $oper);
    }
    catch(Exception $e){
        $success=0;
        $message=$e->getMessage();
    }
    // USCITA JSON
    $j=array();
    $j["success"]=$success;
    $j["code"]=$babelcode;
    $j["params"]=$babelparams;
    $j["message"]=$message;
    $j["SYSID"]=$SYSID;
    return $j; //ritorno standard
}
?>