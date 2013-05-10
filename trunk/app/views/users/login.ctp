<?php
/**
 * Mark-a-Spot User login
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
 * @version    0.98
 */

echo $this->element('head_nomap'); 
$javascript->link('jquery/jquery.validation.min.js', false); 
echo $validation->bind('User');	

echo '<div id="breadcrumb"><div>';
	$html->addcrumb(
		 __('Home', true),
			'/',
			array('escape'=>false)
		);
	$html->addcrumb(
		__('Log in', true),
		array(
			'controller'=>'users',
			'action'=>'login'),
			array('escape'=>false)
	);
	echo $html->getCrumbs(' / ');
	echo '</div>';
	
		
		
	/*
	 * Welcome User with Nickname
	 *
	 */
	echo $this->element('welcome'); 
	echo '</div>';

?>
	<div id="content">

		<h2 id="h2_title" align="center"><?php __('Log in');?></h2>

		<?php
			echo $form->create('User', array('action' => 'login'));
		?>
		<fieldset>
		 <legend><?php __('Enter login data');?></legend>
		 
		<div style="visibility:hidden">
		<?php
		 
		//echo $form->input('email_address',array('value' => 'testuser@markaspot.org', 'label'=>__('E-Mail',true), 'between'=>'<br/>', 'class'=>'text'));
		//echo $form->input('password',array('value' => 'password', 'label'=>__('Password',true), 'between'=>'<br/>', 'class'=>'text'));
		//echo $form->input('remember_me', array('label' => __('Log in automatically on this computer',true), 'type' => 'checkbox'));
		echo $form->hidden('email_address',array('value' => 'testuser@markaspot.org'));
		echo $form->hidden('password',array('value' => 'password'));
		echo $form->hidden('remember_me', array('type' => 'checkbox'));
		echo '</div>';
?>
<noscript>
<center>
<b>Il tuo browser non ha Javascript attivato. L'applicativo funziona solamente se Javascript è attivo</b>
</center>
<div style='display:none;'>
</noscript>
<script>
document.write('<div>');
</script>
<?php
		echo $form->input('username_caspur',array('value' => '', 'label'=>__('Nome utente: (e.g.: numero di telefono):',true), 'between'=>'<br/>', 'class'=>'text'));
		echo $form->input('password_caspur',array('value' => '', 'label'=>__('Password',true), 'between'=>'<br/>', 'class'=>'text', 'type' => 'password'));
		echo '</div>';

		echo '</fieldset>';
	
		echo '<p>';?>


<noscript>
</center>
<div style='display:none;'>
</noscript>
<script>
document.write('<div align="center">');
</script>

		<input type="submit" value="Log in" onclick="if ((document.getElementById('UserUsernameCaspur').value=='') || (document.getElementById('UserPasswordCaspur').value=='')) { alert('Devi inserire nome utente e password');return false}"></div>

		<?php //echo $html->tag('button', __('<span>Log in</span>',true), array('type' => 'submit'));
		echo '</p>';
		
		if (Configure::Read('Social.FB') != false && Configure::Read('Social.Twitter') !=false) {
		echo $html->tag('p', __('Are you already a member of the following social media networks? Sign in to out platform based on "Mark-a-Spot"',true));
		}
		
		if (Configure::Read('Social.FB') != false) {
			echo $facebook->login(); 
		} 
		if (Configure::Read('Social.Twitter') != false) {	
			echo '<a id="signinTwitter" href="/twitter/connect"><span>Sign with Twitter</span></a>';
		}
		echo $form->end();?>

	</div>
	
	<div id="sidebar">
<p align="center"><strong>CLAUSOLE DI UTILIZZO</strong></p>
<p align="justify"><strong> 1.  OGGETTO E DEFINIZIONI</strong></p>

