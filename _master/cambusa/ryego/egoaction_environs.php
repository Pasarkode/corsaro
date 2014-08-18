<?php 
/****************************************************************************
* Name:            egoaction_environs.php                                   *
* Project:         Cambusa/ryEgo                                            *
* Version:         1.00                                                     *
* Description:     Central Authentication Service (CAS)                     *
* Copyright (C):   2013  Rodolfo Calzetti                                   *
* License GNU GPL: http://www.rudyz.net/cambusa/license.html                *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
// CARICO LE LIBRERIE
if(!isset($tocambusa))
    $tocambusa="../";
include_once $tocambusa."ryquiver/quiversex.php";
include_once $tocambusa."ryque/ryq_util.php";

try{
    // DETERMINO LA SESSIONID
    if(isset($_POST["sessionid"]))
        $sessionid=ryqEscapize($_POST["sessionid"]);
    else
        $sessionid="";

    // DETERMINO L'AZIONE
    if(isset($_POST["action"]))
        $action=ryqEscapize($_POST["action"]);
    else
        $action="";

    // DETERMINO APP
    if(isset($_POST["app"]))
        $app=ryqEscapize($_POST["app"]);
    else
        $app="";
        
    // DETERMINO ENV
    if(isset($_POST["env"]))
        $env=ryqEscapize($_POST["env"]);
    else
        $env="";
        
    // DETERMINO NUOVA ENV
    if(isset($_POST["envnew"]))
        $envnew=ryqEscapize($_POST["envnew"]);
    else
        $envnew="";
    
    $app=strtolower($app);
    $env=strtolower($env);
    $envnew=strtolower($envnew);

    // DETERMINO DESCRIPTION
    if(isset($_POST["descr"]))
        $descr=ryqEscapize($_POST["descr"]);
    else
        $descr="";

    // INIZIALIZZO LE VARIABILI IN USCITA
    $success=1;
    $description="Operazione effettuata";

    // APRO IL DATABASE
    $maestro=maestro_opendb("ryego");
    if($maestro->conn!==false){

        // CONTROLLO VALIDITA' SESSIONE
        if(ego_validatesession($maestro, $sessionid)==false){
            $success=0;
            $description="Sessione non valida";
        }

        // CONTROLLI DI CORRETTEZZA REPERIMENTO SYSID
        if($success){
            if($app!=""){
                // Determino appid
                $sql="SELECT SYSID FROM EGOAPPLICATIONS WHERE NAME='$app'";
                maestro_query($maestro, $sql, $r);
                if(count($r)==1)
                    $appid=$r[0]["SYSID"];
                else
                    $appid="";
            }
            else{
                $success=0;
                $description="Seleziona una applicazione";
            }
        }
        if($success){
            if($env!=""){
                // Determino envid
                $sql="SELECT SYSID FROM EGOENVIRONS WHERE APPID='$appid' AND NAME='$env'";
                maestro_query($maestro, $sql, $r);
                if(count($r)==1)
                    $envid=$r[0]["SYSID"];
                else
                    $envid="";
            }
            else{
                $success=0;
                if($action=="insert")
                    $description="Ambiente obbligatorio";
                else
                    $description="Seleziona un ambiente";
            }
        }
        if($success){
            if($action=="insert" || $action=="update"){
                if($descr==""){
                    $success=0;
                    $description="Descrizione obbligatoria";
                }
            }
        }
        if($success){
            if($action=="update"){
                if($envnew!=""){
                    if($env!=$envnew){
                        // Determino envid del nuovo valore di env per vedere se esiste gi�
                        $sql="SELECT SYSID FROM EGOENVIRONS WHERE APPID='$appid' AND NAME='$envnew'";
                        maestro_query($maestro, $sql, $r);
                        if(count($r)==1)
                            $envnewid=$r[0]["SYSID"];
                        else
                            $envnewid="";
                    }
                    else{
                        $envnewid="";
                    }
                }
            }
        }
        if($success){
            // BEGIN TRANSACTION
            maestro_begin($maestro);

            switch($action){
                case "insert":
                    if($envid==""){
                        $envid=qv_createsysid($maestro);
                        $sql="INSERT INTO EGOENVIRONS(SYSID,APPID,NAME,DESCRIPTION) VALUES('$envid','$appid','$env','$descr')";
                        maestro_execute($maestro, $sql);
                    }
                    else{
                        $success=0;
                        $description="Nome gi� in uso";
                    }
                    break;
                case "update":
                    if($envnewid==""){
                        if($envid!=""){
                            $sql="UPDATE EGOENVIRONS SET NAME='$envnew',DESCRIPTION='$descr' WHERE SYSID='$envid'";
                            maestro_execute($maestro, $sql);
                        }
                        else{
                            $success=0;
                            $description="Nome non valido";
                        }
                    }
                    else{
                        $success=0;
                        $description="Nome gi� in uso";
                    }
                    break;
                case "delete":
                    maestro_execute($maestro, "DELETE FROM EGOENVIRONS WHERE SYSID='$envid'", false);
                    maestro_execute($maestro, "DELETE FROM EGOENVIRONUSER WHERE ENVIRONID='$envid'", false);
                    maestro_execute($maestro, "DELETE FROM EGOSESSIONS WHERE ENVIRONID='$envid'", false);
                    break;
            }
            if($success){
                // COMMIT TRANSACTION
                maestro_commit($maestro);
            }
            else{
                // ROLLBACK TRANSACTION
                maestro_rollback($maestro);
            }
        }
    }
    else{
        // CONNESSIONE FALLITA
        $success=0;
        $description=$maestro->errdescr;
    }

    // CHIUDO IL DATABASE
    maestro_closedb($maestro);
}
catch(Exception $e){
    $success=0;
    $description=$e->getMessage();
}

// USCITA JSON
$j=array();
$j["success"]=$success;
$j["description"]=htmlentities($description);
print json_encode($j);
?>