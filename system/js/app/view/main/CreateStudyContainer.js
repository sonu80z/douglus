Ext.define('MdiApp.view.main.CreateStudyContainer',{
	extend : 'Ext.form.Panel',
	show : function(id){
		this.callParent(arguments)
	},
	defineItems : function(config){
		var self=this
		config=config||{}
		config.defaults={
			//frame : true
		}
		config.scroll=true
			
		config.frame=true
		//this.callParent(arguments);
	},
	initComponent : function(){
		var self=this
		var buttons=[];
		var sendBtn={
			xtype : 'button',
			text : 'Save',
			handler : function(){
				{
				var form = self.getForm();
				//var params = form.getValues();
				var params={};
				params.control = 'StudyControl';
				params.method = 'Add';
				form.submit({
					url : 'system/dispatch.php',
					params : params,
					waitMsg : 'Wait while form submitting',
					waitTitle : 'Wait...',
					success : function(){
						var parent=self.findParentByType('studymainview')
						parent.setActiveItem(0)
						Ext.getCmp('studyGrid').getStore().reload()
					},
					failure : function(form,action){
						if (action.failureType === Ext.form.action.Action.CONNECT_FAILURE) {
							Ext.Msg.error('Status:'+action.response.status+': '+action.response.statusText);
						}
						if (action.failureType === Ext.form.action.Action.SERVER_INVALID){
							// server responded with success = false
							Ext.Msg.error(action.result.errormsg);
						}
						if (action.failureType === Ext.form.action.Action.CLIENT_INVALID){
							// server responded with success = false
							Ext.Msg.error('Some fields are invalid');
						}
					}
				}); 
			
			    }
			}
		}
		var closeBtn={
			xtype : 'button',
			text : 'Close',
			handler : function(){
				Ext.Msg.confirm('Exit without saving','Are you sure to switch before saving data?',function(btn){
					if(btn=='yes'){
						var parent=self.findParentByType('studymainview')
						parent.setActiveItem('container-study')
					}
				})
			}
		}
		var modalityStore = Ext.create('Ext.data.Store', {
			fields: ['modality'],
			fields: ['modality'],
			data : [
				{"modality": "CR"}, {"modality": "US"}, {"modality": "EKG"}, {"modality": "PICC"}, {"modality": "ECHO"}
				]
			});
		var refPhysicianStore = new Ext.data.JsonStore({
		//	storeId: 'groupTypeStore',
			//autoLoad: true,
			proxy: {
			type: 'ajax',
			url: 'system/dispatch.php?control=StudyControl&method=referringPhysicianList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				idProperty: 'origid'
			}
			},
			fields: ['referringphysician']
		});
		var studyDateControl=Ext.create('Ext.form.field.Date',{
					xtype : 'datefield',
					fieldLabel : 'Study Date',
					name: 'studydate',
					allowBlank:false,
					submitFormat : 'Y-m-d',
					format: 'm/d/Y',
					value : new Date()
				})
		var studyTimeControl	=Ext.create('Ext.form.field.Time',{
					xtype : 'timefield',
					fieldLabel : 'Study Time',
					name: 'studytime', 
					allowBlank:false, 
					increment: 30, 
					format: 'H:i', 
					value: new Date()
				})
		var cmbReferringPhysician=Ext.create('Ext.form.field.ComboBox',{
			xtype : 'combobox',
			fieldLabel : 'Referring Physician',
			store: refPhysicianStore,
			minChars:2,
			width:400,
			autoSelect: false,
			valueField: 'referringphysician',
			displayField: 'referringphysician',
			emptyText:'Referring Physician',
		})
		var study_info_fieldset=Ext.create('Ext.form.FieldSet',{
			title: 'Study Information',
			items : [
				studyDateControl,
				studyTimeControl,
				{
					xtype : 'textfield',
					fieldLabel : 'Study Description',
					name: 'description', 
					allowBlank:true, 
					anchor:'100%'
				},
				cmbReferringPhysician,
				{
					xtype : 'combobox',
					fieldLabel : 'Modality',
					store: modalityStore,
					queryMode: 'local',
					displayField: 'modality',
					valueField: 'modality',
					name: 'modality', 
				}
			]
		})
		
		var patientStore = new Ext.data.JsonStore({
			//storeId: 'groupTypeStore',
			//autoLoad: true,
			proxy: {
			type: 'ajax',
			url: 'system/dispatch.php?control=StudyControl&method=PatientList',
			reader: {
				type: 'json',
				rootProperty: 'data',
				idProperty: 'origid'
			}
			},
			fields: ['origid', 'firstname', 'lastname', 'birthdate', 'patientname']
		});
		
		var cmbPatientID=Ext.create('Ext.form.field.ComboBox',{
					fieldLabel : 'Patient',
					store: patientStore,
					name: 'patientid',
					tpl: new Ext.XTemplate(
						    '<tpl for="."><div class="x-boundlist-item">',
							'{patientname} ({birthdate}/ID: {origid})',
						    '</div></tpl>'
						),
					minChars:2,
					width:400,
					autoSelect: false,
					valueField: 'origid',
					displayField: 'patientname',
					emptyText:'Patient Name'
				})
		var _field_new_patient={fieldLabel: 'New patient', id: '_field_new_patient', checked: false, name: 'new_patient', xtype:"checkboxfield",
						listeners: 
						{
						    'change': function(self, newVal, oldVal, options)
						    {
								
								try{
									var fullName = cmbPatientID.getRawValue();
									var delimeterPos = fullName.indexOf(' ');
									_field_patname.setValue(fullName.substr(0, delimeterPos));
									_field_lastname.setValue(fullName.substr(delimeterPos + 1));
									
									if (newVal)
									{
										cmbPatientID.setDisabled(true);
										_field_patname.setDisabled(false);
										_field_lastname.setDisabled(false);
										_field_middlename.setDisabled(false);
										_field_birthday.setDisabled(false);
										_field_sex.setDisabled(false);
										_field_neworigid.setDisabled(false);
										_field_institution.setDisabled(false);
									}
									else
									{
										cmbPatientID.setDisabled(false);
										_field_patname.setDisabled(true);
										_field_lastname.setDisabled(true);
										_field_middlename.setDisabled(true);
										_field_birthday.setDisabled(true);
										_field_sex.setDisabled(true);
										_field_neworigid.setDisabled(true);
										_field_institution.setDisabled(false);

									}
								}catch(e){
									console.log(e)
								}
						    }

						}
						    
						}
		var institutionStore = new Ext.data.JsonStore({
			//storeId: 'institutionTypeStore',
			autoLoad: false,
			proxy: {
				type: 'ajax',
				url: 'system/dispatch.php?control=StudyControl&method=institutionList',
				reader: {
					type: 'json',
					rootProperty: 'data',
					idProperty: 'institution'
				}
			},
			fields: ['institution']
		});
		var _Institution_c=Ext.create('Ext.form.field.ComboBox',{
				fieldLabel: 'Institution',
				name: 'institution', 
				allowBlank:false, 
				xtype: 'combo',
				store: institutionStore,
				//id: '_Institution_cid',
				minChars:2,
				width:400,
				autoSelect: false,
				valueField: 'institution',
				displayField: 'institution'
				})
		var patientSex = new Ext.data.Store({
		   data:[{'sex': 'F'}, {'sex': 'M'}],
		   fields: ['sex']
		});
		var _field_neworigid=Ext.create('Ext.form.field.Text',{fieldLabel: 'Patient id', disabled: true, name: 'neworigid', allowBlank:false, width: 400})
		var _field_patname=Ext.create('Ext.form.field.Text',{fieldLabel: 'Name',  disabled: true, name: 'patname', allowBlank:false, width: 400})
		var _field_lastname=Ext.create('Ext.form.field.Text',{fieldLabel: 'Lastname', disabled: true, name: 'lastname', allowBlank:false, width: 400})
		var _field_middlename=Ext.create('Ext.form.field.Text',{fieldLabel: 'Middlename', disabled: true, name: 'middlename',width: 400})
		var _field_birthday=Ext.create('Ext.form.field.Date',{fieldLabel: 'Birthday', disabled: true, name: 'birthday', allowBlank:false,  format: 'm/d/Y', width: 400, value: new Date(),submitFormat:'Y-m-d'})
		var _field_sex=Ext.create('Ext.form.field.ComboBox',{fieldLabel: 'Sex',  disabled: true, name: 'sex', allowBlank:false,  store: patientSex, value: "M", autoSelect: true, valueField: 'sex', displayField: 'sex', queryMode: 'local'})
//	*/	
		var patient_info_fieldset=Ext.create('Ext.form.FieldSet',{
			title:  'Patient Information',
			autoHeight:true, 
			autoWidth:true, 
			defaults: {width: 180}, 
			labelWidth:105, 
			defaultType: 'textfield',
			items : [
				cmbPatientID,
				_field_new_patient,
				_Institution_c,
				_field_neworigid,
				_field_patname,
				_field_lastname,
				_field_middlename,
				_field_birthday,
				_field_sex
			]
		})
		
		
		buttons.push(sendBtn)
		//buttons.push({xtype: 'spacer'})
		buttons.push(closeBtn)
		
		self.buttons=buttons
		self.items=[study_info_fieldset,patient_info_fieldset];	
		this.callParent(arguments);
		
	}
});