<div class="pull-right">
	<a href="<?php echo site_url('study/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Uuid</th>
		<th>Private</th>
		<th>Matched</th>
		<th>ID</th>
		<th>Patientid</th>
		<th>Studydate</th>
		<th>Studytime</th>
		<th>Accessionnum</th>
		<th>Modalities</th>
		<th>Referringphysician</th>
		<th>Description</th>
		<th>Readingphysician</th>
		<th>Admittingdiagnoses</th>
		<th>Interpretationauthor</th>
		<th>Received</th>
		<th>Sourceae</th>
		<th>Reviewed</th>
		<th>Verified</th>
		<th>Compressed</th>
		<th>Studymatchworklist</th>
		<th>Requestingphysician</th>
		<th>Updated</th>
		<th>REVIEWED USER ID</th>
		<th>REVIEWED DATE</th>
		<th>Mirthprocessed</th>
		<th>Is Critical</th>
		<th>Critical Date</th>
		<th>Mailed Date</th>
		<th>Has Attached Orders</th>
		<th>Text</th>
		<th>Notedate</th>
		<th>Username</th>
		<th>Has Tech Notes</th>
		<th>ProcessedITR</th>
		<th>Fax Status</th>
		<th>Actions</th>
    </tr>
	<?php foreach($study as $s){ ?>
    <tr>
		<td><?php echo $s['uuid']; ?></td>
		<td><?php echo $s['private']; ?></td>
		<td><?php echo $s['matched']; ?></td>
		<td><?php echo $s['id']; ?></td>
		<td><?php echo $s['patientid']; ?></td>
		<td><?php echo $s['studydate']; ?></td>
		<td><?php echo $s['studytime']; ?></td>
		<td><?php echo $s['accessionnum']; ?></td>
		<td><?php echo $s['modalities']; ?></td>
		<td><?php echo $s['referringphysician']; ?></td>
		<td><?php echo $s['description']; ?></td>
		<td><?php echo $s['readingphysician']; ?></td>
		<td><?php echo $s['admittingdiagnoses']; ?></td>
		<td><?php echo $s['interpretationauthor']; ?></td>
		<td><?php echo $s['received']; ?></td>
		<td><?php echo $s['sourceae']; ?></td>
		<td><?php echo $s['reviewed']; ?></td>
		<td><?php echo $s['verified']; ?></td>
		<td><?php echo $s['compressed']; ?></td>
		<td><?php echo $s['studymatchworklist']; ?></td>
		<td><?php echo $s['requestingphysician']; ?></td>
		<td><?php echo $s['updated']; ?></td>
		<td><?php echo $s['REVIEWED_USER_ID']; ?></td>
		<td><?php echo $s['REVIEWED_DATE']; ?></td>
		<td><?php echo $s['mirthprocessed']; ?></td>
		<td><?php echo $s['is_critical']; ?></td>
		<td><?php echo $s['critical_date']; ?></td>
		<td><?php echo $s['mailed_date']; ?></td>
		<td><?php echo $s['has_attached_orders']; ?></td>
		<td><?php echo $s['text']; ?></td>
		<td><?php echo $s['notedate']; ?></td>
		<td><?php echo $s['username']; ?></td>
		<td><?php echo $s['has_tech_notes']; ?></td>
		<td><?php echo $s['processedITR']; ?></td>
		<td><?php echo $s['fax_status']; ?></td>
		<td>
            <a href="<?php echo site_url('study/edit/'.$s['uuid']); ?>" class="btn btn-info btn-xs">Edit</a> 
            <a href="<?php echo site_url('study/remove/'.$s['uuid']); ?>" class="btn btn-danger btn-xs">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>
