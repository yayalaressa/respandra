<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="card">
<div class="card-header">
	<h5 class="m-0"><?php echo $dashboard->heading; ?></h5>
</div>
<div class="card-body">
	<h6 class="card-title"><?php echo $dashboard->title; ?></h6>
	<p class="card-text"><?php echo $dashboard->description; ?></p>
	<a href="#" class="btn btn-primary"><?php echo $dashboard->url; ?></a>
</div>
</div>