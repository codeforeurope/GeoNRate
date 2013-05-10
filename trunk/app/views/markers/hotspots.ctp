var latitudine=new Array();
var longitudine=new Array();
var indirizzo=new Array();
var citta=new Array();
var tipologia=new Array();
var indicizzatore=new Array();
var execute_markers_eseguita=false;
function execute_markers() {
	if (execute_markers_eseguita) return;
	execute_markers_eseguita=true;
<?php
	$i=0;
	foreach($markers as $marker){
	  print_r();
	  echo('latitudine['.$i.']="'.$marker['Marker']['lat'].'";');
	  echo('longitudine['.$i.']="'.$marker['Marker']['lon'].'";');
	  echo('indirizzo['.$i.']="'.$marker['Marker']['street'].'";');
	  echo('citta['.$i.']="'.$marker['Marker']['city'].'";');
	  echo('tipologia['.$i.']="'.$marker['Marker']['category_id'].'";');
	  echo('indicizzatore["'.$marker['Marker']['id'].'"]="'.$i.'";');
	  $i++;
    }
	echo 'n_hs="'.$count.'"';
?>
}