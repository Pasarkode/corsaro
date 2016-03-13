<?php 
/****************************************************************************
* Name:            babel_lib.php                                            *
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
include_once $tocambusa."rymaestro/maestro_execlib.php";

function babeldecode($lang, $codes){
    global $config_selflearning;
    $r=array();
    try{
        if($lang!="" && $codes!=""){

            // APERTURA DATABASE
            $maestro=maestro_opendb($lang);
            
            if($maestro->conn!==false){
                $elenco=explode("|",strtoupper($codes));
                $elencoin="'".str_replace("|", "','", strtoupper($codes))."'";
                // INIZIALIZZO LA RISPOSTA
                foreach($elenco as $code){
                    $r[$code]="";
                }
                $sql="SELECT NAME,CAPTION FROM BABELITEMS WHERE [:UPPER(NAME)] IN ($elencoin)";
                maestro_query($maestro, $sql, $s);
                foreach($s as $row){
                    if(($i=array_search($row["NAME"], $elenco))!==false){
                        $r[ $elenco[$i] ]=$row["CAPTION"];
                    }
                }
            }
            
            // CHIUSURA DATABASE
            maestro_closedb($maestro);
            
            if($config_selflearning!=""){
                $self=array();
                // ELENCO GLI INESISTENTI
                foreach($elenco as $k){
                    if(!isset($r[$k]))
                        $self[]=$k;
                }
                if(count($self)>0){
                    $r["___SELFLEARNING"]=implode("|", $self);
                }
            }
        }
    }
    catch(Exception $e){}
    return json_encode($r);
}
?>