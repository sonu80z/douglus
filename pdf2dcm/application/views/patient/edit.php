<?php echo form_open('patient/edit/'.$patient['origid'],array("class"=>"form-horizontal")); ?>

	<div class="form-group">
		<label for="private" class="col-md-4 control-label">Private</label>
		<div class="col-md-8">
			<input type="checkbox" name="private" value="1" <?php echo ($patient['private']==1 ? 'checked="checked"' : ''); ?> id='private' />
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-md-4 control-label">Lastname</label>
		<div class="col-md-8">
			<input type="text" name="lastname" value="<?php echo ($this->input->post('lastname') ? $this->input->post('lastname') : $patient['lastname']); ?>" class="form-control" id="lastname" />
		</div>
	</div>
	<div class="form-group">
		<label for="firstname" class="col-md-4 control-label">Firstname</label>
		<div class="col-md-8">
			<input type="text" name="firstname" value="<?php echo ($this->input->post('firstname') ? $this->input->post('firstname') : $patient['firstname']); ?>" class="form-control" id="firstname" />
		</div>
	</div>
	<div class="form-group">
		<label for="middlename" class="col-md-4 control-label">Middlename</label>
		<div class="col-md-8">
			<input type="text" name="middlename" value="<?php echo ($this->input->post('middlename') ? $this->input->post('middlename') : $patient['middlename']); ?>" class="form-control" id="middlename" />
		</div>
	</div>
	<div class="form-group">
		<label for="prefix" class="col-md-4 control-label">Prefix</label>
		<div class="col-md-8">
			<input type="text" name="prefix" value="<?php echo ($this->input->post('prefix') ? $this->input->post('prefix') : $patient['prefix']); ?>" class="form-control" id="prefix" />
		</div>
	</div>
	<div class="form-group">
		<label for="suffix" class="col-md-4 control-label">Suffix</label>
		<div class="col-md-8">
			<input type="text" name="suffix" value="<?php echo ($this->input->post('suffix') ? $this->input->post('suffix') : $patient['suffix']); ?>" class="form-control" id="suffix" />
		</div>
	</div>
	<div class="form-group">
		<label for="ideographic" class="col-md-4 control-label">Ideographic</label>
		<div class="col-md-8">
			<input type="text" name="ideographic" value="<?php echo ($this->input->post('ideographic') ? $this->input->post('ideographic') : $patient['ideographic']); ?>" class="form-control" id="ideographic" />
		</div>
	</div>
	<div class="form-group">
		<label for="phonetic" class="col-md-4 control-label">Phonetic</label>
		<div class="col-md-8">
			<input type="text" name="phonetic" value="<?php echo ($this->input->post('phonetic') ? $this->input->post('phonetic') : $patient['phonetic']); ?>" class="form-control" id="phonetic" />
		</div>
	</div>
	<div class="form-group">
		<label for="birthdate" class="col-md-4 control-label">Birthdate</label>
		<div class="col-md-8">
			<input type="text" name="birthdate" value="<?php echo ($this->input->post('birthdate') ? $this->input->post('birthdate') : $patient['birthdate']); ?>" class="form-control" id="birthdate" />
		</div>
	</div>
	<div class="form-group">
		<label for="birthtime" class="col-md-4 control-label">Birthtime</label>
		<div class="col-md-8">
			<input type="text" name="birthtime" value="<?php echo ($this->input->post('birthtime') ? $this->input->post('birthtime') : $patient['birthtime']); ?>" class="form-control" id="birthtime" />
		</div>
	</div>
	<div class="form-group">
		<label for="sex" class="col-md-4 control-label">Sex</label>
		<div class="col-md-8">
			<input type="text" name="sex" value="<?php echo ($this->input->post('sex') ? $this->input->post('sex') : $patient['sex']); ?>" class="form-control" id="sex" />
		</div>
	</div>
	<div class="form-group">
		<label for="otherid" class="col-md-4 control-label">Otherid</label>
		<div class="col-md-8">
			<input type="text" name="otherid" value="<?php echo ($this->input->post('otherid') ? $this->input->post('otherid') : $patient['otherid']); ?>" class="form-control" id="otherid" />
		</div>
	</div>
	<div class="form-group">
		<label for="othername" class="col-md-4 control-label">Othername</label>
		<div class="col-md-8">
			<input type="text" name="othername" value="<?php echo ($this->input->post('othername') ? $this->input->post('othername') : $patient['othername']); ?>" class="form-control" id="othername" />
		</div>
	</div>
	<div class="form-group">
		<label for="ethnicgroup" class="col-md-4 control-label">Ethnicgroup</label>
		<div class="col-md-8">
			<input type="text" name="ethnicgroup" value="<?php echo ($this->input->post('ethnicgroup') ? $this->input->post('ethnicgroup') : $patient['ethnicgroup']); ?>" class="form-control" id="ethnicgroup" />
		</div>
	</div>
	<div class="form-group">
		<label for="institution" class="col-md-4 control-label">Institution</label>
		<div class="col-md-8">
			<input type="text" name="institution" value="<?php echo ($this->input->post('institution') ? $this->input->post('institution') : $patient['institution']); ?>" class="form-control" id="institution" />
		</div>
	</div>
	<div class="form-group">
		<label for="address" class="col-md-4 control-label">Address</label>
		<div class="col-md-8">
			<input type="text" name="address" value="<?php echo ($this->input->post('address') ? $this->input->post('address') : $patient['address']); ?>" class="form-control" id="address" />
		</div>
	</div>
	<div class="form-group">
		<label for="age" class="col-md-4 control-label">Age</label>
		<div class="col-md-8">
			<input type="text" name="age" value="<?php echo ($this->input->post('age') ? $this->input->post('age') : $patient['age']); ?>" class="form-control" id="age" />
		</div>
	</div>
	<div class="form-group">
		<label for="height" class="col-md-4 control-label">Height</label>
		<div class="col-md-8">
			<input type="text" name="height" value="<?php echo ($this->input->post('height') ? $this->input->post('height') : $patient['height']); ?>" class="form-control" id="height" />
		</div>
	</div>
	<div class="form-group">
		<label for="weight" class="col-md-4 control-label">Weight</label>
		<div class="col-md-8">
			<input type="text" name="weight" value="<?php echo ($this->input->post('weight') ? $this->input->post('weight') : $patient['weight']); ?>" class="form-control" id="weight" />
		</div>
	</div>
	<div class="form-group">
		<label for="occupation" class="col-md-4 control-label">Occupation</label>
		<div class="col-md-8">
			<input type="text" name="occupation" value="<?php echo ($this->input->post('occupation') ? $this->input->post('occupation') : $patient['occupation']); ?>" class="form-control" id="occupation" />
		</div>
	</div>
	<div class="form-group">
		<label for="lastaccess" class="col-md-4 control-label">Lastaccess</label>
		<div class="col-md-8">
			<input type="text" name="lastaccess" value="<?php echo ($this->input->post('lastaccess') ? $this->input->post('lastaccess') : $patient['lastaccess']); ?>" class="form-control" id="lastaccess" />
		</div>
	</div>
	<div class="form-group">
		<label for="patientmatchworklist" class="col-md-4 control-label">Patientmatchworklist</label>
		<div class="col-md-8">
			<input type="text" name="patientmatchworklist" value="<?php echo ($this->input->post('patientmatchworklist') ? $this->input->post('patientmatchworklist') : $patient['patientmatchworklist']); ?>" class="form-control" id="patientmatchworklist" />
		</div>
	</div>
	<div class="form-group">
		<label for="issuer" class="col-md-4 control-label">Issuer</label>
		<div class="col-md-8">
			<input type="text" name="issuer" value="<?php echo ($this->input->post('issuer') ? $this->input->post('issuer') : $patient['issuer']); ?>" class="form-control" id="issuer" />
		</div>
	</div>
	<div class="form-group">
		<label for="speciesdescr" class="col-md-4 control-label">Speciesdescr</label>
		<div class="col-md-8">
			<input type="text" name="speciesdescr" value="<?php echo ($this->input->post('speciesdescr') ? $this->input->post('speciesdescr') : $patient['speciesdescr']); ?>" class="form-control" id="speciesdescr" />
		</div>
	</div>
	<div class="form-group">
		<label for="sexneutered" class="col-md-4 control-label">Sexneutered</label>
		<div class="col-md-8">
			<input type="text" name="sexneutered" value="<?php echo ($this->input->post('sexneutered') ? $this->input->post('sexneutered') : $patient['sexneutered']); ?>" class="form-control" id="sexneutered" />
		</div>
	</div>
	<div class="form-group">
		<label for="breeddescr" class="col-md-4 control-label">Breeddescr</label>
		<div class="col-md-8">
			<input type="text" name="breeddescr" value="<?php echo ($this->input->post('breeddescr') ? $this->input->post('breeddescr') : $patient['breeddescr']); ?>" class="form-control" id="breeddescr" />
		</div>
	</div>
	<div class="form-group">
		<label for="respperson" class="col-md-4 control-label">Respperson</label>
		<div class="col-md-8">
			<input type="text" name="respperson" value="<?php echo ($this->input->post('respperson') ? $this->input->post('respperson') : $patient['respperson']); ?>" class="form-control" id="respperson" />
		</div>
	</div>
	<div class="form-group">
		<label for="resppersonrole" class="col-md-4 control-label">Resppersonrole</label>
		<div class="col-md-8">
			<input type="text" name="resppersonrole" value="<?php echo ($this->input->post('resppersonrole') ? $this->input->post('resppersonrole') : $patient['resppersonrole']); ?>" class="form-control" id="resppersonrole" />
		</div>
	</div>
	<div class="form-group">
		<label for="resppersonorg" class="col-md-4 control-label">Resppersonorg</label>
		<div class="col-md-8">
			<input type="text" name="resppersonorg" value="<?php echo ($this->input->post('resppersonorg') ? $this->input->post('resppersonorg') : $patient['resppersonorg']); ?>" class="form-control" id="resppersonorg" />
		</div>
	</div>
	<div class="form-group">
		<label for="charset" class="col-md-4 control-label">Charset</label>
		<div class="col-md-8">
			<input type="text" name="charset" value="<?php echo ($this->input->post('charset') ? $this->input->post('charset') : $patient['charset']); ?>" class="form-control" id="charset" />
		</div>
	</div>
	<div class="form-group">
		<label for="comments" class="col-md-4 control-label">Comments</label>
		<div class="col-md-8">
			<textarea name="comments" class="form-control" id="comments"><?php echo ($this->input->post('comments') ? $this->input->post('comments') : $patient['comments']); ?></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="history" class="col-md-4 control-label">History</label>
		<div class="col-md-8">
			<textarea name="history" class="form-control" id="history"><?php echo ($this->input->post('history') ? $this->input->post('history') : $patient['history']); ?></textarea>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-8">
			<button type="submit" class="btn btn-success">Save</button>
        </div>
	</div>
	
<?php echo form_close(); ?>