<?php 
/**
 * Mark-a-Spot Add marker, if user is logged in already
 *
 * 
 *
 * Copyright (c) 2010 Holger Kreis
 * http://www.mark-a-spot.org
 *
 *
 * PHP version 5
 * CakePHP version 1.2
 *
 * @copyright  2010 Holger Kreis <holger@markaspot.org>
 * @license    http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero General Public License
 * @link       http://mark-a-spot.org/
 * @version    1.3 beta 
 */
 
echo $this->element('head_add');
?>

<?php
echo $javascript->link('jquery/jquery.validation.min.js', false); 

//echo $javascript->link('jquery/tiny_mce/jquery.tinymce.js', false);

echo $validation->bind(array('Marker'),array('messageId' => 'validateMessage'));
?>

<div id="validateMessage"></div>
<h1 class="hidden"><?php __('Add a marker');?></h1>
<?php		
	echo '<div id="breadcrumb"><div>';
	$html->addcrumb(
		__('Home',true),
			'/',
			array('escape'=>false)
		);
	/*$html->addcrumb(
		 __('Map',true),
		array(
		'controller'=>'markers',
		'action'=>'app'),
		array('escape'=>false)
	);*/
	$html->addcrumb(
		 __('Add marker',true),
		array(
		'controller'=>'markers',
		'action'=>'add'),
		array('escape'=>false)
	);
	echo $html->getCrumbs(' / ');
	echo '</div>';
	
	echo('<script>execute_markers()</script>');
	
	/*
	 * Welcome User with Nickname
	 *
	 */
	echo $this->element('welcome'); 
	echo '</div>';
?>
	<div id="content">

<!--
<?php echo $form->create('Markers_id', array('enctype' => 'multipart/form-data') );?>
			<div align="center">
			<fieldset>
			<LABEL for="marker_id">ID Hot Spot</LABEL>
			<input type="text" id="marker_id" readonly="readonly">
			</fieldset>
			</div>
			<div id="votings">
			<h3><?php __('Voting');?></h3>
			<h4><?php __('Do you agree with this?');?></h4>
			<?php
				echo $this->element('voting', array(
					'plugin' => 'voting', 'model' => 'Marker', 'id' => '16')
				); ?>
			</div>
<?php echo $form->end();?>
-->

		<h2 id="h2_title"><?php __('Add your marker');?></h2>
		<p><?php echo __('Enter details of the problem. Please note that all fields marked with "*" are mandatory.');?></p>
		<?php echo $form->create('Markers', array('enctype' => 'multipart/form-data') );?>
			<div align="center">
			<fieldset>
			<legend><?php __('Give us some Information');?></legend>
			 	<?php echo $this->element('form_add_edit_admin'); ?>
			<LABEL for="n_spot">HotSpot inseriti dall'utente</LABEL>
			<input style="background: #ececec;" type="text" id="n_spot" readonly="readonly">
			<LABEL for="distanza">Hot Spot e proposte entro 100m</LABEL>
			<textarea style="background: #ececec;" style="font-size: 120%" id="distanza" name="distanza" readonly="readonly" cols="10" rows="3" ></textarea>
			</fieldset>
			<input type="submit" value="Salva proposta" onclick="
if (parseInt(document.getElementById('n_spot').value) > 9) {
	alert('Consentito inserimento di massimo 10 Hot Spot.');
	return false;
}
if (document.getElementById('distanza').value != 'Nessuno') {
	alert('Meno di 100m da un Hot Spot esistente.');
	return false;
}
return true;">
	</div>
		<?php //echo '<p>'.$html->tag('button', '<span>'.__('Save information',true).'</span>', array('type' => 'submit')).'</p>';?>
		<?php echo $form->end();?>
	</div>
	<div id="sidebar">
		<h3><?php __('Where does it happen? Localize!')?></h3>
		<p id="add-instructions"><?php echo __('Add a streets name or click on the map in the desired position. You can correct the position of the marker by dragging it.');?></p>

			<div align="center">
			<table><tr>
			<td>Cerca Indirizzo <input type="text" size="40" id="find_street" value="<?php if(isset($this->params['url']['street'])){ echo $this->params['url']['street']; } ?>"><!--</td>-->
			<!--<td>Citt&agrave; <select name="find_city" id="find_city">
