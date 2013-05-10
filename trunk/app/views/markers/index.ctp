<?php 
/**
 * Mark-a-Spot Index Template
 *
 * Index View Splashpage
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
 * @version    1.6 beta 
 */


echo $this->element('head_add', array('cache'=> 3600));?>


<?php		
	/*
	 * Breadcrumb
	 *
	 */

	echo '<div id="breadcrumb"><div>';
	echo $html->addcrumb(
		__('Home',true),
			'/',
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
		<h2><?php __("Welcome to Geo'n'rate") ?></h2>

		<!-- p class="intro"><?php __('Managing Concerns of Public Space') ?></p -->
			<hr class="hidden"/>
		<div id="linksIndex">
			<p align="center">
			<strong>Visita il sito del progetto ProvinciaWiFi:</strong><br/></p>
			<p align="center">
			<a href="http://www.provincia.roma.it/percorsitematici/innovazione-tecnologica/progetti/4035" target="_blank"><img src="<?php echo $this->webroot; ?>img/logo-wifive-in-evidenza_0.jpg"></a>
			</p>
			<?php echo $this->element('intro_ita');?>
			<?php
				echo '<div id="bubble">';
			 	if ($session->read('Auth.User.id')) {
					$action = "add";
					echo '<ul id="nav">';
					echo '<li class="MasOverviewAdd">';
					echo '<h3>Partecipa</h3>';
					echo $html->link('Proponi un Hot Spot ...', array('controller' => 'markers', 'action' => $action),array('class'=>'add'));
					echo '</li></ul>';
					echo '<ul id="navListe">';
					echo '<li>';
					$action = "liste";
					echo $html->link('Vota una proposta ...', array('controller' => 'markers', 'action' => $action),array('class'=>'liste'));
					echo '</li></ul>';
				} else {
					//$action = "startup";?>
					
<p align="center"><strong>SEGNALAZIONI</strong></p>
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
</ul>
<?php 	} ?>		      </div>
			<!--	
			<div id="bubblePeak">
				<?php echo $this->element('search');?>
			</div>-->

			<br style="clear:left">
			<?php if($attachments):?>

			<div id="media">
				<h3><?php __('Reports with fotos');?></h3>
				<div>

				<?php

				$i = 0;
				foreach($markersPublished as $markerPublished){
					foreach($attachments as $attachment){
						$i++;
						if($attachment['Attachment']['foreign_key'] == $markerPublished['Marker']['id'] && $attachment['Attachment']['dirname'] == "img") {
							echo '<div class="thumb">';
									echo '<a class="lightbox imageThumbView" href="'.$this->webroot.'media/filter/xl/'.$attachment['Attachment']['dirname']."/".substr($attachment['Attachment']['basename'],0,strlen($attachment['Attachment']['basename'])-3).'png">';
									
									echo '<img src ="'.$this->webroot.'media/filter/s/'.$attachment['Attachment']['dirname']."/".substr($attachment['Attachment']['basename'],0,strlen($attachment['Attachment']['basename'])-3).'png"/></a>
									<div>
										<div class="clear"></div>';
									echo $html->link(__('View details',true), array('action' => 'view', $attachment['Attachment']['foreign_key']), array('escape'=>false)).'</div>
								</div>';
						} 
						if ($i >= 9) {
							break;
						}
					}
				}
				?>
				</div>
			</div>
			<?php endif;?>
			
		</div>	
		<hr class="hidden"/>
		<div id="listIndex">
			<div id="map_wrapper_splash">
			<?php echo $html->link('', array('controller' => 'markers', 'action' => 'app'), array('title' => __('Click to watch the map',true), 'id' => 'start', 'escape' => false)); ?></div>
			<noscript><p><?php 
				echo $html->link($html->image('http://maps.google.com/staticmap?center='.$googleCenter.'&amp;zoom=10&amp;size=320x200&amp;maptype=mobile\&amp;markers='.$googleCenter.',bluea%7C&amp;key='.$googleKey.'&amp;sensor=false'), array('controller' => 'markers', 'action' => 'app'), array('escape' => false)); ?> 
			</p></noscript>
			<div class="clear"></div>
			<h3><?php __('Recent changes') ?></h3>
			<ul class="marker_splash">
			<?php
			$i = 0;
			foreach ($markers as $marker):
			$i++;

			?>
				<li><div class="color_<?php echo $marker['Status']['hex'] ?>">				
				<!--<p class="status">Stato: <?php echo $marker['Status']['name'] ?></p>--><p class="transactions"><?php //__('This happened:') ?> <?php if (isset($marker['Transaction'][0]['name'])) { echo __($marker['Transaction'][0]['name'],true);}?></p>
				<?php echo $html->link($text->truncate($marker['Marker']['subject'],60, array('ending' => '... ', 'exact' => false)), array('action' => 'view', $marker['Marker']['id']), array('escape'=>false));?>
				</div><small class="meta"><?php //echo $marker['User']['nickname']; ?><?php //__(', on ');
				
 				if ($marker['Marker']['modified'] != Null) { echo $datum->date_it($marker['Marker']['modified'],1); }?></small>
				</li>
				<?php 
					if ($i >= 3) {
						//echo '<div class="thumb_empty">'.__('No picture available',true).'</div>';
						break;
					}
				?>
				
			<?php endforeach; ?>
			
			<!-- Revolver Maps http://www.revolvermaps.com/ -->
				<li>
				<script type="text/javascript" src="http://jh.revolvermaps.com/j.js"></script>
				<script type="text/javascript">rm_j1st('0','180','true','false','000000','7ju0h7xteui','true','ff0000');</script>
				<noscript>
				<applet codebase="http://rh.revolvermaps.com/j" code="core.RE" width="180" height="180" archive="g.jar">
				<param name="cabbase" value="g.cab" />
				<param name="r" value="true" />
				<param name="n" value="false" />
				<param name="i" value="7ju0h7xteui" />
				<param name="m" value="0" />
				<param name="s" value="180" />
				<param name="c" value="ff0000" />
				<param name="v" value="true" />
				<param name="b" value="000000" />
				<param name="rfc" value="true" />
				</applet>
				</noscript>
				</li>
				
			</ul>

		</div>
	</div>