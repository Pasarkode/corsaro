<?php 
/****************************************************************************
* Name:            solveroot.php                                            *
* Project:         Cambusa                                                  *
* Version:         1.69                                                     *
* Description:     Cambusa - Solve URL/PATH                                 *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         https://github.com/cambusa                               *
*                  postmaster@rudyz.net                                     *
****************************************************************************/

function installationPATH(){
    $s=realpath(dirname(__FILE__)."/..");
    $s=str_replace("\\", "/", $s);
    $s.="/";
    return $s;
}

function installationURL(){
    $s=currPageURL();
    $p=strrpos($s, "/cambusa");
    if($p!==false){
        $s=substr($s, 0, $p);
    }
    else{
        $p=strrpos($s, "/apps");
        if($p!==false){
            $s=substr($s, 0, $p);
        }
        else{
            $p=strrpos($s, "/customize");
            if($p!==false){
                $s=substr($s, 0, $p);
            }
        }
    }
    $s.="/";
    return $s;
}

function currPageURL(){
    if(isset($_SERVER["REQUEST_URI"])){
        $pageURL='http';
        if(isset($_SERVER["HTTPS"])){
            if($_SERVER["HTTPS"]=="on"){
                $pageURL.="s";
            }
        }
        $pageURL.="://";
        if($_SERVER["SERVER_PORT"]!="80"){
            $pageURL.=$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        }
        else{
            $pageURL.=$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }
    }
    else{
        $pageURL="";
    }
    return $pageURL;
}
?>