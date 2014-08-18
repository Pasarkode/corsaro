<?php 
/****************************************************************************
* Name:            qv_objectviews_insert.php                                *
* Project:         Cambusa/ryQuiver                                         *
* Version:         1.00                                                     *
* Description:     Arrows-oriented Library                                  *
* Copyright (C):   2013  Rodolfo Calzetti                                   *
* License GNU GPL: http://www.rudyz.net/cambusa/license.html                *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
include_once "quivervws.php";
function qv_objectviews_insert($maestro, $data){
    global $babelcode, $babelparams;
    global $global_lastadmin;
    try{
        // IMPOSTO I VALORI DI RITORNO PREDEFINITI
        $success=1;
        $message="Operazione riuscita";
        $SYSID="";
        
        // GESTIONE AMMINISTRATORE
        if($global_lastadmin==0){
            $babelcode="QVERR_FORBIDDEN";
            $b_params=array();
            $b_pattern="Autorizzazioni insufficienti";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
        
        // DETERMINO UN NUOVO SYSID
        $SYSID=qv_createsysid($maestro);
        
        // DETERMINO TYPOLOGYID
        qv_solverecord($maestro, $data, "QVOBJECTTYPES", "TYPOLOGYID", "TYPOLOGYNAME", $TYPOLOGYID);
        if($TYPOLOGYID==""){
            $babelcode="QVERR_OBJECTTYPE";
            $b_params=array();
            $b_pattern="Tipo di oggetto non specificato";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
        
        // DETERMINO FIELDNAME
        if(isset($data["FIELDNAME"])){
            $FIELDNAME=ryqEscapize($data["FIELDNAME"], 50);
            qv_checkfieldname($maestro, "QVOBJECTVIEWS", $SYSID, $TYPOLOGYID, $FIELDNAME);
        }
        else{
            $babelcode="QVERR_FIELDNAME";
            $b_params=array();
            $b_pattern="Nome del campo non specificato";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
            
        // DETERMINO FIELDTYPE
        if(isset($data["FIELDTYPE"]))
            $FIELDTYPE=ryqEscapize($data["FIELDTYPE"], 50);
        else
            $FIELDTYPE="CHAR(50)";
        
        // DETERMINO FORMULA
        if(isset($data["FORMULA"]))
            $FORMULA=ryqEscapize($data["FORMULA"], 200);
        else
            $FORMULA="";
        
        // DETERMINO CAPTION
        if(isset($data["CAPTION"]))
            $CAPTION=ryqEscapize($data["CAPTION"], 50);
        else
            $CAPTION=$FIELDNAME;
        
        // DETERMINO WRITABLE
        if(isset($data["WRITABLE"])){
            if(intval($data["WRITABLE"])!=0)
                $WRITABLE=1;
            else
                $WRITABLE=0;
        }
        else{
            $WRITABLE=0;
        }
        
        // DROPPO LA VECCHIA VIEW
        qv_deleteview($maestro, "QVOBJECT", $TYPOLOGYID);
        
        // PREDISPONGO COLONNE E VALORI DA REGISTRARE
        $columns="SYSID,TYPOLOGYID,FIELDNAME,FIELDTYPE,FORMULA,CAPTION,WRITABLE";
        $values="'$SYSID','$TYPOLOGYID','$FIELDNAME','$FIELDTYPE','$FORMULA','$CAPTION',$WRITABLE";
        $sql="INSERT INTO QVOBJECTVIEWS($columns) VALUES($values)";
        
        if(!maestro_execute($maestro, $sql, false)){
            $babelcode="QVERR_EXECUTE";
            $trace=debug_backtrace();
            $b_params=array("FUNCTION" => $trace[0]["function"] );
            $b_pattern=$maestro->errdescr;
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
        // RICREO LA VIEW
        qv_refreshview($maestro, "QVOBJECT", $SYSID, $TYPOLOGYID);
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