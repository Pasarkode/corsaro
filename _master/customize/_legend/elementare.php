<?php
function legendMain(){
    global $SEEKER;

    $SEEKER->dataload();
    
    $int=&$SEEKER->bags["Interni"]["DATA"];
    $est=&$SEEKER->bags["Esterni"]["DATA"];
    $intsignum=$SEEKER->bags["Interni"]["SIGNUM"];
    $estsignum=$SEEKER->bags["Esterni"]["SIGNUM"];
    
    // PRIMO METODO
    $estamount=array();
    foreach($est as $i => $mov){
        $estamount[$i]=$estsignum*$mov["AMOUNT"];
    }
    
    // SECONDO METODO
    //$SEEKER->column2table($est, "AMOUNT", $estamount);
    //$SEEKER->index($estamount, "AMOUNT", $index);
    
    /*
    $SEEKER->markov_initialize();
    $SEEKER->markov(array( array("SYSID"=>"0001", "AMOUNT"=>2000 ) ), 0);
    $SEEKER->markov_terminate();
    */
    
    $total=count($estamount);
    $counter=0;
    
    foreach($int as $mov){
        $counter+=1;
        $SEEKER->progress("Cicli " . $counter . " di " . $total);
        // PRIMO METODO
        $i=array_search(-$intsignum*$mov["AMOUNT"], $estamount);
        // SECONDO METODO
        //$i=$SEEKER->dichotomic($estamount, "AMOUNT", -$intsignum*$mov["AMOUNT"], $index, true);
        if($i!==false){
            $CLUSTERID=$SEEKER->clusterize($mov["SYSID"]);
            // PRIMO METODO
            $SEEKER->clusterize($est[$i]["SYSID"], $CLUSTERID);
            unset($estamount[$i]);
            // SECONDO METODO
            //$SEEKER->clusterize($estamount[$index[$i]]["SYSID"], $CLUSTERID);
        }
    }
    return true;
}
?>