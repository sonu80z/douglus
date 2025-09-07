<?php echo form_open('patient/add',array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="private" class="col-md-4 control-label">Private</label>
		<div class="col-md-8">
			<input type="checkbox" name="private" value="1" id="private" />
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-md-4 control-label">Lastname</label>
		<div class="col-md-8">
			<input type="text" name="lastname" value="<?php echo $this->input->post('lastname'); ?>" class="form-control" id="lastname" />
		</div>
	</div>
	<div class="form-group">
		<label for="firstname" class="col-md-4 control-label">Firstname</label>
		<div class="col-md-8">
			<input type="text" name="firstname" value="<?php echo $this->input->post('firstname'); ?>" class="form-control" id="firstname" />
		</div>
	</div>
	<div class="form-group">
		<label for="middlename" class="col-md-4 control-label">Middlename</label>
		<div class="col-md-8">
			<input type="text" name="middlename" value="<?php echo $this->input->post('middlename'); ?>" class="form-control" id="middlename" />
		</div>
	</div>
	<div class="form-group">
		<label for="prefix" class="col-md-4 control-label">Prefix</label>
		<div class="col-md-8">
			<input type="text" name="prefix" value="<?php echo $this->input->post('prefix'); ?>" class="form-control" id="prefix" />
		</div>
	</div>
	<div class="form-group">
		<label for="suffix" class="col-md-4 control-label">Suffix</label>
		<div class="col-md-8">
			<input type="text" name="suffix" value="<?php echo $this->input->post('suffix'); ?>" class="form-control" id="suffix" />
		</div>
	</div>
	<div class="form-group">
		<label for="ideographic" class="col-md-4 control-label">Ideographic</label>
		<div class="col-md-8">
			<input type="text" name="ideographic" value="<?php echo $this->input->post('ideographic'); ?>" class="form-control" id="ideographic" />
		</div>
	</div>
	<div class="form-group">
		<label for="phonetic" class="col-md-4 control-label">Phonetic</label>
		<div class="col-md-8">
			<input type="text" name="phonetic" value="<?php echo $this->input->post('phonetic'); ?>" class="form-control" id="phonetic" />
		</div>
	</div>
	<div class="form-group">
		<label for="birthdate" class="col-md-4 control-label">Birthdate</label>
		<div class="col-md-8">
			<input type="text" name="birthdate" value="<?php echo $this->input->post('birthdate'); ?>" class="form-control" id="birthdate" />
		</div>
	</div>
	<div class="form-group">
		<label for="birthtime" class="col-md-4 control-label">Birthtime</label>
		<div class="col-md-8">
			<input type="text" name="birthtime" value="<?php echo $this->input->post('birthtime'); ?>" class="form-control" id="birthtime" />
		</div>
	</div>
	<div class="form-group">
		<label for="sex" class="col-md-4 control-label">Sex</label>
		<div class="col-md-8">
			<input type="text" name="sex" value="<?php echo $this->input->post('sex'); ?>" class="form-control" id="sex" />
		</div>
	</div>
	<div class="form-group">
		<label for="otherid" class="col-md-4 control-label">Otherid</label>
		<div class="col-md-8">
			<input type="text" name="otherid" value="<?php echo $this->input->post('otherid'); ?>" class="form-control" id="otherid" />
		</div>
	</div>
	<div class="form-group">
		<label for="othername" class="col-md-4 control-label">Othername</label>
		<div class="col-md-8">
			<input type="text" name="othername" value="<?php echo $this->input->post('othername'); ?>" class="form-control" id="othername" />
		</div>
	</div>
	<div class="form-group">
		<label for="ethnicgroup" class="col-md-4 control-label">Ethnicgroup</label>
		<div class="col-md-8">
			<input type="text" name="ethnicgroup" value="<?php echo $this->input->post('ethnicgroup'); ?>" class="form-control" id="ethnicgroup" />
		</div>
	</div>
	<div class="form-group">
		<label for="institution" class="col-md-4 control-label">Institution</label>
		<div class="col-md-8">
			<input type="text" name="institution" value="<?php echo $this->input->post('institution'); ?>" class="form-control" id="institution" />
		</div>
	</div>
	<div class="form-group">
		<label for="address" class="col-md-4 control-label">Address</label>
		<div class="col-md-8">
			<input type="text" name="address" value="<?php echo $this->input->post('address'); ?>" class="form-control" id="address" />
		</div>
	</div>
	<div class="form-group">
		<label for="age" class="col-md-4 control-label">Age</label>
		<div class="col-md-8">
			<input type="text" name="age" value="<?php echo $this->input->post('age'); ?>" class="form-control" id="age" />
		</div>
	</div>
	<div class="form-group">
		<label for="height" class="col-md-4 control-label">Height</label>
		<div class="col-md-8">
			<input type="text" name="height" value="<?php echo $this->input->post('height'); ?>" class="form-control" id="height" />
		</div>
	</div>
	<div class="form-group">
		<label for="weight" class="col-md-4 control-label">Weight</label>
		<div class="col-md-8">
			<input type="text" name="weight" value="<?php echo $this->input->post('weight'); ?>" class="form-control" id="weight" />
		</div>
	</div>
	<div class="form-group">
		<label for="occupation" class="col-md-4 control-label">Occupation</label>
		<div class="col-md-8">
			<input type="text" name="occupation" value="<?php echo $this->input->post('occupation'); ?>" class="form-control" id="occupation" />
		</div>
	</div>
	<div class="form-group">
		<label for="lastaccess" class="col-md-4 control-label">Lastaccess</label>
		<div class="col-md-8">
			<input type="text" name="lastaccess" value="<?php echo $this->input->post('lastaccess'); ?>" class="form-control" id="lastaccess" />
		</div>
	</div>
	<div class="form-group">
		<label for="patientmatchworklist" class="col-md-4 control-label">Patientmatchworklist</label>
		<div class="col-md-8">
			<input type="text" name="patientmatchworklist" value="<?php echo $this->input->post('patientmatchworklist'); ?>" class="form-control" id="patientmatchworklist" />
		</div>
	</div>
	<div class="form-group">
		<label for="issuer" class="col-md-4 control-label">Issuer</label>
		<div class="col-md-8">
			<input type="text" name="issuer" value="<?php echo $this->input->post('issuer'); ?>" class="form-control" id="issuer" />
		</div>
	</div>
	<div class="form-group">
		<label for="speciesdescr" class="col-md-4 control-label">Speciesdescr</label>
		<div class="col-md-8">
			<input type="text" name="speciesdescr" value="<?php echo $this->input->post('speciesdescr'); ?>" class="form-control" id="speciesdescr" />
		</div>
	</div>
	<div class="form-group">
		<label for="sexneutered" class="col-md-4 control-label">Sexneutered</label>
		<div class="col-md-8">
			<input type="text" name="sexneutered" value="<?php echo $this->input->post('sexneutered'); ?>" class="form-control" id="sexneutered" />
		</div>
	</div>
	<div class="form-group">
		<label for="breeddescr" class="col-md-4 control-label">Breeddescr</label>
		<div class="col-md-8">
			<input type="text" name="breeddescr" value="<?php echo $this->input->post('breeddescr'); ?>" class="form-control" id="breeddescr" />
		</div>
	</div>
	<div class="form-group">
		<label for="respperson" class="col-md-4 control-label">Respperson</label>
		<div class="col-md-8">
			<input type="text" name="respperson" value="<?php echo $this->input->post('respperson'); ?>" class="form-control" id="respperson" />
		</div>
	</div>
	<div class="form-group">
		<label for="resppersonrole" class="col-md-4 control-label">Resppersonrole</label>
		<div class="col-md-8">
			<input type="text" name="resppersonrole" value="<?php echo $this->input->post('resppersonrole'); ?>" class="form-control" id="resppersonrole" />
		</div>
	</div>
	<div class="form-group">
		<label for="resppersonorg" class="col-md-4 control-label">Resppersonorg</label>
		<div class="col-md-8">
			<input type="text" name="resppersonorg" value="<?php echo $this->input->post('resppersonorg'); ?>" class="form-control" id="resppersonorg" />
		</div>
	</div>
	<div class="form-group">
		<label for="charset" class="col-md-4 control-label">Charset</label>
		<div class="col-md-8">
			<input type="text" name="charset" value="<?php echo $this->input->post('charset'); ?>" class="form-control" id="charset" />
		</div>
	</div>
	<div class="form-group">
		<label for="comments" class="col-md-4 control-label">Comments</label>
		<div class="col-md-8">
			<textarea name="comments" class="form-control" id="comments"><?php echo $this->input->post('comments'); ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="history" class="col-md-4 control-label">History</label>
		<div class="col-md-8">
			<textarea name="history" class="form-control" id="history"><?php echo $this->input->post('history'); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>

<?php echo form_close(); ?>