<div class="text-center">
	<h2>Welcome to PDFs to DCMs conversion tool.</h2>
</div>
<div class="text-center">
	<h3><u>QUEUED PDF FILES(<?php echo count($pdfs); ?>)</u></h3>
	
</div>
<div class="pull-right">
	<a href="<?php echo site_url('dashboard/convert'); ?>" class="btn btn-success">Convert All</a> 
</div>
<br>
<br>
<table class="table table-striped table-bordered">
    <tr>
		<th>File</th>
		<th>Study Info</th>
		<th>Patient Info</th>
		<!-- <th>Actions</th> -->
    </tr>
	<?php foreach($pdfs as $pdf){ ?>
    <tr>
		<td><?php echo $pdf['file']; ?></td>
		<!-- <td><?php //var_dump($pdf['study']); ?></td>
		<td><?php //var_dump($pdf['patient']); ?></td> -->
		<td><?php echo $ctrl->getDisplayString($pdf['study']); ?></td>
		<td><?php echo $ctrl->getDisplayString($pdf['patient']); ?></td>
		<!-- <td>
            <a href="#" class="btn btn-info btn-xs">CONVERT</a>
        </td> -->
    </tr>
	<?php } ?>
</table>