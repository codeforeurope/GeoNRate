<?php
class Comment extends AppModel {

	var $name = 'Comment';
	var $validate = array(
			'name'		=> array('rule'=>array('minLength', '1'), 'required'=>true, 'message'=> 'Please enter your name'),
			'comment'	=> array('rule'=>array('minLength', '1'), 'required'=>true, 'message'=> 'Please enter a commment'),
			'email'		=> array('rule'=>'email', 'required'=>true, 'message'=> 'We need a valid e-mail address')
	);
	//For search plugin
	/*
	var $actsAs = array(
			'Search.Searchable' => array(
					'fields' => array('comment')
			)
	);*/
	//The Associations below have been created with all possible keys, those that are not needed can be removed
	var $belongsTo = array(
			'Marker' => array(
					'className' => 'Marker',
					'foreignKey' => 'marker_id',
					'conditions' => '',
					'fields' => '',
					'order' => ''
			)
	);

	/**
	 * get all comments of a specific marker
	 *
	 */

	function getComments($id) {

		$commentSum= $this->find('count', array(
				'conditions' => array(
						'marker_id' => $id,
						'status >=' => 1 ,
				)
		)
		);
		return $commentSum;
	}

}
?>
