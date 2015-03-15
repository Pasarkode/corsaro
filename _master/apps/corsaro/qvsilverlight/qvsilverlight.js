/****************************************************************************
* Name:            qvsilverlight.js                                         *
* Project:         Corsaro                                                  *
* Version:         1.69                                                     *
* Description:     Arrows Oriented Modeling                                 *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
function class_qvsilverlight(settings,missing){
    var formid=RYWINZ.addform(this);
    var sl_source="";
    var sl_width=730;
    var sl_height=400;
    
    if(_isset(settings["width"])){
        sl_width=_getinteger(settings["width"]);
    }
    
    if(_isset(settings["height"])){
        sl_height=_getinteger(settings["height"]);
    }
    
    // DETERMINO IL PERCORSO DELL'APPLICAZIONE
    if(_isset(settings["source"])){
        sl_source=settings["source"];
        sl_source=sl_source.replace(/@cambusa\//gi,_cambusaURL);
        sl_source=sl_source.replace(/@apps\//gi,_appsURL);
        sl_source=sl_source.replace(/@customize\//gi,_customizeURL);
        sl_source=_cambusaURL+"rygeneral/sl_wrapper.php?source="+sl_source+"&formid="+formid+"&sessionid="+_sessioninfo.sessionid+"&env="+_sessioninfo.environ+"&userid="+_sessioninfo.userid+"&root="+_baseURL;
        if(window.console&&_sessioninfo.debugmode){console.log("Silverlight source: "+sl_source)}
        $("#"+formid+"iframe iframe").attr({width:sl_width}).attr({height:sl_height}).attr("src", sl_source);
    }
}