<?php 
/****************************************************************************
* Name:            babel_action.php                                         *
* Project:         Cambusa/ryBabel                                          *
* Version:         1.69                                                     *
* Description:     Language localization                                    *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         https://github.com/cambusa                               *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
if(!isset($tocambusa))
    $tocambusa="../";
include_once $tocambusa."ryquiver/quiversex.php";
include_once $tocambusa."rygeneral/writelog.php";

set_time_limit(0);

try{
    if(isset($_POST["sessionid"]))
        $sessionid=$_POST["sessionid"];
    else
        $sessionid="";

    if(isset($_POST["action"]))
        $action=$_POST["action"];
    else
        $action="";
        
    if(isset($_POST["default"]))
        $default=$_POST["default"];
    else
        $default="";
        
    if(isset($_POST["SYSID"]))
        $SYSID=ryqEscapize($_POST["SYSID"]);
    else
        $SYSID="";
        
    if(isset($_POST["NAME"]))
        $NAME=ryqEscapize(strtoupper($_POST["NAME"]));
    else
        $NAME="";
        
    if(isset($_POST["languages"]))
        $languages=$_POST["languages"];
    else
        $languages=array();
    
    if(isset($_POST["captions"]))
        $captions=$_POST["captions"];
    else
        $captions=array();
    
    $jret=array();
    $jret["success"]=1;
    $jret["message"]="OK";

    $maestro=array();
    
    if($action=="select")
        $valid=true;
    else
        $valid=ext_validatesession($sessionid);
    
    if($valid){
        // APRO I DATABASE E IMPOSTO IL VALORE DI DEFAULT
        foreach($languages as $lang){
            $maestro[$lang]=maestro_opendb($lang);
            $jret[$lang]="";
        }
        
        switch($action){
        case "select":
            maestro_query($maestro[$default],"SELECT NAME,CAPTION FROM BABELITEMS WHERE SYSID='$SYSID'", $r);
            if(count($r)==1){
                $REGNAME=strtoupper($r[0]["NAME"]);
                foreach($languages as $lang){
                    if($lang==$default){
                        $jret[$lang]=$r[0]["CAPTION"];
                    }
                    else{
                        maestro_query($maestro[$lang],"SELECT CAPTION FROM BABELITEMS WHERE [:UPPER(NAME)]='$REGNAME'", $l);
                        if(count($l)==1){
                            $jret[$lang]=$l[0]["CAPTION"];
                        }
                    }
                }
                $jret["NAME"]=$REGNAME;
            }
            break;

        case "new":
            $SYSID=qv_createsysid($maestro[$default]);
            foreach($languages as $lang){
                if($lang==$default){
                    $ID=$SYSID;
                    $jret["SYSID"]=$SYSID;
                }
                else{
                    $ID=qv_createsysid($maestro[$lang]);
                }
                $sql="INSERT INTO BABELITEMS(SYSID,NAME,CAPTION,DESCRIPTION) VALUES('$ID','$SYSID','','')";
                maestro_execute($maestro[$lang], $sql);
            }
            break;
        
        case "update":
            $sql="SELECT NAME FROM BABELITEMS WHERE SYSID='$SYSID'";
            maestro_query($maestro[$default], $sql, $r);
            if(count($r)==1){
                $REGNAME=strtoupper($r[0]["NAME"]);
                foreach($languages as $i => $lang){
                    $cap=ryqEscapize($captions[$i]);
                    if($lang==$default){
                        $sql="UPDATE BABELITEMS SET NAME='$NAME', CAPTION='$cap' WHERE [:UPPER(NAME)]='$REGNAME'";
                        maestro_execute($maestro[$lang], $sql);
                    }
                    else{
                        $sql="SELECT SYSID FROM BABELITEMS WHERE [:UPPER(NAME)]='$REGNAME'";
                        maestro_query($maestro[$lang], $sql, $r);
                        if(count($r)==1){
                            $sql="UPDATE BABELITEMS SET NAME='$NAME', CAPTION='$cap' WHERE [:UPPER(NAME)]='$REGNAME'";
                            maestro_execute($maestro[$lang], $sql);
                        }
                        else{
                            $ID=qv_createsysid($maestro[$lang]);
                            $sql="INSERT INTO BABELITEMS(SYSID,NAME,CAPTION,DESCRIPTION) VALUES('$ID','$REGNAME','$cap','')";
                            maestro_execute($maestro[$lang], $sql);
                        }
                    }
                }
            }
            break;

        case "delete":
            maestro_query($maestro[$default],"SELECT NAME FROM BABELITEMS WHERE SYSID='$SYSID'", $r);
            if(count($r)==1){
                $REGNAME=strtoupper($r[0]["NAME"]);
                foreach($languages as $lang){
                    $sql="DELETE FROM BABELITEMS WHERE [:UPPER(NAME)]='$REGNAME'";
                    maestro_execute($maestro[$lang], $sql);
                }
            }
            break;
        }

        // CHIUDO I DATABASE
        foreach($maestro as $m){
            maestro_closedb($m);
        }
    }    
    else{
        $jret=array();
        $jret["success"]=0;
        $jret["message"]="Invalid session";
    }

    array_walk_recursive($jret, "escapize");
    print json_encode($jret);
}
catch(Exception $e){
    $jret=array();
    $jret["success"]=0;
    $jret["message"]=$e->getMessage();
    array_walk_recursive($jret, "escapize");
    print json_encode($jret);
}
function escapize(&$sql){
    $sql=utf8_decode(utf8_encode($sql));
}
?>