<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="card">
<?php if(!empty($update)): ?>
<div class="card-header">
	<h5 class="m-0"><strong><?php echo $update['name']; ?></strong></h5>
</div>
<div class="card-body">
	<p class="card-text">
        <p>Author: <strong><?php echo $update['author']['login']; ?></strong></p>
        <p>Version: <strong><?php echo $update['tag_name']; ?></strong></p>
        <p>Notes:</p>
        <p><?php echo \Michelf\MarkdownExtra::defaultTransform($update['body']); ?></p>
    </p>
	<a href="<?php echo $url; ?>" class="btn btn-primary"><?php echo $button; ?></a>
</div>
<?php else: ?>
<div class="card-body">
	<p class="card-text">
		<?php echo $heading; ?>
	</p>
</div>
<?php endif; ?>
</div>