<ul>
	<ul>
		<li>
			<p align="justify"> Le  		presenti Clausole di Utilizzo (&ldquo;CdU&rdquo;) regolano e disciplinano  		l'utilizzo della piattaforma &ldquo;Provincia di Roma - Mark a Spot&rdquo; presente  		all'indirizzo web markaspot.provincia.roma.it da parte dell'Utente nonché le  		responsabilità di quest'ultimo relativamente all'utilizzo della  		piattaforma anzidetta.</p>
		</li>
		<li>
			<p align="justify"> Tramite la piattaforma  		&ldquo;Provincia di Roma - Mark a Spot&rdquo; l'Utente può  		segnalare gratuitamente l'ubicazione di un nuovo Hot Spot per la rete Wi Fi della Provincia di Roma.</p>
		</li>
		<li>
			<p align="justify"> Per  		&ldquo;Utente&rdquo; si intende la persona fisica o giuridica registrata che effettua le segnalazioni per nuovi Hot Spot  		alla piattaforma &ldquo;Provincia di Roma - Mark a Spot&rdquo;.</p>
	</ul>
</ul>
<p align="justify"> <strong>2.  REGISTRAZIONE</strong></p>

<p align="justify">Per  poter effettuare l'attività di segnalazione di cui all'articolo  precedente, l'Utente deve necessariamente registrasi al Servizio Internet Provinciawifi (può essere utilizzata  l'apposita procedura di registrazione presente <a href="https://wasp.provinciawifi.it/owums/account/signup" target="_blank">qui</a>).</p>
<p align="justify">L'Utente si assume ogni responsabilità civile e penale per  l&rsquo;eventuale falsità o non correttezza delle informazioni e dei  dati comunicati.<br />
</p>
<p align="justify"> <strong>3.  SEGNALAZIONI</strong></p>
<ul>
	<ul>

		<li>
			<p align="justify"> Oggetto  		delle segnalazioni dell'Utente sono i siti in cui si vorrebbe fosse installata l'apposita apparecchiatura per la connessione alla rete Wi Fi della Provincia di Roma.</p>
		</li>
		<li>
			<p align="justify"> La  		segnalazione da parte dell'Utente potrà avvenire tramite l'invio  		informatico di fotografie, filmati audio e video, commenti.</p>

		</li>
		<li>
			<p align="justify"> Si possono effettuare un numero massimo di 10 segnalazioni.</p>
		</li>
		<li>
			<p align="justify"> Può essere richiesta la rimozione di una segnalazione scrivendo all'indirizzo e-mail nella sezione "Contatti".</p>
		</li>
		<li>
			<p align="justify">Le segnalazioni possono essere votate dagli altri Utenti registrati. Successivamente la Provincia di Roma terrà conto delle segnalazioni, delle votazioni e della fattibilità tecnica per stabilire nuove ubicazioni per gli Hot Spot della rete ProvinciaWiFi.</p>
		</li>
		<li>
			<p align="justify"> Con  		l'invio della segnalazione l'Utente dichiara di essere titolare di  		ogni diritto eventualmente connesso alla segnalazione (a titolo  		meramente esemplificativo e non esaustivo: fotografie, filmati  		audio/ video etc.).</p>
		</li>

		<li>
			<p align="justify"> Ricevuta  		la segnalazione da parte dell'Utente la Provincia di Roma sarà libera di  		inserirla all'interno della piattaforma &ldquo;Provincia di Roma - Mark a Spot&rdquo;. La Provincia di Roma  		potrà in ogni momento eliminare dalla piattaforma la segnalazione,  		come pure potrà modificare gli elementi non essenziali della  		stessa (ad esempio la durata del filmato, la grandezza delle  		immagini, commenti non attinenti etc.). La Provincia di Roma potrà anche  		oscurare parte della segnalazione qualora essa possa ledere il  		diritto di soggetti terzi o di una collettività di persone.</p>
		</li>
		<li>
			<p align="justify"> In  		ogni caso, l'inserimento e l'eliminazione della segnalazione  		all'interno della piattaforma &ldquo;Provincia di Roma - Mark a Spot&rdquo; è rimesso alla  		discrezionalità della Provincia di Roma.</p>

		</li>
		
		<li>
			<p align="justify"> L'Utente  		registrato accetta di partecipare anche alla eventuale  		realizzazione di classifiche inerenti le segnalazioni (a titolo  		meramente esemplificativo e non esaustivo: classifiche per numero  		di segnalazioni, per tipologie di segnalazioni, per provenienza di  		segnalazioni etc.);</p>
		</li>
	</ul>
