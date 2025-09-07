<div class="pull-right">
	<a href="<?php echo site_url('patient/add'); ?>" class="btn btn-success">Add</a> 
</div>

<table class="table table-striped table-bordered">
    <tr>
		<th>Origid</th>
		<th>Private</th>
		<th>Lastname</th>
		<th>Firstname</th>
		<th>Middlename</th>
		<th>Prefix</th>
		<th>Suffix</th>
		<th>Ideographic</th>
		<th>Phonetic</th>
		<th>Birthdate</th>
		<th>Birthtime</th>
		<th>Sex</th>
		<th>Otherid</th>
		<th>Othername</th>
		<th>Ethnicgroup</th>
		<th>Institution</th>
		<th>Address</th>
		<th>Age</th>
		<th>Height</th>
		<th>Weight</th>
		<th>Occupation</th>
		<th>Lastaccess</th>
		<th>Patientmatchworklist</th>
		<th>Issuer</th>
		<th>Speciesdescr</th>
		<th>Sexneutered</th>
		<th>Breeddescr</th>
		<th>Respperson</th>
		<th>Resppersonrole</th>
		<th>Resppersonorg</th>
		<th>Charset</th>
		<th>Comments</th>
		<th>History</th>
		<th>Actions</th>
    </tr>
	<?php foreach($patient as $p){ ?>
    <tr>
		<td><?php echo $p['origid']; ?></td>
		<td><?php echo $p['private']; ?></td>
		<td><?php echo $p['lastname']; ?></td>
		<td><?php echo $p['firstname']; ?></td>
		<td><?php echo $p['middlename']; ?></td>
		<td><?php echo $p['prefix']; ?></td>
		<td><?php echo $p['suffix']; ?></td>
		<td><?php echo $p['ideographic']; ?></td>
		<td><?php echo $p['phonetic']; ?></td>
		<td><?php echo $p['birthdate']; ?></td>
		<td><?php echo $p['birthtime']; ?></td>
		<td><?php echo $p['sex']; ?></td>
		<td><?php echo $p['otherid']; ?></td>
		<td><?php echo $p['othername']; ?></td>
		<td><?php echo $p['ethnicgroup']; ?></td>
		<td><?php echo $p['institution']; ?></td>
		<td><?php echo $p['address']; ?></td>
		<td><?php echo $p['age']; ?></td>
		<td><?php echo $p['height']; ?></td>
		<td><?php echo $p['weight']; ?></td>
		<td><?php echo $p['occupation']; ?></td>
		<td><?php echo $p['lastaccess']; ?></td>
		<td><?php echo $p['patientmatchworklist']; ?></td>
		<td><?php echo $p['issuer']; ?></td>
		<td><?php echo $p['speciesdescr']; ?></td>
		<td><?php echo $p['sexneutered']; ?></td>
		<td><?php echo $p['breeddescr']; ?></td>
		<td><?php echo $p['respperson']; ?></td>
		<td><?php echo $p['resppersonrole']; ?></td>
		<td><?php echo $p['resppersonorg']; ?></td>
		<td><?php echo $p['charset']; ?></td>
		<td><?php echo $p['comments']; ?></td>
		<td><?php echo $p['history']; ?></td>
		<td>
            <a href="<?php echo site_url('patient/edit/'.$p['origid']); ?>" class="btn btn-info btn-xs">Edit</a> 
            <a href="<?php echo site_url('patient/remove/'.$p['origid']); ?>" class="btn btn-danger btn-xs">Delete</a>
        </td>
    </tr>
	<?php } ?>
</table>
