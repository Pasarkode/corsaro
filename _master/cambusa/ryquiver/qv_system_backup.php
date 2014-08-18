<?php 
/****************************************************************************
* Name:            qv_system_backup.php                                     *
* Project:         Cambusa/ryQuiver                                         *
* Version:         1.00                                                     *
* Description:     Arrows-oriented Library                                  *
* Copyright (C):   2013  Rodolfo Calzetti                                   *
* License GNU GPL: http://www.rudyz.net/cambusa/license.html                *
* Contact:         faustroll@tiscali.it                                     *
*                  postmaster@rudyz.net                                     *
****************************************************************************/
include_once "quiverfil.php";
include_once "../rymaestro/maestro_querylib.php";
include_once "../tbs_us/plugins/tbs_plugin_opentbs.php";
function qv_system_backup($maestro, $data){
    global $babelcode, $babelparams;
    global $path_databases, $path_cambusa;
    global $global_lastadmin;
    try{
        // IMPOSTO I VALORI DI RITORNO PREDEFINITI
        $success=1;
        $message="Operazione riuscita";
        $SYSID="";
        
        // GESTIONE AMMINISTRATORE
        if($global_lastadmin==0){
            $babelcode="QVERR_FORBIDDEN";
            $b_params=array();
            $b_pattern="Autorizzazioni insufficienti";
            throw new Exception( qv_babeltranslate($b_pattern, $b_params) );
        }
        
        // RISOLVO DIRECTORY TEMPORANEA E DIRECTORY ALLEGATI
        qv_environs($maestro, $dirtemp, $dirattach);
        
        // CARICO LA STRUTTURA DEL DATABASE
        $maestro->loadinfo();

        // APERTURA FILE
        $BACKUPNAME=$maestro->environ . date("YmdHis");
        $pathname=$path_databases."_backup/$BACKUPNAME.QBK";
        $pathnametemp=$path_databases."_backup/$BACKUPNAME.QBK_TMP";
        $fp=fopen($pathnametemp, "wb");
        $version="QBK VERSION 001";
        $rec=str_repeat("0", 18);
        fwrite($fp, $version);
        fwrite($fp, "\n");
        fwrite($fp, $rec);  // Posizione 15+1
        fwrite($fp, "\n");
        
        // CALCOLO IL TOTALE DEI RECORD PER L'AVANZAMENTO
        $total=0;
        foreach($maestro->infobase as $TABLENAME => $TABLE){
            if($TABLE->type=="database"){
                $sql="SELECT COUNT(*) AS TOT FROM $TABLENAME";
                maestro_query($maestro, $sql, $r);
                if(count($r)==1){
                    $total+=intval($r[0]["TOT"]);
                }
            }
        }
        
        // UTILE PER AVANZAMENTO DA CLIENT
        print substr( ($total+1) . str_repeat(" ", 100), 0, 100);
        flush();
        
        $counter=0;
        foreach($maestro->infobase as $TABLENAME => $TABLE){
            if($TABLE->type=="database"){
                $res=maestro_unbuffered($maestro, "SELECT * FROM $TABLENAME ORDER BY SYSID");
                while( $row=maestro_fetch($maestro, $res) ){
                    $row["_TABLENAME"]=$TABLENAME;
                    if($TABLENAME=="QVFILES"){
                        // INCORPORAZIONE DEL DOCUMENTO
                        $SYSID=$row["SYSID"];
                        $SUBPATH=$row["SUBPATH"];
                        $IMPORTNAME=$row["IMPORTNAME"];
                        $path_parts=pathinfo($IMPORTNAME);
                        if(isset($path_parts["extension"]))
                            $ext="." . $path_parts["extension"];
                        else
                            $ext="";
                        $filedoc=$dirattach.$SUBPATH.$SYSID.$ext;
                        $contents="";
                        if(file_exists($filedoc)){
                            $contents=file_get_contents($filedoc);
                            $contents=base64_encode($contents);
                        }
                        $row["_CONTENTS"]=$contents;
                    }
                    
                    // SERIALIZZAZIONE
                    $buff=serialize($row);
                    $head=substr( str_repeat("0", 18) . strlen($buff), -18 );
        
                    // SCRITTURA
                    fwrite($fp, $head);
                    fwrite($fp, "\n");
                    fwrite($fp, $buff);
                    fwrite($fp, "\n");
                    
                    // AGGIORNO IL CONTATORE
                    $counter+=1;
                    
                    // UTILE PER AVANZAMENTO DA CLIENT
                    print str_repeat("X", 100);
                    flush();
                }
                maestro_free($maestro, $res);
            }
        }
        // UTILE PER AVANZAMENTO DA CLIENT
        print "Y";
        
        // AGGIORNAMENTO DEL NUMERO DI RECORD
        fseek($fp, 16);
        $buff=substr( str_repeat("0", 18) . $counter, -18 );
        fwrite($fp, $buff);

        // CHIUSURA FILE
        fclose($fp);
        
        // RINOMINO IL FILE
        @rename($pathnametemp, $pathname);
        
        // VARIABILI DI RITORNO
        $babelparams["BACKUP"]="_backup/$BACKUPNAME.QBK";
    }
    catch(Exception $e){
        $success=0;
        $message=$e->getMessage();
        // UTILE PER AVANZAMENTO DA CLIENT
        print "Y";
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