<?php echo form_open('study/edit/'.$study['uuid'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="private" class="col-md-4 control-label">Private</label>
		<div class="col-md-8">
			<input type="checkbox" name="private" value="1" <?php echo ($study['private']==1 ? 'checked="checked"' : ''); ?> id='private' />
		</div>
	</div>
	<div class="form-group">
		<label for="matched" class="col-md-4 control-label">Matched</label>
		<div class="col-md-8">
			<input type="checkbox" name="matched" value="1" <?php echo ($study['matched']==1 ? 'checked="checked"' : ''); ?> id='matched' />
		</div>
	</div>
	<div class="form-group">
		<label for="id" class="col-md-4 control-label">ID</label>
		<div class="col-md-8">
			<input type="text" name="id" value="<?php echo ($this->input->post('id') ? $this->input->post('id') : $study['id']); ?>" class="form-control" id="id" />
		</div>
	</div>
	<div class="form-group">
		<label for="patientid" class="col-md-4 control-label">Patientid</label>
		<div class="col-md-8">
			<input type="text" name="patientid" value="<?php echo ($this->input->post('patientid') ? $this->input->post('patientid') : $study['patientid']); ?>" class="form-control" id="patientid" />
		</div>
	</div>
	<div class="form-group">
		<label for="studydate" class="col-md-4 control-label">Studydate</label>
		<div class="col-md-8">
			<input type="text" name="studydate" value="<?php echo ($this->input->post('studydate') ? $this->input->post('studydate') : $study['studydate']); ?>" class="form-control" id="studydate" />
		</div>
	</div>
	<div class="form-group">
		<label for="studytime" class="col-md-4 control-label">Studytime</label>
		<div class="col-md-8">
			<input type="text" name="studytime" value="<?php echo ($this->input->post('studytime') ? $this->input->post('studytime') : $study['studytime']); ?>" class="form-control" id="studytime" />
		</div>
	</div>
	<div class="form-group">
		<label for="accessionnum" class="col-md-4 control-label">Accessionnum</label>
		<div class="col-md-8">
			<input type="text" name="accessionnum" value="<?php echo ($this->input->post('accessionnum') ? $this->input->post('accessionnum') : $study['accessionnum']); ?>" class="form-control" id="accessionnum" />
		</div>
	</div>
	<div class="form-group">
		<label for="modalities" class="col-md-4 control-label">Modalities</label>
		<div class="col-md-8">
			<input type="text" name="modalities" value="<?php echo ($this->input->post('modalities') ? $this->input->post('modalities') : $study['modalities']); ?>" class="form-control" id="modalities" />
		</div>
	</div>
	<div class="form-group">
		<label for="referringphysician" class="col-md-4 control-label">Referringphysician</label>
		<div class="col-md-8">
			<input type="text" name="referringphysician" value="<?php echo ($this->input->post('referringphysician') ? $this->input->post('referringphysician') : $study['referringphysician']); ?>" class="form-control" id="referringphysician" />
		</div>
	</div>
	<div class="form-group">
		<label for="description" class="col-md-4 control-label">Description</label>
		<div class="col-md-8">
			<input type="text" name="description" value="<?php echo ($this->input->post('description') ? $this->input->post('description') : $study['description']); ?>" class="form-control" id="description" />
		</div>
	</div>
	<div class="form-group">
		<label for="readingphysician" class="col-md-4 control-label">Readingphysician</label>
		<div class="col-md-8">
			<input type="text" name="readingphysician" value="<?php echo ($this->input->post('readingphysician') ? $this->input->post('readingphysician') : $study['readingphysician']); ?>" class="form-control" id="readingphysician" />
		</div>
	</div>
	<div class="form-group">
		<label for="admittingdiagnoses" class="col-md-4 control-label">Admittingdiagnoses</label>
		<div class="col-md-8">
			<input type="text" name="admittingdiagnoses" value="<?php echo ($this->input->post('admittingdiagnoses') ? $this->input->post('admittingdiagnoses') : $study['admittingdiagnoses']); ?>" class="form-control" id="admittingdiagnoses" />
		</div>
	</div>
	<div class="form-group">
		<label for="interpretationauthor" class="col-md-4 control-label">Interpretationauthor</label>
		<div class="col-md-8">
			<input type="text" name="interpretationauthor" value="<?php echo ($this->input->post('interpretationauthor') ? $this->input->post('interpretationauthor') : $study['interpretationauthor']); ?>" class="form-control" id="interpretationauthor" />
		</div>
	</div>
	<div class="form-group">
		<label for="received" class="col-md-4 control-label">Received</label>
		<div class="col-md-8">
			<input type="text" name="received" value="<?php echo ($this->input->post('received') ? $this->input->post('received') : $study['received']); ?>" class="form-control" id="received" />
		</div>
	</div>
	<div class="form-group">
		<label for="sourceae" class="col-md-4 control-label">Sourceae</label>
		<div class="col-md-8">
			<input type="text" name="sourceae" value="<?php echo ($this->input->post('sourceae') ? $this->input->post('sourceae') : $study['sourceae']); ?>" class="form-control" id="sourceae" />
		</div>
	</div>
	<div class="form-group">
		<label for="reviewed" class="col-md-4 control-label">Reviewed</label>
		<div class="col-md-8">
			<input type="text" name="reviewed" value="<?php echo ($this->input->post('reviewed') ? $this->input->post('reviewed') : $study['reviewed']); ?>" class="form-control" id="reviewed" />
		</div>
	</div>
	<div class="form-group">
		<label for="verified" class="col-md-4 control-label">Verified</label>
		<div class="col-md-8">
			<input type="text" name="verified" value="<?php echo ($this->input->post('verified') ? $this->input->post('verified') : $study['verified']); ?>" class="form-control" id="verified" />
		</div>
	</div>
	<div class="form-group">
		<label for="compressed" class="col-md-4 control-label">Compressed</label>
		<div class="col-md-8">
			<input type="text" name="compressed" value="<?php echo ($this->input->post('compressed') ? $this->input->post('compressed') : $study['compressed']); ?>" class="form-control" id="compressed" />
		</div>
	</div>
	<div class="form-group">
		<label for="studymatchworklist" class="col-md-4 control-label">Studymatchworklist</label>
		<div class="col-md-8">
			<input type="text" name="studymatchworklist" value="<?php echo ($this->input->post('studymatchworklist') ? $this->input->post('studymatchworklist') : $study['studymatchworklist']); ?>" class="form-control" id="studymatchworklist" />
		</div>
	</div>
	<div class="form-group">
		<label for="requestingphysician" class="col-md-4 control-label">Requestingphysician</label>
		<div class="col-md-8">
			<input type="text" name="requestingphysician" value="<?php echo ($this->input->post('requestingphysician') ? $this->input->post('requestingphysician') : $study['requestingphysician']); ?>" class="form-control" id="requestingphysician" />
		</div>
	</div>
	<div class="form-group">
		<label for="updated" class="col-md-4 control-label">Updated</label>
		<div class="col-md-8">
			<input type="text" name="updated" value="<?php echo ($this->input->post('updated') ? $this->input->post('updated') : $study['updated']); ?>" class="form-control" id="updated" />
		</div>
	</div>
	<div class="form-group">
		<label for="REVIEWED_USER_ID" class="col-md-4 control-label">REVIEWED USER ID</label>
		<div class="col-md-8">
			<input type="text" name="REVIEWED_USER_ID" value="<?php echo ($this->input->post('REVIEWED_USER_ID') ? $this->input->post('REVIEWED_USER_ID') : $study['REVIEWED_USER_ID']); ?>" class="form-control" id="REVIEWED_USER_ID" />
		</div>
	</div>
	<div class="form-group">
		<label for="REVIEWED_DATE" class="col-md-4 control-label">REVIEWED DATE</label>
		<div class="col-md-8">
			<input type="text" name="REVIEWED_DATE" value="<?php echo ($this->input->post('REVIEWED_DATE') ? $this->input->post('REVIEWED_DATE') : $study['REVIEWED_DATE']); ?>" class="form-control" id="REVIEWED_DATE" />
		</div>
	</div>
	<div class="form-group">
		<label for="mirthprocessed" class="col-md-4 control-label">Mirthprocessed</label>
		<div class="col-md-8">
			<input type="text" name="mirthprocessed" value="<?php echo ($this->input->post('mirthprocessed') ? $this->input->post('mirthprocessed') : $study['mirthprocessed']); ?>" class="form-control" id="mirthprocessed" />
		</div>
	</div>
	<div class="form-group">
		<label for="is_critical" class="col-md-4 control-label">Is Critical</label>
		<div class="col-md-8">
			<input type="text" name="is_critical" value="<?php echo ($this->input->post('is_critical') ? $this->input->post('is_critical') : $study['is_critical']); ?>" class="form-control" id="is_critical" />
		</div>
	</div>
	<div class="form-group">
		<label for="critical_date" class="col-md-4 control-label">Critical Date</label>
		<div class="col-md-8">
			<input type="text" name="critical_date" value="<?php echo ($this->input->post('critical_date') ? $this->input->post('critical_date') : $study['critical_date']); ?>" class="form-control" id="critical_date" />
		</div>
	</div>
	<div class="form-group">
		<label for="mailed_date" class="col-md-4 control-label">Mailed Date</label>
		<div class="col-md-8">
			<input type="text" name="mailed_date" value="<?php echo ($this->input->post('mailed_date') ? $this->input->post('mailed_date') : $study['mailed_date']); ?>" class="form-control" id="mailed_date" />
		</div>
	</div>
	<div class="form-group">
		<label for="has_attached_orders" class="col-md-4 control-label">Has Attached Orders</label>
		<div class="col-md-8">
			<input type="text" name="has_attached_orders" value="<?php echo ($this->input->post('has_attached_orders') ? $this->input->post('has_attached_orders') : $study['has_attached_orders']); ?>" class="form-control" id="has_attached_orders" />
		</div>
	</div>
	<div class="form-group">
		<label for="text" class="col-md-4 control-label">Text</label>
		<div class="col-md-8">
			<input type="text" name="text" value="<?php echo ($this->input->post('text') ? $this->input->post('text') : $study['text']); ?>" class="form-control" id="text" />
		</div>
	</div>
	<div class="form-group">
		<label for="notedate" class="col-md-4 control-label">Notedate</label>
		<div class="col-md-8">
			<input type="text" name="notedate" value="<?php echo ($this->input->post('notedate') ? $this->input->post('notedate') : $study['notedate']); ?>" class="form-control" id="notedate" />
		</div>
	</div>
	<div class="form-group">
		<label for="username" class="col-md-4 control-label">Username</label>
		<div class="col-md-8">
			<input type="text" name="username" value="<?php echo ($this->input->post('username') ? $this->input->post('username') : $study['username']); ?>" class="form-control" id="username" />
		</div>
	</div>
	<div class="form-group">
		<label for="has_tech_notes" class="col-md-4 control-label">Has Tech Notes</label>
		<div class="col-md-8">
			<input type="text" name="has_tech_notes" value="<?php echo ($this->input->post('has_tech_notes') ? $this->input->post('has_tech_notes') : $study['has_tech_notes']); ?>" class="form-control" id="has_tech_notes" />
		</div>
	</div>
	<div class="form-group">
		<label for="processedITR" class="col-md-4 control-label">ProcessedITR</label>
		<div class="col-md-8">
			<input type="text" name="processedITR" value="<?php echo ($this->input->post('processedITR') ? $this->input->post('processedITR') : $study['processedITR']); ?>" class="form-control" id="processedITR" />
		</div>
	</div>
	<div class="form-group">
		<label for="fax_status" class="col-md-4 control-label">Fax Status</label>
		<div class="col-md-8">
			<input type="text" name="fax_status" value="<?php echo ($this->input->post('fax_status') ? $this->input->post('fax_status') : $study['fax_status']); ?>" class="form-control" id="fax_status" />
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>