</ul>
<p align="justify"> <strong>4.  RESPONSABILITA' DELL'UTENTE</strong></p>
<ul>
	<ul>
		<li>
			<p align="justify">L'Utente  		si  		assume ogni responsabilità nonché ogni conseguenza diretta o  		indiretta derivante da eventuali lesioni dei diritti di terzi (a  		titolo meramente esemplificativo e non esaustivo, diritti d&rsquo;autore  		o altri diritti di proprietà, diritti relativi alla riservatezza  		delle persone etc.) dovuti a seguito dell'inserimento nella  		segnalazione dell'Utente di testi, commenti, fotografie, filmati  		e/o qualsiasi altro materiale fatto comunque pervenire alla Provincia di Roma.</p>
		</li>

		<li>
			<p align="justify"> L'Utente  		si impegna non inserire nella segnalazione materiale o estratti di  		materiale:</p>
		</li>
	</ul>
</ul>
<ul>
	<li>
		<p align="justify">coperti  	da diritto d'autore e di cui non sia esso stesso titolare;</p>

	</li>
	<li>
		<p align="justify">contrari  	alla morale e l'ordine pubblico, ovvero in grado di turbare la  	quiete pubblica o privata o di recare offesa, o danno diretto o  	indiretto a chiunque o ad una specifica categoria di persone (per  	esempio è vietato l&rsquo;inserimento di materiali o estratti di  	materiale che possano ledere la sensibilità di gruppi etnici o  	religiosi, etc.);</p>
	</li>
	<li>
		<p align="justify">contrario  	al diritto alla riservatezza di soggetti terzi;</p>
	</li>

	<li>
		<p align="justify">lesive  	dell'onore, del decoro o della reputazione di soggetti terzi;</p>
	</li>
	<li>
		<p align="justify"> comunque  	contrario alla legge.</p>
	</li>
</ul>

<p align="justify"> </p>
<p align="justify"> <strong>5</strong><strong>.  LIMITAZIONI DI RESPONSABILITA' </strong></p>
<p align="justify">La Provincia di Roma non risponde dei danni diretti, indiretti o consequenziali subiti  dall'Utente o da terzi in dipendenza della pubblicazione della  segnalazione e/o per l'utilizzo dei Servizi di Comunicazione e/o per  danni di qualsiasi genere o a qualsiasi titolo connessi con dette  situazioni e a tal fine l'Utente dichiara di manlevare la Provincia di Roma da ogni  forma di responsabilità;</p>
<p align="justify"> <strong>6. MODIFICA DELLE CLAUSOLE DI UTILIZZO</strong></p>
<p align="justify">La Provincia di Roma si riserva  il diritto di modificare i termini, le condizioni, e le comunicazioni  ai sensi dei quali viene offerta la Piattaforma &ldquo;Provincia di Roma - Mark a Spot&rdquo;.
</p>

	<!--	<p><?php //echo __('You haven&rsquo;t been here yet and want to add a marker?',true);?></p> -->
	<!--	<ul> -->
	<!--	<?php
		//echo '<li>'.$html->link(__('Add a marker directly', true), array('controller' => '/', 'action' => 'startup')).'</li>';
		//echo '<li>'.$html->link(__('Lost your password?', true), array('controller' => 'users', 'action' => 'resetpassword')).'</li>';
		?> -->
	<!--	</ul> -->
	<!-- <div id="map" style="visibility:hidden"></div> -->
</div>
