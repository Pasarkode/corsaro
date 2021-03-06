<?php 
/****************************************************************************
* Name:            ryq_search.php                                           *
* Project:         Cambusa/ryQue                                            *
* Version:         1.69                                                     *
* Description:     Lightweight access to databases                          *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         https://github.com/cambusa                               *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
if(!isset($tocambusa))
    $tocambusa="../";
include_once $tocambusa."rymaestro/maestro_execlib.php";

$tr=Array();
$tr["\'"]="'";
$tr["\\\""]="\"";
$tr["\\\\"]="\\";

$reqid=$_POST['reqid'];
if(isset($_POST['criteria']))
    $criteria=$_POST['criteria'];
else
    $criteria=array();

// REPERIMENTO AMBIENTE
$env_name=file_get_contents("requests/".$reqid.".req");

// REPERIMENTO FILE INDICE
$indexes=file_get_contents("requests/".$reqid.".ndx");

// REPERIMENTO TABELLA
$from=file_get_contents("requests/".$reqid.".tbl");

// APERTURA DATABASE
$maestro=maestro_opendb($env_name, false);
$lenid=$maestro->lenid+1;

$where="";
$in="'" . str_replace("|", "','", $indexes) . "'";
$indexes="|".$indexes."|";

if(isset($criteria["where"])){
    $where="(" . strtr($criteria["where"], $tr) . ")";
    $where.=" AND SYSID IN (" . $in . ")";
}

if(isset($criteria["gauge"])){
    $gauge=floatval($criteria["gauge"]);
    $name=$criteria["name"];

    include_once $tocambusa."ryque/ryq_gauge.php";
    
    $values=array();
    $refs=array();
    
    if($where==""){
        $where="SYSID IN (" . $in . ")";
    }

    maestro_query($maestro, "SELECT SYSID, $name AS _NAME FROM $from WHERE $where", $r);
    for($i=0; $i<count($r); $i++){
        $SYSID=$r[$i]["SYSID"];
        $p=strpos($indexes, "|".$SYSID."|");
        $refs[]=($p/$lenid)+1;
        $values[]=floatval($r[$i]["_NAME"]);
    }
    unset($r);
    
    $s=gaugesearch($reqid, array("gauge" => $gauge), $values, $refs);
}
elseif(count($criteria)==0){
    if(is_file("requests/".$reqid.".sts")){
        include_once $tocambusa."ryque/ryq_gauge.php";
        $s=gaugesearch($reqid);
    }
    else{
        $s=array();
    }
}
else{
    if($where==""){
        $where="SYSID IN (" . $in . ")";
    }

    $s=array();

    // QUERY FINALE DI REPERIMENTO SYSID
    maestro_query($maestro, "SELECT SYSID FROM $from WHERE $where", $r);

    for($i=0; $i<count($r); $i++){
        $SYSID=$r[$i]["SYSID"];
        $p=strpos($indexes, "|".$SYSID."|");
        $s[]=($p/$lenid)+1;
    }
}

// CHIUSURA DATABASE
maestro_closedb($maestro);

sort($s);
print json_encode($s);
?>