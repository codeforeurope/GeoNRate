<?php
		if (isset($this->params['pass'][0])) {
			echo $form->input('Marker.id', array("type" => "hidden","value" => $this->params['pass'][0]));
		}
		echo $form->hidden('Marker.subject', array('value' => 'deprecated'));		?>
		<div align="center">
		<p><label for="MarkerStreet">Indirizzo</label> <input readonly="readonly" name="data[Marker][street]" style="background: #ececec;" type="text" id="MarkerStreet" value="<?php if($marker){ echo $marker['Marker']['street']; } ?>" /></p>
		<p><label for="MarkerZip">CAP</label> <input readonly="readonly" name="data[Marker][zip]" style="background: #ececec;" type="text" id="MarkerZip" value="<?php if($marker){ echo $marker['Marker']['zip']; } ?>" /></p> 
		<p><label for="MarkerCity">Citta'</label> <input readonly="readonly" name="data[Marker][city]" style="background: #ececec;" type="text" id="MarkerCity" value="<?php if($marker){ echo $marker['Marker']['city']; } ?>" /></p>
		</div>
		<?php  /* echo $form->input('Marker.street', array('div' => 'input text', 'readonly' => true, 'before' => __('<div>Enter address or drag marker</div>',true), 'label' => __('Address',true)));
		echo $form->input('Marker.zip', array(
			'div' => 'input text', 'readonly' => true, 'maxlength'=>'5', 'label' => __('Zip',true)));
		echo $form->input('Marker.city', array(
			'div' => 'input text', 'readonly' => true, 'label' => __('City',true))); */	
		echo $form->input('Marker.description', array(
			'div' => 'input text', 'label' => __('Describe the situation',true)));	
		echo $form->hidden('Marker.category_id',array('value' => 32));
		echo '<div id="addFormMedia"><a class="showLink" href="#addFormMedia">'.__('Add some images or media?', true).'</a>';
		echo '<div id="addFormMediaDiv">';
		echo $this->element('attachments', array('plugin' => 'media', 'model' => 'Marker'));
		echo '</div>';
		echo '</div>';	

//		echo $form->input('Marker.status_id',array('label' => __('Status',true), 'disabled' => true));
?>
