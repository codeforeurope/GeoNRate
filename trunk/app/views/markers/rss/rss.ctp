<?php header('Content-type: text/xml'); ?> 
<?php
function transform_rss($markers) {
    return array(
      'title' => $markers['Marker']['street'].', '.$markers['Marker']['zip'].' '.$markers['Marker']['city'],
      'link'  => array('action' => 'view', $markers['Marker']['id']),
      'guid'  => array('action' => 'view', $markers['Marker']['id']),
      'description' => 'Evento: '.$markers['Transaction'][0]['name'].'<br/>Note: '.$markers['Marker']['description'],
      'author' => "Provincia di Roma - Mark-a-Spot",
      'pubDate' => $markers['Marker']['modified']
    );
  }
  echo $rss->items($markers, 'transform_rss');   
?>