<option value=""></option>
<option value="Affile">Affile</option>
<option value="Agosta">Agosta</option>
<option value="Albano Laziale">Albano Laziale</option>
<option value="Allumiere">Allumiere</option>
<option value="Anguillara Sabazia">Anguillara Sabazia</option>
<option value="Anticoli Corrado">Anticoli Corrado</option>
<option value="Anzio">Anzio</option>
<option value="Arcinazzo Romano">Arcinazzo Romano</option>
<option value="Ardea">Ardea</option>
<option value="Ariccia">Ariccia</option>
<option value="Arsoli">Arsoli</option>
<option value="Artena">Artena</option>
<option value="Bellegra">Bellegra</option>
<option value="Bracciano">Bracciano</option>
<option value="Camerata Nuova">Camerata Nuova</option>
<option value="Campagnano di Roma">Campagnano di Roma</option>
<option value="Canale Monterano">Canale Monterano</option>
<option value="Canterano">Canterano</option>
<option value="Capena">Capena</option>
<option value="Capranica Prenestina">Capranica Prenestina</option>
<option value="Carpineto Romano">Carpineto Romano</option>
<option value="Casape">Casape</option>
<option value="Castel Gandolfo">Castel Gandolfo</option>
<option value="Castel Madama">Castel Madama</option>
<option value="Castel San Pietro Romano">Castel San Pietro Romano</option>
<option value="Castelnuovo di Porto">Castelnuovo di Porto</option>
<option value="Cave">Cave</option>
<option value="Cerreto Laziale">Cerreto Laziale</option>
<option value="Cervara di Roma">Cervara di Roma</option>
<option value="Cerveteri">Cerveteri</option>
<option value="Ciampino">Ciampino</option>
<option value="Ciciliano">Ciciliano</option>
<option value="Cineto Romano">Cineto Romano</option>
<option value="Civitavecchia">Civitavecchia</option>
<option value="Civitella San Paolo">Civitella San Paolo</option>
<option value="Colleferro">Colleferro</option>
<option value="Colonna">Colonna</option>
<option value="Fiano Romano">Fiano Romano</option>
<option value="Filacciano">Filacciano</option>
<option value="Fiumicino">Fiumicino</option>
<option value="Fonte Nuova">Fonte Nuova</option>
<option value="Formello">Formello</option>
<option value="Frascati">Frascati</option>
<option value="Gallicano nel Lazio">Gallicano nel Lazio</option>
<option value="Gavignano">Gavignano</option>
<option value="Genazzano">Genazzano</option>
<option value="Genzano di Roma">Genzano di Roma</option>
<option value="Gerano">Gerano</option>
<option value="Gorga">Gorga</option>
<option value="Grottaferrata">Grottaferrata</option>
<option value="Guidonia Montecelio">Guidonia Montecelio</option>
<option value="Jenne">Jenne</option>
<option value="Labico">Labico</option>
<option value="Ladispoli">Ladispoli</option>
<option value="Lanuvio">Lanuvio</option>
<option value="Lariano">Lariano</option>
<option value="Licenza">Licenza</option>
<option value="Magliano Romano">Magliano Romano</option>
<option value="Mandela">Mandela</option>
<option value="Manziana">Manziana</option>
<option value="Marano Equo">Marano Equo</option>
<option value="Marcellina">Marcellina</option>
<option value="Marino">Marino</option>
<option value="Mazzano Romano">Mazzano Romano</option>
<option value="Mentana">Mentana</option>
<option value="Monte Compatri">Monte Compatri</option>
<option value="Monte Porzio Catone">Monte Porzio Catone</option>
<option value="Monteflavio">Monteflavio</option>
<option value="Montelanico">Montelanico</option>
<option value="Montelibretti">Montelibretti</option>
<option value="Monterotondo">Monterotondo</option>
<option value="Montorio Romano">Montorio Romano</option>
<option value="Moricone">Moricone</option>
<option value="Morlupo">Morlupo</option>
<option value="Nazzano">Nazzano</option>
<option value="Nemi">Nemi</option>
<option value="Nerola">Nerola</option>
<option value="Nettuno">Nettuno</option>
<option value="Olevano Romano">Olevano Romano</option>
<option value="Palestrina">Palestrina</option>
<option value="Palombara Sabina">Palombara Sabina</option>
<option value="Percile">Percile</option>
<option value="Pisoniano">Pisoniano</option>
<option value="Poli">Poli</option>
<option value="Pomezia">Pomezia</option>
<option value="Ponzano Romano">Ponzano Romano</option>
<option value="Riano">Riano</option>
<option value="Rignano Flaminio">Rignano Flaminio</option>
<option value="Riofreddo">Riofreddo</option>
<option value="Rocca Canterano">Rocca Canterano</option>
<option value="Rocca di Cave">Rocca di Cave</option>
<option value="Rocca di Papa">Rocca di Papa</option>
<option value="Rocca Priora">Rocca Priora</option>
<option value="Rocca Santo Stefano">Rocca Santo Stefano</option>
<option value="Roccagiovine">Roccagiovine</option>
<option value="Roiate">Roiate</option>
<option value="Roma">Roma</option>
<option value="Roviano">Roviano</option>
<option value="Sacrofano">Sacrofano</option>
<option value="Sambuci">Sambuci</option>
<option value="San Cesareo">San Cesareo</option>
<option value="San Gregorio da Sassola">San Gregorio da Sassola</option>
<option value="San Polo dei Cavalieri">San Polo dei Cavalieri</option>
<option value="San Vito Romano">San Vito Romano</option>
<option value="Santa Marinella">Santa Marinella</option>
<option value="Sant'Angelo Romano">Sant'Angelo Romano</option>
<option value="Sant'Oreste">Sant'Oreste</option>
<option value="Saracinesco">Saracinesco</option>
<option value="Segni">Segni</option>
<option value="Subiaco">Subiaco</option>
<option value="Tivoli">Tivoli</option>
<option value="Tolfa">Tolfa</option>
<option value="Torrita Tiberina">Torrita Tiberina</option>
<option value="Trevignano Romano">Trevignano Romano</option>
<option value="Vallepietra">Vallepietra</option>
<option value="Vallinfreda">Vallinfreda</option>
<option value="Valmontone">Valmontone</option>
<option value="Velletri">Velletri</option>
<option value="Vicovaro">Vicovaro</option>
<option value="Vivaro Romano">Vivaro Romano</option>
<option value="Zagarolo">Zagarolo</option>			
							</select>
			</td>
			<td>--><input type="button" value="Cerca" onclick="
				myID_1 = Math.floor(Math.random()*4294967295).toString(16);
				myID_2 = Math.floor(Math.random()*65535).toString(16);
				myID_3 = Math.floor(Math.random()*65535).toString(16);
				var data = new Date();
				myID_4 = data.getFullYear().toString();
				myID_5 = (data.getMonth()+1).toString();
				if (myID_5.length == 1) myID_5='0'+myID_5;
				myID_6 = data.getDate().toString();
				if (myID_6.length == 1) myID_6='0'+myID_6;
				myID_7 = data.getHours().toString();
				if (myID_7.length == 1) myID_7='0'+myID_7;
				myID_8 = data.getMinutes().toString();
				if (myID_8.length == 1) myID_8='0'+myID_8;
				myID_9 = data.getSeconds().toString();
				if (myID_9.length == 1) myID_9='0'+myID_9;
				myID = myID_1+'-'+myID_2+'-'+myID_3+'-'+myID_4+'-'+myID_5+myID_6+myID_7+myID_8+myID_9;
				street=document.getElementById('find_street').value;
				
				if (street == '') {
					alert('Inserire la citt&agrave;.');
				} else {
					// THIS SUCKS BECAUSE THERE IS NO WAY OF KNOWING WITH CERTAINITY WHICH PARAMETER IS WHAT!
					// WHAT IF EXAMPLE THE APP IS INSTALLED IN A SUBFOLDER?
					//var URL = window.location.protocol +'//'+window.location.host+conf.masDir+'markers/add/'+myID+'/'+street+'/'+city;
					//var URL = window.location.protocol +'//'+window.location.host+conf.masDir+'markers/add/?id='+myID+'&street='+street+'&city='+city;
					//location.href = URL;
					searchAddress();
				}
			"></td>
			</tr></table>
			</div>
			
		<div id="map_wrapper_add"></div>
	</div>