<?php header('Content-type: text/html; charset=UTF-8') ;?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<head>
<?php echo $this->Html->charset();?>
<title><?php echo $title_for_layout?></title>
<link rel="shortcut icon" href="<?php echo $this->webroot ?>favicon.ico" type="image/x-icon"/>
<!-- Include external files and scripts here (See HTML helper for more info.) -->
<?php echo $scripts_for_layout ?>
</head>
<body>
<div id="wrapperPage">
<!-- If you'd like some sort of menu to 
show up on all of your views, include it here -->
<h1 class="hidden"><?php echo $title_for_layout?></h1>
<hr class="hidden" />
<div id="accessibility">
	<p><?php __('Skiplinks: Jump to ') ?><a href="#navigation"><?php __('Skiplinks: Navigation') ?></a> | <a href="#content"><?php __('Skiplinks: Content') ?></a> | <a href="#sidebar"><?php __('Skiplinks: Sidebar') ?></a> | <a href="#footer"><?php __('Skiplinks: Footer') ?></a></p>
</div>
<hr class="hidden" />
<div id="header">
<h3 class="hidden">Navigation</h3>

	<div id="home_app"><a href="http://www.mark-a-spot.de" title="<?php __('Home') ?>"><span><?php __('Home') ?></span></a></div>
</div>
<hr class="hidden"/>
<?php    
if ($session->check('Message.flash')) {
	echo $session->flash();  
}
if ($session->check('Message.auth')) {
	echo $session->flash('auth');  
}


echo $content_for_layout;
?>
<!-- wrapper-end -->
</div>
<div class="clear">&nbsp;</div>
<div id="footer_page">
	<div class="footer_inner left">
		<h3><?php __('Meta') ?></h3>
		<ul>
			<?php
			echo '<li>'.$html->link(__('Contact', true),array('controller' => 'seiten', 'action' => 'contact')).'</li>';
			echo '<li>'.$html->link(__('FAQ', true),array('controller' => 'seiten', 'action' => 'faq')).'</li>';
			echo '<li>'.$html->link(__('Imprint', true),array('controller' => 'seiten', 'action' => 'imprint')).'</li>';

			?>
		</ul>
	</div>
</div>
</body>
</html>
