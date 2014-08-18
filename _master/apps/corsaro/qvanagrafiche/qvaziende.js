/****************************************************************************
* Name:            qvaziende.js                                             *
* Project:         Corsaro                                                  *
* Version:         1.00                                                     *
* Description:     Arrows Oriented Modeling                                 *
* Copyright (C):   2013  Rodolfo Calzetti                                   *
* License GNU GPL: http://www.rudyz.net/apps/corsaro/license.html           *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
function class_qvaziende(settings,missing){
    var formid=RYWINZ.addform(this);
    winzProgress(formid);

    var currsysid="";
    var currtypologyid=RYQUE.formatid("0AZIENDE0000");
    var context="";
    var prefix="#"+formid;
    var flagopen=false;
    var flagsuspend=false;
    var loadedsysid="";
    
    // DEFINIZIONE TAB SELEZIONE
    
    // GRID DI SELEZIONE
    var objgridsel=$(prefix+"gridsel").ryque({
        left:20,
        top:80,
        width:400,
        height:300,
        numbered:true,
        checkable:true,
        environ:_sessioninfo.environ,
        from:"QWOBJECTS",
        orderby:"DESCRIPTION",
        columns:[
            {id:"DESCRIPTION",caption:"Descrizione",width:200}
        ],
        changerow:function(o,i){
            if(i>0){
                o.solveid(i);
            }
            else{
                if(currsysid!=""){
                    objtabs.enabled(2,false);
                    objtabs.enabled(3,false);
                }
                currsysid="";
                oper_print.enabled(o.isselected());
                oper_delete.enabled(o.isselected());
            }
            context="";
        },
        selchange:function(o, i){
            oper_print.enabled(o.isselected());
            oper_delete.enabled(o.isselected());
        },
        solveid:function(o,d){
            oper_print.enabled(1);
            oper_delete.enabled(1);
            if(currsysid==""){
                currsysid=d;
                objtabs.enabled(2,true);
                objtabs.enabled(3,true);
            }
            else{
                currsysid=d;
            }
            if(flagopen){
                flagopen=false;
                objtabs.currtab(2);
            }
        },
        enter:function(){
            objtabs.currtab(2);
        }
    });
    var offsety=80;
    var lbf_search=$(prefix+"lbf_search").rylabel({left:430, top:offsety, caption:"Ricerca"});offsety+=20;
    var txf_search=$(prefix+"txf_search").rytext({left:430, top:offsety, width:300, 
        assigned:function(){
            oper_refresh.engage()
        }
    });offsety+=30;
    
    var oper_refresh=$(prefix+"oper_refresh").rylabel({
        left:430,
        top:offsety,
        caption:"Aggiorna",
        button:true,
        click:function(o, done){
            var q="";
            var t=_likeescapize(txf_search.value());

            q="TYPOLOGYID='"+currtypologyid+"'";
            if(t!="")
                q+=" AND ( [:UPPER(DESCRIPTION)] LIKE '%[=DESCRIPTION]%' OR [:UPPER(TAG)] LIKE '%[=TAG]%' )";

            objgridsel.where(q);
            objgridsel.query({
                args:{
                    "DESCRIPTION":t,
                    "TAG":t
                },
                ready:function(){
                    if(done!=missing){done()}
                }
            });
        }
    });
    
    var oper_new=$(prefix+"oper_new").rylabel({
        left:430,
        top:240,
        caption:"Nuovo",
        button:true,
        click:function(o){
            winzProgress(formid);
            var data = new Object();
            data["DESCRIPTION"]="(nuova azienda)";
            data["TYPOLOGYID"]=currtypologyid;
            $.post(_cambusaURL+"ryquiver/quiver.php", 
                {
                    "sessionid":_sessionid,
                    "env":_sessioninfo.environ,
                    "function":"objects_insert",
                    "data":data
                }, 
                function(d){
                    try{
                        var v=$.parseJSON(d);
                        if(v.success>0){
                            var newid=v.SYSID;
                            flagopen=true;
                            objgridsel.splice(0, 0, newid);
                            /*
                            objgridsel.query({
                                where:"SYSID='"+newid+"'",
                                ready:function(){
                                    flagopen=true;
                                    objgridsel.index(1);
                                }
                            });
                            */
                        }
                        winzTimeoutMess(formid, v.success, v.message);
                    }
                    catch(e){
                        winzClearMess(formid);
                        alert(d);
                    }
                }
            );
        }
    });
    
    var oper_print=$(prefix+"oper_print").rylabel({
        left:430,
        top:290,
        caption:"Stampa selezione",
        button:true,
        click:function(o){
            qv_printselected(formid, objgridsel, "rep_objects.php")
        }
    });

    var oper_delete=$(prefix+"oper_delete").rylabel({
        left:430,
        top:340,
        caption:"Elimina selezione",
        button:true,
        click:function(o){
            qv_bulkdelete(formid, objgridsel, "objects");
        }
    });

    // DEFINIZIONE TAB CONTESTO
    var offsety=60;
    $(prefix+"LB_DESCRIPTION").rylabel({left:20, top:offsety, caption:"Rag. Sociale"});
    var txdescr=$(prefix+"DESCRIPTION").rytext({left:120, top:offsety, width:300, maxlen:100, datum:"C", tag:"DESCRIPTION"});
    offsety+=30;
    
    $(prefix+"LB_INDIRIZZO").rylabel({left:20, top:offsety, caption:"Indirizzo"});
    $(prefix+"INDIRIZZO").rytext({left:120, top:offsety, width:240, maxlen:100, datum:"C", tag:"INDIRIZZO"});
    $(prefix+"CIVICO").rytext({left:380, top:offsety, width:40, datum:"C", tag:"CIVICO"});
    offsety+=30;
    
    $(prefix+"LB_CAP").rylabel({left:20, top:offsety, caption:"C.A.P."});
    $(prefix+"CAP").rytext({left:120, top:offsety, width:60, maxlen:5, datum:"C", tag:"CAP"});
    
    $(prefix+"LB_CITTA").rylabel({left:200, top:offsety, caption:"Citt&agrave;"});
    $(prefix+"CITTA").rytext({left:240, top:offsety, width:250, maxlen:50, datum:"C", tag:"CITTA"});
    
    $(prefix+"LB_PROVINCIA").rylabel({left:520, top:offsety, caption:"Provincia"});
    $(prefix+"PROVINCIA").rytext({left:590, top:offsety, width:40, maxlen:30, datum:"C", tag:"PROVINCIA"});
    
    $(prefix+"oper_cerca").rylabel({
        left:640,
        top:offsety,
        width:70,
        caption:"Cerca...",
        button:true,
        click:function(o){
            qv_geography(formid,
                {
                    "type":"comuni",
                    "onselect":function(d){
                        globalobjs[formid+"CAP"].value(d["CAP"], true);
                        globalobjs[formid+"CITTA"].value(d["DESCRIPTION"], true);
                        globalobjs[formid+"PROVINCIA"].value(d["SIGLA"], true);
                    }
                }
            );
        }
    });

    offsety+=30;
    $(prefix+"LB_NAZIONE").rylabel({left:20, top:offsety, caption:"Nazione"});
    $(prefix+"NAZIONE").rytext({left:120, top:offsety, width:200, datum:"C", tag:"NAZIONE"});
    offsety+=30;

    $(prefix+"LB_CODFISC").rylabel({left:20, top:offsety, caption:"Cod. Fisc."});
    $(prefix+"CODFISC").rytext({left:120, top:offsety, width:300, datum:"C", tag:"CODFISC"});
    offsety+=30;
    
    $(prefix+"LB_PIVA").rylabel({left:20, top:offsety, caption:"Partita IVA"});
    $(prefix+"PIVA").rytext({left:120, top:offsety, width:300, maxlen:30, datum:"C", tag:"PIVA"});
    offsety+=30;
    
    $(prefix+"LB_ATECO").rylabel({left:20, top:offsety, caption:"AT.ECO."});
    $(prefix+"ATECO").rytext({left:120, top:offsety, width:300, maxlen:30, datum:"C", tag:"ATECO"});
    offsety+=30;
    
    $(prefix+"LB_CCIAA").rylabel({left:20, top:offsety, caption:"C.C.I.A.A."});
    $(prefix+"CCIAA").rytext({left:120, top:offsety, width:300, datum:"C", tag:"CCIAA"});
    offsety+=30;

    $(prefix+"LB_BEGINTIME").rylabel({left:20, top:offsety, caption:"Iscrizione"});
    $(prefix+"BEGINTIME").rydate({left:120, top:offsety, datum:"C", tag:"BEGINTIME"});
    $(prefix+"LB_ENDTIME").rylabel({left:270, top:offsety, caption:"Cessazione"});
    $(prefix+"ENDTIME").rydate({left:350, top:offsety, defaultvalue:"99991231", datum:"C", tag:"ENDTIME"});
    offsety+=30;
    
    $(prefix+"LB_TELEFONO").rylabel({left:20, top:offsety, caption:"Telefono"});
    $(prefix+"TELEFONO").rytext({left:120, top:offsety, width:200, maxlen:30, datum:"C", tag:"TELEFONO"});
    $(prefix+"LB_FAX").rylabel({left:385, top:offsety, caption:"Fax"});
    $(prefix+"FAX").rytext({left:430, top:offsety, width:200, maxlen:30, datum:"C", tag:"FAX"});
    offsety+=30;
    
    $(prefix+"LB_EMAIL").rylabel({left:20, top:offsety, caption:"Email"});
    $(prefix+"EMAIL").rytext({left:120, top:offsety, width:200, maxlen:50, datum:"C", tag:"EMAIL"});
    offsety+=30;
 
    var savey=offsety;
 
    $(prefix+"LB_CONTODEFAULTID").rylabel({left:20, top:offsety, caption:"Conto predef."});
    $(prefix+"CONTODEFAULTID").ryhelper({
        left:120, top:offsety, width:300, datum:"C", tag:"CONTODEFAULTID", formid:formid, table:"QW_CONTI", title:"Scelta conto predefinito",
        open:function(o){
            o.where("SYSID IN (SELECT SYSID FROM QW_CONTI WHERE TITOLAREID='"+currsysid+"')");
        }
    });offsety+=30;
    
    $(prefix+"LB_TITOLAREID").rylabel({left:20, top:offsety, caption:"Titolare"});
    $(prefix+"TITOLAREID").ryhelper({
        left:120, top:offsety, width:300, datum:"C", tag:"TITOLAREID", formid:formid, table:"QW_PERSONE", title:"Titolare",
        open:function(o){
            o.where("");
        }
    });offsety+=30;
    
    $(prefix+"LB_TAG").rylabel({left:20, top:offsety, caption:"Marche"});
    $(prefix+"TAG").rytext({left:120, top:offsety, width:300, datum:"C", tag:"TAG"});

    offsety=savey;
    
    $(prefix+"LB_BANCA").rylabel({left:540, top:offsety, caption:"Banca"});
    $(prefix+"BANCA").rycheck({left:610, top:offsety, datum:"C", tag:"BANCA"});
    offsety+=30;

    $(prefix+"LB_CLIENTE").rylabel({left:540, top:offsety, caption:"Cliente"});
    $(prefix+"CLIENTE").rycheck({left:610, top:offsety, datum:"C", tag:"CLIENTE"});
    offsety+=30;

    $(prefix+"LB_FORNITORE").rylabel({left:540, top:offsety, caption:"Fornitore"});
    $(prefix+"FORNITORE").rycheck({left:610, top:offsety, datum:"C", tag:"FORNITORE"});
    offsety+=30;
    
    $(prefix+"LB_REGISTRY").rylabel({left:20, top:offsety, caption:"Note"});offsety+=30;
    $(prefix+"REGISTRY").ryedit({left:20, top:offsety, width:700, height:400, datum:"C", tag:"REGISTRY"});
    
    var oper_contextengage=$(prefix+"oper_contextengage").rylabel({
        left:680,
        top:60,
        caption:"Salva",
        button:true,
        click:function(o, done){
            winzProgress(formid);
            context=txdescr.value();
            var data=qv_mask2object(formid, "C", currsysid);
            $.post(_cambusaURL+"ryquiver/quiver.php", 
                {
                    "sessionid":_sessionid,
                    "env":_sessioninfo.environ,
                    "function":"objects_update",
                    "data":data
                }, 
                function(d){
                    try{
                        var v=$.parseJSON(d);
                        if(v.success>0){ RYWINZ.modified(formid, 0) }
                        objgridsel.dataload();
                        winzTimeoutMess(formid, v.success, v.message);
                    }
                    catch(e){
                        winzClearMess(formid);
                        alert(d);
                    }
                    if(done!=missing){done()}
                }
            );
        }
    });

    // DEFINIZIONE TAB DOCUMENTI
    var filemanager=new qv_filemanager(this, formid, "QVOBJECTS", "QW_AZIENDE");

    // INIZIALIZZO I TABS
    var objtabs=$( prefix+"tabs" ).rytabs({
        top:10,position:"relative",
        tabs:[
            {title:"Selezione"},
            {title:"Contesto"},
            {title:"Documenti"}
        ],
        select:function(i,p){
            if(p==2){
                // PROVENGO DAI DATI
                flagsuspend=qv_changemanagement(formid, objtabs, oper_contextengage, {
                    abandon:function(){
                        loadedsysid="";
                    }
                });
            }
            if(i==1){
                loadedsysid="";
            }
            else if(i==2){
                if(currsysid==loadedsysid){
                    flagsuspend=true;
                }
            }
            if(!flagsuspend){
                switch(i){
                case 1:
                    objgridsel.dataload();
                    break;
                case 2:
                    // CARICAMENTO DEL CONTESTO
                    if(window.console&&_sessioninfo.debugmode){console.log("Caricamento contesto: "+currsysid)}
                    qv_maskclear(formid, "C");
                    RYQUE.query({
                        sql:"SELECT * FROM QW_AZIENDE WHERE SYSID='"+currsysid+"'",
                        ready:function(v){
                            qv_object2mask(formid, "C", v[0]);
                            context=v[0]["DESCRIPTION"];
                            loadedsysid=currsysid;
                            castFocus(prefix+"DESCRIPTION");
                        }
                    });
                    break;
                case 3:
                    // CARICAMENTO DOCUMENTI
                    filemanager.initialize(currsysid, "Contesto: "+context, currtypologyid);
                    qv_contextmanagement(context, {sysid:currsysid, table:"QVOBJECTS", select:"DESCRIPTION", formula:"[=DESCRIPTION]",
                        done:function(d){
                            context=d;
                            filemanager.caption("Contesto: "+context);
                        }
                    });
                }
            }
            flagsuspend=false;
        }
    });
    objtabs.currtab(1);
    objtabs.enabled(2,false);
    objtabs.enabled(3,false);
    
    // INIZIALIZZAZIONE FORM
    RYBOX.localize(_sessioninfo.language, formid,
        function(){
            setTimeout( 
                function(){ 
                    oper_refresh.engage(
                        function(){
                            winzClearMess(formid);
                            txf_search.focus();
                        }
                    ) 
                }, 100
            );
        }
    );
    winzKeyTools(formid, objtabs, {sfocus:"gridsel", srefresh:oper_refresh, snew:oper_new, xfocus:"DESCRIPTION", xengage:oper_contextengage, files:3} );
}

