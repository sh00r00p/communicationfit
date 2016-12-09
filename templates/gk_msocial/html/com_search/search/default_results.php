<?php

// no direct access
defined('_JEXEC') or die;

?>

<dl class="search-results<?php echo $this->pageclass_sfx; ?>">
	<?php foreach($this->results as $result) : ?>
	<dt class="result-title"> 
		<?php if ($result->href) :?>
		<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>> <?php echo $this->escape($result->title);?> </a>
		<?php else:?>
		<?php echo $this->escape($result->title);?>
		<?php endif; ?>
	</dt>
	<?php if ($this->params->get('show_date') && $result->created) : ?>
	<dd class="result-created<?php echo $this->pageclass_sfx; ?>"> <?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?> </dd>
	<?php endif; ?>
	<?php endforeach; ?>
</dl>
<?php echo str_replace('</ul>', '<li class="counter">'.$this->pagination->getPagesCounter().'</li>', $this->pagination->getPagesLinks()); ?>