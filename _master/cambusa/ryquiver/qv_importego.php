<?php 
/****************************************************************************
* Name:            qv_importego.php                                         *
* Project:         Cambusa/ryQuiver                                         *
* Version:         1.69                                                     *
* Description:     Arrows-oriented Library                                  *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         https://github.com/cambusa                               *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
include_once $path_cambusa."ryquiver/quiverinf.php";
include_once $path_cambusa."rygeneral/post_request.php";
function qv_importego($maestro, $data){
    global $babelcode, $babelparams, $url_cambusa;
    try{
        // IMPOSTO I VALORI DI RITORNO PREDEFINITI
        $success=1;
        $message="Operazione riuscita";
        $SYSID="";

        // DETERMINO SESSIONID
        if(isset($data["SESSIONID"]))
            $SEXID=ryqEscapize($data["SESSIONID"]);
        else
            $SEXID="";

        // DETERMINO APPID
        if(isset($data["APPID"]))
            $APPID=ryqEscapize($data["APPID"]);
        else
            $APPID="";

        // DETERMINO ENVID
        if(isset($data["ENVID"]))
            $ENVID=ryqEscapize($data["ENVID"]);
        else
            $ENVID="";

        // IMPORTO DA EGO TUTTI GLI UTENTI E TUTTI I RUOLI
        $postdata = array(
            'sessionid' => $SEXID,
            'appid' => $APPID,
            'envid' => $ENVID
        );
        $ego=json_decode( do_post_request($url_cambusa."ryego/ego_export.php?appid=$APPID&envid=$ENVID", $postdata) );

        $users=$ego->infos->USERS;
        for($i=0; $i<count($users); $i++){
            $egoid=$users[$i]->SYSID;
            $egoname=html_entity_decode($users[$i]->NAME);
            $egoreg=html_entity_decode($users[$i]->REGISTRY);
            $egoadmin=intval($users[$i]->ADMINISTRATOR);
            $egoemail=html_entity_decode($users[$i]->EMAIL);
            maestro_query($maestro, "SELECT * FROM QVUSERS WHERE EGOID='$egoid' AND ARCHIVED=0", $r);
            if(count($r)==1){
                $quiverid=$r[0]["SYSID"];
                $qvname=$r[0]["USERNAME"];
                $qvreg=$r[0]["REGISTRY"];
                $qvadmin=intval($r[0]["ADMINISTRATOR"]);
                $qvemail=$r[0]["EMAIL"];
                if($qvname!=$egoname || $qvreg!=$egoreg || $qvadmin!=$egoadmin || $qvemail!=$egoemail){
                    // ALLINEO LA TABELLA INTERNA CON IL DATO EGO MODIFICATO
                    $egoname=ryqEscapize($egoname);
                    $egoreg=ryqEscapize($egoreg);
                    $egoemail=ryqEscapize($egoemail);
                    maestro_execute($maestro, "UPDATE QVUSERS SET USERNAME='$egoname',REGISTRY='$egoreg', ADMINISTRATOR=$egoadmin, EMAIL='$egoemail' WHERE SYSID='$quiverid'");
                }
            }
            else{
                // ARCHIVIO TUTTI I RECORD CHE HANNO LO STESSO USERNAME PER CONSIDERARLI OBSOLETI
                maestro_execute($maestro, "UPDATE QVUSERS SET ARCHIVED=1 WHERE [:UPPER(USERNAME)]='" . strtoupper( ryqEscapize($egoname) ) . "'");
                // INSERISCO UNA COPIA DEI DATI EGO NELLA TABELLA INTERNA
                $quiverid=qv_createsysid($maestro);
                $egoname=ryqEscapize($egoname);
                $egoreg=ryqEscapize($egoreg);
                $egoemail=ryqEscapize($egoemail);
                maestro_execute($maestro, "INSERT INTO QVUSERS(SYSID,EGOID,USERNAME,REGISTRY,ADMINISTRATOR,EMAIL,ARCHIVED) VALUES('$quiverid','$egoid','$egoname','$egoreg',$egoadmin,'$egoemail',0)");
            }
            
        }
        $roles=$ego->infos->ROLES;
        for($i=0; $i<count($roles); $i++){
            $egoid=$roles[$i]->SYSID;
            $egoname=html_entity_decode($roles[$i]->NAME);
            maestro_query($maestro, "SELECT * FROM QVROLES WHERE EGOID='$egoid' AND ARCHIVED=0", $r);
            if(count($r)==1){
                $quiverid=$r[0]["SYSID"];
                $qvname=$r[0]["ROLENAME"];
                if($qvname!=$egoname){
                    // ALLINEO LA TABELLA INTERNA CON IL DATO EGO MODIFICATO
                    $egoname=ryqEscapize($egoname);
                    maestro_execute($maestro, "UPDATE QVROLES SET ROLENAME='$egoname' WHERE SYSID='$quiverid'");
                }
            }
            else{
                // ARCHIVIO TUTTI I RECORD CHE HANNO LO STESSO ROLENAME PER CONSIDERARLI OBSOLETI
                maestro_execute($maestro, "UPDATE QVROLES SET ARCHIVED=1 WHERE [:UPPER(ROLENAME)]='".strtoupper( ryqEscapize($egoname) )."'");
                // INSERISCO UNA COPIA DEI DATI EGO NELLA TABELLA INTERNA
                $quiverid=qv_createsysid($maestro);
                $egoname=ryqEscapize($egoname);
                maestro_execute($maestro, "INSERT INTO QVROLES(SYSID,EGOID,ROLENAME,ARCHIVED) VALUES('$quiverid','$egoid','$egoname',0)");
            }
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