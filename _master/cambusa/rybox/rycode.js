/****************************************************************************
* Name:            rycode.js                                                *
* Project:         Cambusa/ryBox                                            *
* Version:         1.69                                                     *
* Description:     Masked input and other form controls                     *
* Copyright (C):   2015  Rodolfo Calzetti                                   *
*                  License GNU LESSER GENERAL PUBLIC LICENSE Version 3      *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
var clipcode=null;
var _globalcodeinsert=$.cookie("codeinsert");
if(_ismissing(_globalcodeinsert)){
    _globalcodeinsert=1;
}
(function($,missing) {
    $.extend(true,$.fn, {
        rycode:function(settings){
			var propleft=20;
			var proptop=20;
			var propwidth=120;
			var propheight=22;
            var propmaxlen=8;
			var propcode="";
            var propmode="alpha";
            var proplock=0;
            var prophelper=1;
            var propinsert=parseInt(_globalcodeinsert);
			var propstart=0;
			var propfocusout=true;
            var propselected=false;
            var propsospendsel=false;
			var propctrl=false;
			var propshift=false;
			var propobj=this;
            var propchanged=false;
			var propenabled=1;
			var propvisible=true;
			
			var propname=$(this).attr("id");
			this.id="#"+propname;
			this.tag=null;
			this.type="code";
			
			globalobjs[propname]=this;

			if(settings.left!=missing){propleft=settings.left}
			if(settings.top!=missing){proptop=settings.top}
            if(settings.width!=missing){propwidth=settings.width}
            if(settings.maxlen!=missing){propmaxlen=settings.maxlen}
            if(settings.mode!=missing){propmode=settings.mode}
            if(settings.lock!=missing){proplock=_bool(settings.lock)}
            if(settings.helper!=missing){prophelper=_bool(settings.helper)}

            if(settings.formid!=missing){
                // Aggancio alla maschera per quando i campi sono dinamici
                $("#"+propname).prop("parentid", settings.formid);
                _globalforms[settings.formid].controls[propname]=propname.substr(settings.formid.length);
            }
            if(settings.datum!=missing){
                // Le modifiche vengono segnalate alla maschera
                $("#"+propname).prop("datum", settings.datum);
            }
            if(settings.tag!=missing){this.tag=settings.tag}

            $("#"+propname).prop("modified", 0 )
            .addClass("ryobject")
            .addClass("rycode")
            .css({
                "position":"absolute",
                "left":propleft,
                "top":proptop,
                "width":propwidth,
                "height":propheight,
                "color":"transparent",
                "background-color":"silver",
                "font-family":"verdana,sans-serif",
                "font-size":"13px",
                "line-height":"17px",
                "cursor":"default"
            })
            .html("<input type='text' id='"+propname+"_anchor'><div id='"+propname+"_internal'></div><div id='"+propname+"_button'></div>");

            $("#"+propname+"_internal")
            .css({"position":"absolute","left":1,"top":1,"width":propwidth-2,"height":propheight-2,"color":"#000000","background-color":"#FFFFFF","overflow":"hidden"})
            .html("<div id='"+propname+"_text'></div><div id='"+propname+"_cursor'></div><span id='"+propname+"_span'></span>");

            $("#"+propname+"_cursor").css({"position":"absolute","left":1,"top":1,"width":1,"height":propheight-4,"background-color":"#000000","visibility":"hidden"});
            $("#"+propname+"_span").css({"position":"absolute","visibility":"hidden"});
            $("#"+propname+"_text").css({"position":"absolute","cursor":"text","left":2,"top":1,"height":propheight-4,"overflow":"hidden"});
            $("#"+propname+"_button").css({"position":"absolute","cursor":"pointer","left":propwidth-20,"top":2,"width":18,"height":18,"background":"url("+_cambusaURL+"ryquiver/images/helper.png)"});
            
            if(prophelper){
                $("#"+propname+"_text").css({"width":propwidth-26});
                $("#"+propname+"_button").css({"display":"block"});
            }
            else{
                $("#"+propname+"_text").css({"width":propwidth-4});
                $("#"+propname+"_button").css({"display":"none"});
            }

            $("#"+propname+"_anchor").focus(
            	function(){
            		if(propenabled){
            			$("#"+propname+"_cursor").css({"visibility":"visible"});
            			$("#"+propname+"_internal").css({"background-color":globalcolorfocus});
                        propfocusout=false;
                        propchanged=false;
                        if(!propsospendsel)
                            propobj.selected(true);
                        propstart=0;
                        propobj.refreshcursor();
                        propobj.raisegotfocus();
            		}
            	}
            );
            $("#"+propname+"_anchor").focusout(
            	function(){
            		if(propenabled){
            			$("#"+propname+"_cursor").css({"visibility":"hidden"});
            			$("#"+propname+"_internal").css({"background-color":"#FFFFFF"});
            			propobj.completion();
                        propobj.selected(false);
            			if(propchanged)
                            propobj.raiseassigned();
                        propobj.raiselostfocus();
                        propfocusout=true;
            		}
            	}
            );
            $("#"+propname+"_anchor").keydown(
            	function(k){
                    if(_navigateKeys(k))  // Tasti usati in navigazione tabs
                        return true;
            		if(propenabled && !proplock){
            			propctrl=k.ctrlKey; // da usare anche nella press
            			propshift=k.shiftKey;
                        // GESTIONE CLIPBOARD
                        if(propctrl){
                            switch(k.keyCode){
                            case 88:
            					clipcode=propobj.value();
            					propobj.value("");
                                k.preventDefault();
                                return false;
                            case 67:
            					var v=propobj.value();
            					if(v)
            						clipcode=v;
                                k.preventDefault();
                                return false;
                            case 86:
            					propobj.value(clipcode);
                                k.preventDefault();
                                return false;
                            }
                        }
                        // GESTIONE ALTRI TASTI
            			if(k.which==39){ // right
            				if(propstart<propcode.length){
                                propstart+=1;
            					propobj.refreshcursor();
            				}
            			}
            			else if(k.which==37){ // left
            				if(propstart>0){
                                propstart-=1;
            					propobj.refreshcursor();
            				}
            			}
            			else if(k.which==36){ // home
            				if(propstart>0){
            					propstart=0;
            					propobj.refreshcursor();
            				}
            			}
            			else if(k.which==35){ // end
            				if(propstart<propcode.length){
            					propstart=propcode.length;
            					propobj.refreshcursor();
            				}
            			}
            			else if(k.which==46){ // delete
            				if(propctrl){
            					clipcode=propobj.value();
            					propobj.clear();
            				}
            				else{
                                if(propselected){
                                    propobj.clear();
                                    propobj.selected(false);
                                }
                                if(propstart<propcode.length){
                                    propcode=propcode.substr(0,propstart)+propcode.substr(propstart+1);
                                }
            				}
            				$("#"+propname+"_text").html(propcode);
            				propobj.refreshcursor();
                            propobj.raisechanged();
            			}
            			else if(k.which==45){ // ins
            				if(propctrl){
            					var v=propobj.value();
            					if(v)
            						clipcode=v;
            				}
            				else if(propshift){
            					propobj.value(clipcode);                    
            				}
                            else{
                                propobj.insert(!propinsert);
                            }
            			}
            			else if(k.which==113){ // F2
            				propobj.showdialog();
            			}
            			else if(k.which==13){ // INVIO
                            propobj.completion();
            				propstart=0;
            				propobj.refreshcursor();
                            propchanged=false;
                            propobj.raiseassigned();
                            if(settings.enter!=missing){
                                settings.enter(propobj);
                            }
            			}
            			if(k.which==8){
                            if(propselected){
                                propobj.clear();
                                propobj.selected(false);
                            }
                            
                            if(propstart>0){
                                propstart-=1;
                                propcode=propcode.substr(0,propstart)+propcode.substr(propstart+1);
                            }

                            $("#"+propname+"_text").html(propcode);
            				propobj.refreshcursor();
                            propobj.raisechanged();
            			}
            		}
            		if(k.which==8 || k.which==35 || k.which==36){
            			return false;
            		}
                    else if(k.which==9){
                        return nextFocus(propname, propshift);
                    }
            	}
            );
            $("#"+propname+"_anchor").keypress(
            	function(k){
                    if(_navigateKeys(k))  // Tasti usati in navigazione tabs
                        return true;
            		if(propenabled && !proplock){
                        var n=String.fromCharCode(k.which);
            			var u=n.toUpperCase();
            			if(propstart<propmaxlen){
                            var ok=false;
                            if(!propinsert || propcode.length<propmaxlen ){
                                switch(propmode){
                                case "filled":
                                    ok=("0"<=u && u<="9");
                                    break;
                                case "system":
                                    ok=("0"<=u && u<="9") || ("A"<=u && u<="Z");
                                    n=u;
                                    break;
                                default:
                                    ok=("0"<=u && u<="9") || ("A"<=u && u<="Z") || n=="_";
                                }
                            }
            				if(ok){
                                if(propselected){
                                    propobj.clear();
                                    propobj.selected(false);
                                }
                                if( propstart<propmaxlen ){
                                    if(propinsert)
                                        propcode=propcode.substr(0,propstart)+n+propcode.substr(propstart);
                                    else
                                        propcode=propcode.substr(0,propstart)+n+propcode.substr(propstart+1);
                                    propstart+=1;
                                    
                                    $("#"+propname+"_text").html(propcode);
                                    propobj.refreshcursor();
                                    propobj.raisechanged();
                                }
            				}
            			}
            		}
            	}
            );
            $("#"+propname+"_anchor").keyup(
            	function(k){
                    if(k.which!=9 && k.which!=16){
                        if(propselected){
                            propobj.selected(false);
                        }
                    }
                    // MANTENGO PULITO INPUT
                    $("#"+propname+"_anchor").val("");
                }
            );
            $("#"+propname+"_text").mousedown(
            	function(evt){
                    if(propselected){
                        propobj.selected(false);
                    }
            		if(propenabled && !proplock){
            			var p=evt.pageX-propleft;
            			var l,i;
            			propstart=0;
            			for(i=1;i<=propmaxlen;i++){
            				l=propobj.textwidth(propcode.substr(0,i));
            				if(l>p+3){
                                propstart=i;
            					break;
            				}
            			}
            			propobj.refreshcursor();
            		}
            	}
            );
            $("#"+propname).mousedown(
            	function(evt){
            		if(propenabled){
            			castFocus(propname);
            		}
            	}
            );
            $("#"+propname+"_button").click(
            	function(){
            		if(propenabled){
            			propobj.showdialog();
            		}
            	}
            );
            $("#"+propname+"_text").contextMenu("dateMenu", {
            	bindings: {
            		'cut': function(t) {
            			clipcode=propobj.value();
            			propobj.value("");
            		},
            		'copy': function(t) {
            			var v=propobj.value();
            			if(v)
            				clipcode=v;
            		},
            		'paste': function(t) {
            			propobj.value(clipcode);
            		}
            	},
            	onContextMenu:
            		function(e) {
            			if((clipcode==null && propobj.value()==null) || !propenabled)
            				return false;
            			else 
            				return true;
            		},
            	onShowMenu: 
            		function(e, menu) {
            			if(!propobj.value()){
            				$('#copy', menu).remove();
            			}
            			if(!propobj.value() || proplock){
            				$('#cut',menu).remove();
            			}
            			if(!clipcode || proplock){
            				$('#paste', menu).remove();
            			}
            			return menu;
            		}
            });
            // FUNZIONI PUBBLICHE
            this.move=function(params){
                if(params.left!=missing){propleft=params.left}
                if(params.top!=missing){proptop=params.top}
                if(params.width!=missing){propwidth=params.width}
                $("#"+propname).css({"left":propleft,"top":proptop,"width":propwidth});
            }
			this.maxlen=function(l){
				if(l==missing)
					return propmaxlen;
				else
					propmaxlen=l;
			}
			this.showdialog=function(r){
                if(settings.dialog!=missing){
                    propsospendsel=true;
                    settings.dialog(propobj);
                }
			}
			this.refreshcursor=function(){
				var t=propcode;
				var s=t.substr(0, propstart);
				$("#"+propname+"_cursor").css({"left":propobj.textwidth(s)+1})
			}
			this.textwidth=function(s){
				$("#"+propname+"_span").html(s);
				return $("#"+propname+"_span").width();
			}
			this.completion=function(){
                var c=propcode, t;
                switch(propmode){
                case "filled":
                    if(propcode!="" && propcode.length!=propmaxlen){
                        t="00000000000000000000"+propcode;
                        propcode=t.substr(t.length-propmaxlen);
                    }
                    break;
                default:
                    if(propcode.length>propmaxlen){
                        propcode=propcode.substr(0, propmaxlen);
                    }
                }
                if(propcode!=c)
                    propobj.raisechanged();
                $("#"+propname+"_text").html(propcode);
			}
			this.value=function(v,a){
				if(v==missing){
					propobj.completion();
                    return propcode;
				}
				else{
                    propobj.raisechanged();
                    propchanged=false;
					try{
						if(v!=""){
                            propcode=v;
                            if(a==missing){a=false}
                            if(a){propobj.raiseassigned()}
						}
						else{
							propobj.clear();
						}
					}
					catch(e){
						propobj.clear();
					}
					$("#"+propname+"_text").html(propcode);
					propstart=0;
					propobj.refreshcursor();
				}
			}
			this.name=function(){
				return propname;
			}
			this.enabled=function(v){
				if(v==missing){
					return propenabled;
				}
				else{
					propenabled=_bool(v);
					if(v){
						$("#"+propname+"_anchor").removeAttr("disabled");
						$("#"+propname+"_text").css({"color":"#000000","cursor":"text"});
						$("#"+propname+"_button").css({"cursor":"pointer"});
						if(propfocusout==false){
							$("#"+propname+"_cursor").css({"visibility":"visible"});
							propobj.refreshcursor();
						}
					}
					else{
						$("#"+propname+"_anchor").attr("disabled",true);
						$("#"+propname+"_text").css({"color":"gray","cursor":"default"});
						$("#"+propname+"_button").css({"cursor":"default"});
						$("#"+propname+"_cursor").css({"visibility":"hidden"});
					}
				}
			}
			this.visible=function(v){
				if(v==missing){
					return propvisible;
				}
				else{
					propvisible=v;
					if(v)
						$("#"+propname).css({"visibility":"visible"});
					else
						$("#"+propname).css({"visibility":"hidden"});
				}
			}
			this.mode=function(v){
				if(v==missing){
					return propmode;
				}
				else{
					propmode=_bool(v);
				}
			}
			this.lock=function(v){
				if(v==missing){
					return proplock;
				}
				else{
					proplock=_bool(v);
				}
			}
			this.helper=function(v){
				if(v==missing){
					return prophelper;
				}
				else{
					prophelper=_bool(v);
                    if(prophelper){
                        $("#"+propname+"_text").css({"width":propwidth-20});
                        $("#"+propname+"_button").css({"display":"block"});
                    }
                    else{
                        $("#"+propname+"_text").css({"width":propwidth-2});
                        $("#"+propname+"_button").css({"display":"none"});
                    }
				}
			}
			this.insert=function(v){
				if(v==missing){
					return propinsert;
				}
				else{
					propinsert=_bool(v);
                    $.cookie("codeinsert", propinsert, {expires:10000});
				}
			}
			this.changed=function(v){
				if(v==missing)
					return propchanged;
				else
					propchanged=v;
			}
			this.modified=function(v){
				if(v==missing)
					return _bool( $("#"+propname).prop("modified") );
				else
					$("#"+propname).prop("modified", _bool(v) );
			}
            this.selected=function(v){
                propselected=v;
                propsospendsel=false;
                if($("#"+propname+"_text").html()=="")
                    propselected=false;
                if(propselected)
                    $("#"+propname+"_text").css({"background-color":"#87CEFA", "color":"white"});
                else
                    $("#"+propname+"_text").css({"background-color":"transparent", "color":"black"});
            }
			this.clear=function(){
				propstart=0;
                propcode="";
                $("#"+propname+"_text").html(propcode);
                propobj.raisechanged();
			}
			this.focus=function(){
				objectFocus(propname);
			}
            this.raisegotfocus=function(){
                if(settings.gotfocus!=missing){settings.gotfocus(propobj)}
            }
            this.raiselostfocus=function(){
                if(settings.lostfocus!=missing){settings.lostfocus(propobj)}
            }
            this.raisechanged=function(){
                propchanged=true;
                propobj.modified(1);
                if(settings.changed!=missing){settings.changed(propobj)}
                _modifiedState(propname,true);
            }
            this.raiseassigned=function(){
                propobj.modified(1);
                if(settings.assigned!=missing){settings.assigned(propobj)}
                propchanged=false;
            }
			return this;
		}
	});
})(jQuery);