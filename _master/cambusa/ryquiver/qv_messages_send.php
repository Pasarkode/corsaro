<?php 
/****************************************************************************
* Name:            qv_messages_send.php                                  *
* Project:         Cambusa/ryQuiver                                         *
* Version:         1.00                                                     *
* Description:     Arrows-oriented Library                                  *
* Copyright (C):   2013  Rodolfo Calzetti                                   *
* License GNU GPL: http://www.rudyz.net/cambusa/license.html                *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
include_once "quiverinf.php";
function qv_messages_send($maestro, $data){
    global $babelcode, $babelparams;
    try{
        // IMPOSTO I VALORI DI RITORNO PREDEFINITI
        $success=1;
        $message="Operazione riuscita";
        $SYSID="";
        
        // DETERMINO UN NUOVO SYSID
        $SYSID=qv_createsysid($maestro);
        
        // DETERMINO DESCRIPTION
        if(isset($data["DESCRIPTION"])){
            $DESCRIPTION=ryqEscapize(qv_inputUTF8($data["DESCRIPTION"]), 200);
            if($DESCRIPTION=="")
                $DESCRIPTION="Nessuna descrizione";
        }
        else{
            $DESCRIPTION="Nessuna descrizione";
        }
            
        // DETERMINO REGISTRY
        $clobs=false;
        if(isset($data["REGISTRY"]))
            qv_setclob($maestro, "REGISTRY", $data["REGISTRY"], $REGISTRY, $clobs);
        else
            $REGISTRY="''";

        // DETERMINO SENDERID
        qv_solveuser($maestro, $data, "SENDERID", "SENDEREGO", "SENDERNAME", $SENDERID, $SENDERNAME);
        if($SENDERID==""){
            $babelcode="QVERR_SENDERID";
            $b_params=array();
            $b_pattern="Mittente non specificato";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }

        // DETERMINO RECEIVERID
        qv_solveuser($maestro, $data, "RECEIVERID", "RECEIVEREGO", "RECEIVERNAME", $RECEIVERID, $RECEIVERNAME);
        if($RECEIVERID==""){
            $babelcode="QVERR_RECEIVERID";
            $b_params=array();
            $b_pattern="Destinatario non specificato";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
        
        // DETERMINO PRIORITY   0 (normale), 1 (importante), 2 (urgente, errore)
        if(isset($data["PRIORITY"])){
            $PRIORITY=intval($data["PRIORITY"]);
            if($PRIORITY<0 || $PRIORITY>2 )
                $PRIORITY=0;
        }
        else{
            $PRIORITY=0;
        }
        
        // DETERMINO ACTION
        if(isset($data["ACTION"]))
            $ACTION=ryqEscapize($data["ACTION"]);
        else
            $ACTION="";
            
        $STATUS=0;  // 0 (sent), 1 (received), 2 (viewed), 4 (deleted)
        $SENDINGTIME="[:NOW()]";
        $RECEIVINGTIME="[:DATE(" . LOWEST_DATE . ")]";
        
        // PREDISPONGO COLONNE E VALORI DA REGISTRARE
        $columns="SYSID,DESCRIPTION,REGISTRY,SENDERID,RECEIVERID,SENDINGTIME,RECEIVINGTIME,PRIORITY,STATUS,ACTION";
        $values="'$SYSID','$DESCRIPTION',$REGISTRY,'$SENDERID','$RECEIVERID',$SENDINGTIME,$RECEIVINGTIME,$PRIORITY,$STATUS,'$ACTION'";
        $sql="INSERT INTO QVMESSAGES($columns) VALUES($values)";
        
        if(!maestro_execute($maestro, $sql, false, $clobs)){
            $babelcode="QVERR_EXECUTE";
            $trace=debug_backtrace();
            $b_params=array("FUNCTION" => $trace[0]["function"] );
            $b_pattern=$maestro->errdescr;
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
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