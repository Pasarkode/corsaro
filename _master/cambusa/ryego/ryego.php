<?php 
/****************************************************************************
* Name:            ryego.php                                                *
* Project:         Cambusa/ryEgo                                            *
* Version:         1.69                                                     *
* Description:     Central Authentication Service (CAS)                     *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         https://github.com/cambusa                               *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
// CARICO I PERCORSI
if(!isset($tocambusa))
    $tocambusa="../";
include_once $tocambusa."sysconfig.php";
    
// APPLICAZIONE
if(isset($_GET["app"]))
    $appname=$_GET["app"];
elseif(isset($_POST["app"]))
    $appname=$_POST["app"];
else
    $appname="";

// RISOLVO LE IMMAGINI DECORATIVE
$egotitle="Servizio di autenticazione";
$egoimage_header=$url_cambusa."ryego/images/classic-backheader.svg";
$egoimage_logo=$url_cambusa."ryego/images/ego.gif";
$egoimage_footer=$url_cambusa."ryego/images/classic-backfooter.svg";
if(is_file($path_customize."ryego/custdecos.php")){
    include_once $path_customize."ryego/custdecos.php";
}

// CARICO LE LIBRERIE
include_once $path_applications."cacheversion.php";
include_once $tocambusa."ryego/ego_crypt.php";    
include_once $tocambusa."ryquiver/quiversex.php";
include_once $tocambusa."ryque/ryq_util.php";
include_once $tocambusa."phpseclib/Math/BigInteger.php";
include_once $tocambusa."phpseclib/Crypt/RSA.php";

// APRO IL DATABASE
$maestro=maestro_opendb("ryego");

// URL
if(isset($_GET["url"])){
    $returnurl=$_GET["url"];
    $egomethod="GET";
}
elseif(isset($_POST["url"])){
    $returnurl=$_POST["url"];
    $egomethod="POST";
}
else{
    $returnurl="";
    $egomethod="POST";
}

if(isset($_GET["title"]))
    $apptitle=$_GET["title"];
elseif(isset($_POST["title"]))
    $apptitle=$_POST["title"];
else
    $apptitle="";

if($apptitle==""){
    $apptitle=$appname;
}

if($apptitle==""){
    $apptitle="Ego";
}

// GESTIONE AMBIENTE: POSSO CAMBIARLO SENZA PASSARE ESPLICITAMENTE DAL SETUP EGO
if(isset($_GET["env"]))
    $castenv=$_GET["env"];
elseif(isset($_POST["env"]))
    $castenv=$_POST["env"];
else
    $castenv="";
    
// VALIDATORE
$validator="ego";
if($maestro->conn!==false){
    $sql="SELECT VALUE FROM EGOSETTINGS WHERE NAME='validator'";
    maestro_query($maestro, $sql, $r);
    if(count($r)==1){
        if($r[0]["VALUE"]!="")
            $validator=$r[0]["VALUE"];
    }
}

// METODO    
if(isset($_GET["method"]))
    $egomethod=$_GET["method"];
elseif(isset($_POST["method"]))
    $egomethod=$_POST["method"];

// AVVIARE SETUP
if(isset($_GET["set"]))
    $setup=intval($_GET["set"]);
elseif(isset($_POST["set"]))
    $setup=intval($_POST["set"]);
else
    $setup=0;

// POSIZIONAMENTO MASCHERA SETUP
if(isset($_POST["msk"]))
    $msk=$_POST["msk"];
else
    $msk="login";

if(isset($_POST["sessionid"])){
    $sessionid=ryqEscapize($_POST["sessionid"]);
    if($maestro->conn!==false){
        if(!ext_validatesession($sessionid)){
            $sessionid="";
            $msk="login";
        }
    }
    else{
        $sessionid="";
        $msk="login";
    }
}
else{
    $sessionid="";
    $msk="login";
}

if(isset($_POST["userid"]))
    $userid=$_POST["userid"];
else
    $userid="";

if(isset($_POST["aliasid"]))
    $aliasid=$_POST["aliasid"];
else
    $aliasid="";

if(isset($_POST["appid"]))
    $appid=$_POST["appid"];
else
    $appid="";

if(isset($_POST["expiry"]))
    $expiry=intval($_POST["expiry"]);
else
    $expiry=0;

if(isset($_GET["active"]))
    $active=intval($_GET["active"]);
elseif(isset($_POST["active"]))
    $active=intval($_POST["active"]);
else
    $active="settings";

if(isset($_GET["setuponly"]))
    $setuponly=intval($_GET["setuponly"]);
elseif(isset($_POST["setuponly"]))
    $setuponly=intval($_POST["setuponly"]);
else
    $setuponly=0;

$egouser="";
if($appname!="" && isset($_COOKIE['_egouser']))
    $egouser=$_COOKIE['_egouser'];

$egolanguage=$config_defaultlang;
if(isset($_COOKIE['_egolanguage']))
    $egolanguage=$_COOKIE['_egolanguage'];

// PREPARAZIONE CRITTOGRAFIA PER PROTEZIONE PASSWORD
prepareEncrypt($maestro, $publickey);

?><!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
<meta http-equiv="x-ua-compatible" content="ie=edge, chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="description" content="ryEgo - Central Authentication Service" />
<meta name="framework" content="Cambusa <?php print $cambusa_version ?>" />
<meta name="license" content="GNU LGPL v3" />
<meta name="repository" content="https://github.com/cambusa/" />
<title>Ego - Servizio di autenticazione</title>
<link rel='shortcut icon' href='images/favicon.ico' type='image/x-icon'/>
<link type='text/css' href='ryego.css?ver=<?php print $cacheversion ?>' rel='stylesheet' />
    
<style type="text/css">
body{font-family:verdana,sans-serif;font-size:13px;background-color:white;}
table{font-family:verdana,sans-serif;font-size:13px;}
</style>

<link type='text/css' href='../rybox/rybox.css?ver=<?php print $cacheversion ?>' rel='stylesheet' />
<link type='text/css' href='../ryque/ryque.css?ver=<?php print $cacheversion ?>' rel='stylesheet' />

<link type='text/css' href='../jquery/css/jquery.ui.core.css' rel='stylesheet' />
<link type='text/css' href='../jquery/css/jquery.ui.datepicker.css' rel='stylesheet' />
<link type='text/css' href='../jquery/css/jquery.ui.theme.css' rel='stylesheet' />
<link type='text/css' href='../jquery/css/jquery.ui.button.css' rel='stylesheet' />
<link type='text/css' href='../jquery/css/jquery.ui.tabs.css' rel='stylesheet' />

<style>
div.ui-datepicker{font-size:11px;}
</style>

<style>
input,select,a:focus{outline:none;border:none;}
.contextMenu{position:absolute;display:none;}
.contextMenu>ul>li{font-family:verdana;font-size:12px;text-align:left;}
.contextMenu>ul>li>a{color:black;}
.contextMenu>ul>li>a:focus{outline:1px dotted;color:black;}
.contextDisabled>a{color:silver !important;}
</style>
            
<script type='text/javascript' src='../jquery/jquery.js' ></script>
<script type='text/javascript' src='../jquery/jquery.ui.core.js'></script>
<script type='text/javascript' src='../jquery/jquery.ui.datepicker.js' ></script>
<script type='text/javascript' src='../jquery/jquery.ui.contextmenu.js?ver=<?php print $cacheversion ?>' ></script>
<script type='text/javascript' src='../jquery/jquery.ui.widget.js'></script>
<script type='text/javascript' src='../jquery/jquery.ui.button.js'></script>
<script type='text/javascript' src='../rygeneral/rygeneral.js?ver=<?php print $cacheversion ?>' ></script>
<script type='text/javascript' src='../rybox/rybox.js?ver=<?php print $cacheversion ?>' ></script>
<script type='text/javascript' src='../cryptojs/rollups/sha1.js' ></script>
<!-- Moved here from cambusa\jsencrypt\bin to avoid the blocking IIS 7 policies -->
<script type='text/javascript' src='../rygeneral/jsencrypt.js' ></script>
<script type='text/javascript' src='../jquery/jquery.ui.mouse.js'></script>
<script type='text/javascript' src='../jquery/jquery.ui.draggable.js'></script>
<script type='text/javascript' src='../jquery/jquery.ui.mousewheel.js'></script>
<script type='text/javascript' src='../jquery/jquery.ui.tabs.js'></script>
<script type='text/javascript' src='../jquery/jquery.cookie.js'></script>
<script type='text/javascript' src='../ryque/ryque.js?ver=<?php print $cacheversion ?>' ></script>

<script>
_sessioninfo.sessionid="<?php print $sessionid ?>";
_systeminfo.relative.root="<?php print $relative_base ?>";
_systeminfo.relative.apps=_systeminfo.relative.root+"apps/";
_systeminfo.relative.cambusa=_systeminfo.relative.root+"cambusa/";
_systeminfo.relative.customize=_systeminfo.relative.root+"customize/";
var htimer="";
var _publickey="<?php print strtr($publickey, array("\n" => "[n]", "\r" => "[r]")); ?>";
_publickey=_publickey.replace(/\[n\]/g, "\n").replace(/\[r\]/g, "\r");
var _returnURL="<?php print $returnurl ?>";
var _egomethod="<?php  print $egomethod ?>";
var _setuponly=<?php  print $setuponly ?>;
var _appname="<?php  print $appname ?>";
var _castenv="<?php  print $castenv ?>";
var _validator="<?php  print $validator ?>";
var _egocontext="default";
function encryptString(s){
    var e=new JSEncrypt();
    s=CryptoJS.SHA1(s);
    e.setPublicKey(_publickey);
    var r=e.encrypt( s.toString() );
    return r;
}
function bareString(s){
    var e=new JSEncrypt();
    e.setPublicKey(_publickey);
    var r=e.encrypt( s.toString() );
    return r;
}
function syswaiting(){
    if(htimer!=""){
        clearInterval(htimer);
        htimer="";
    }
	$("#messbar").html("<img src='images/waiting.gif' style='border:1px solid silver;'>").show();
}
function sysmessage(t,s){
    if(htimer!=""){
        clearInterval(htimer);
        htimer="";
    }
	var c="red";
    var m=6000;
	if(s==1){
		c="green";
        m=4000;
    }
	$("#messbar").html(t).css({color:c}).show();
	htimer=setTimeout(sysmessagehide, m);
}
function sysmessagehide(){
    htimer="";
	$("#messbar").html("").hide("slow");
}
function logout(){
    if(_sessioninfo.sessionid!=""){
        $("body").html("<br/><br/><br/><br/><br/><br/><img src='images/waiting.gif' style='border:1px solid black;'>");
        setTimeout(
            function(){
                $.post("ego_logout.php", {sessionid:_sessioninfo.sessionid}, function(){
                    _sessioninfo.sessionid="";
                    if(_returnURL!="")
                        location.replace(_returnURL);
                    else if(_setuponly)
                        location.replace("ryego.php?app="+_appname+"&setuponly=1");
                    else
                        location.replace("ryego.php");
                });
            }, 100
        );
    }
}
function supportsCookies(){
    $.cookie("EGOTEST", "DUMMY");
    return ($.cookie("EGOTEST")=="DUMMY");
}
function removePrivacyCookie(){
    $("#filibuster-privacycookie").remove();
    $.cookie("EGOCOOKIE", 1, { expires : 10000 });
}
</script>

<?php 
if($msk=="login"){
    include("egoform_login.php"); 
} 
elseif($msk=="setup"){ 
    if($appname==""){ // Setup Ego
        include("egoform_setupego.php"); 
    }
    else{   // Setup Applicazione
        include("egoform_setupapp.php"); 
    }
} 
?>

</head>

<body class='classicBody' style='overflow:hidden;' spellcheck='false'>

<!-- MARGINE SUPERIORE -->
<div class='classicTopMargin'>&nbsp;</div>

<!-- INIZIO AREA FOGLIO CENTRATA -->
<div class='classicSheet'>

<!-- MENU' SUPERIORE -->
<div class='classicBackImage' style='top:13px;height:90px;background-image:url(<?php print $egoimage_header; ?>);'>&nbsp;</div>
<?php if($sessionid!=""){ ?>
<div class='classicTopMenu'><span class='classicMiniAnchor' onclick='egoterminate(true)'>Logout</span>&nbsp;&nbsp;</div>
<?php } ?>

<!-- OMBRA SUPERIORE -->
<div class='classicSkinTop'>&nbsp;</div>

<!-- INIZIO OMBRE LATERALI -->
<div class='classicSkinLeft'>
<div class='classicSkinRight'>

<!-- INIZIO TABELLA DI CONTENIMENTO -->
<table class='classicTable' cellspacing='0' cellpadding='0'>

<!-- INIZIO INTESTAZIONE -->
<tr>
<td colspan='8' valign='top'>

<div class='classicSkip10'>&nbsp;</div>
<div style='position:relative;font-size:16px;height:30px;text-align:center;white-space:nowrap;color:black;'><span id="egotitle"><?php print $egotitle ?></span></div>
<div class='classicSkip10'>&nbsp;</div>
<div class='classicHR'>&nbsp;</div>
<div class='classicSkip20'>&nbsp;</div>

<!-- FINE INTESTAZIONE -->
</td>
</tr>

<!-- INIZIO CELLE DEI CONTENUTI -->
<tr>

<!-- INIZIO AREA LATERALE SINISTRA -->
<td valign='top'>
<div class='classicLateralLeft'>

<!-- INIZIO CONTENUTI DI SINISTRA -->


<!-- FINE CONTENUTI DI SINISTRA -->

<!-- FINE AREA LATERALE SINISTRA -->
</div>
</td>

<!-- Celle separatrici sx/dx -->
<td class='classicVertSkip'>&nbsp;</td>
<td class='classicVertSkip'>&nbsp;</td>

<!-- INIZIO AREA CENTRALE -->
<td valign='top'>


<!-- INIZIO BOX NAVIGAZIONE -->
<!-- FINE BOX NAVIGAZIONE -->

<!-- INIZIO CONTENUTI -->
<table cellspacing='0' cellpadding='0'><tr><td>
<div class='classicContainerOuter'>
<div class='classicContainerInner'>

<?php 
if($msk=="login"){ 
    $posx=245;
    include("egoform_loginbody.php"); 
}
elseif($msk=="setup"){ 
    if($appname==""){ // Setup Ego
        $posx=395;
        include("egoform_setupbodyego.php"); 
    }
    else{ // Setup Applicazione
        $posx=295;
        include("egoform_setupbodyapp.php"); 
    }
} 
?>

<div id="messbar" style="display:none;position:absolute;left:10px;top:<?php print $posx ?>px;"></div>

<!-- FINE CONTENUTI -->
</div>
</div>
</td></tr></table>




<div class='classicSkip20'>&nbsp;</div>

<!-- FINE AREA CENTRALE -->
</td>

<!-- Celle separatrici sx/dx -->
<td class='classicVertSkip'>&nbsp;</td>
<td class='classicVertSkipRow'>&nbsp;</td>

<!-- INIZIO AREA LATERALE DESTRA -->
<td valign='top'>
<div class='classicLateralRight'>

<!-- INIZIO CONTENUTI DI DESTRA -->

<img src='<?php print $egoimage_logo; ?>' height='60px' border='0'>
<div id="titlename" class="classicAppName"><?php print $apptitle ?></div>

<div class='classicSkip10'>&nbsp;</div>
<div class='classicSeparator'>&nbsp;</div>

<div class='classicSkip4'>&nbsp;</div>

<?php 
if($msk=="setup"){ 
    if($appname!=""){
?>
&nbsp;<a id="side_settings" href="#" onclick="activation('settings')">Opzioni</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_changepassword" href="#" onclick="activation('changepassword')">Cambio password</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_deactivation" href="#" onclick="activation('deactivation')">Disattivazione</a><div class='classicSkip4'>&nbsp;</div>
<?php 
    }
    else{
?>
&nbsp;<a id="side_settings" href="#" onclick="activation('settings')">Opzioni</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_users" href="#" onclick="activation('users')">Utenti</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_applications" href="#" onclick="activation('applications')">Applicazioni</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_languages" href="#" onclick="activation('languages')">Lingue</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_sessions" href="#" onclick="activation('sessions')">Sessioni</a><div class='classicSkip4'>&nbsp;</div>
&nbsp;<a id="side_changepassword" href="#" onclick="activation('changepassword')">Cambio password</a><div class='classicSkip4'>&nbsp;</div>
<?php 
    }
} 
?>

<!-- FINE CONTENUTI DI DESTRA -->

<!-- FINE AREA LATERALE DESTRA -->
</div>
</td>

<!-- FINE CELLE DEI CONTENUTI -->
</tr>

<!-- INIZIO PIEDE -->
<tr>
<td colspan='8' valign='top'>

<br/>

<!-- FINE PIEDE -->
</td>
</tr>

<!-- FINE TABELLA DI CONTENIMENTO -->
</table>

<!-- MENU' INFERIORE -->
<!--
<div class='classicSkip20'>&nbsp;</div>
-->
<div style='position:relative;'>
<div class='classicBackImage' style='top:-35px;height:35px;background-image:url(<?php print $egoimage_footer; ?>);'>&nbsp;</div>
<!--
<div class='classicBottomMenu'><a class='classicMiniAnchor' href='../license.html' target='_blank'>Framework Cambusa</a>&nbsp;&nbsp;</div>
-->
</div>

<!-- FINE OMBRE LATERALI -->
</div>
</div>

<!-- OMBRA INFERIORE -->
<div class='classicSkinBottom'>&nbsp;</div>

<!-- FINE AREA FOGLIO CENTRATA -->
</div>
<img src='images/waiting.gif' style='display:none;'>
</body>
</html>
<?php 
// CHIUDO IL DATABASE
maestro_closedb($maestro);
?>