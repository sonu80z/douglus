Ext.define('MdiApp.view.admin.AddEditUserWindow',{
	extend : 'Ext.Window',
	//closeAction:'hide', 
	modal:true,
	width:400,
	height:592,
	resizable:false,
	title:'Add User',
	layout : 'fit',
	//items:[userForm],
	
	//controller : ,
	constructor : function(config){
		var self=this;
		
		var addEditUser=function(){
                var values = userForm.getValues();
				var request={success:function(response){
						console.log(response);
                                if (response.success) 
                                {
                                   /* MdiApp.setCanMailPDF(values.canmailpdf);
                                    MdiApp.setCanBatchPrintPDFs(values.canbatchprintpdfs);
                                    MdiApp.setCanMarkAsReviewed(values.canmarkasreviewed);
                                    MdiApp.setCanBurnCD(values.canburncd);
                                    MdiApp.setCanMarkCritical(values.canmarkcritical);
                                    MdiApp.setCanAttachOrder(values.canattachorder);
                                    MdiApp.setStaffRole(values.staffrole);
                                    MdiApp.setCanAddNote(values.canaddnote);*/
                                    //we no longer use this setting.
                                    //mdi.setCanViewHTML5Viewer(values.canviewhtml5viewer);
                                   /* MdiApp.setCanViewHTML5Viewer(mdi.getCanViewHTML5Viewer());*/
                                    
                                    //MdiApp.admin.getStore().load();
                                    //Ext.getCmp('groupUserSourceGrid').getStore().load();
									config.grid.getStore().load();
                                    self.close();
                                }
                                    else if(response.msg){
                                        Ext.Msg.error(response.msg);
                                }
                }};
				if(config.data){
					request.params=Ext.applyIf({control:'UserControl', method:'Update'}, values)
				}else{
					delete values.id;
					request.params=Ext.applyIf({control:'UserControl', method:'Add'}, values)
				}
				console.log(request)
                MdiApp.admin.request(request);
		};
		self.buttons=[];
		self.buttons.push({text:"Cancel", handler:function(){
			self.close();
		}})
		if(config.data){
			self.buttons.push({text:"Save", /*id:'button-user-edit',*/ handler:addEditUser})
		}else{
			self.buttons.push({text:"Add",handler:addEditUser, /*id:'button-user-add'*/})
		}
		
		
		var userForm = Ext.create('Ext.form.FormPanel',{
                        id:'userForm',
                        labelWidth: 175, // label settings here cascade unless overridden
                        bodyStyle:'padding:5px 5px 0',
                        autoWidth:true,
                        autoHeight:true,
						scrollable : true,
						//data : data,
                        defaultType: 'textfield',
                        items: [{fieldLabel: 'ID', name: 'id', allowBlank:false, xtype:'hidden' },
                                { xtype:'fieldset', title: 'User Information', autoHeight:true, autoWidth:true, defaults: {width: 180}, labelWidth:105, defaultType: 'textfield',
                                items :[
                                        {fieldLabel: 'First Name', name: 'firstname', allowBlank:false, width:280},
                                        {fieldLabel: 'Middle Name', name: 'middlename', width:280},
                                        {fieldLabel: 'Last Name', name: 'lastname', allowBlank:false, width:280}
                                ]
                        },{xtype:'fieldset',  title: 'User Login', autoHeight:true, autoWidth:true, defaultType: 'textfield',
                                items :[
                                        {fieldLabel: 'User Name', name: 'username', width:280, allowBlank:false },
                                        {fieldLabel: 'Password', name: 'password', width:280, allowBlank:false, inputType:'password' }
                                //	{fieldLabel: 'Confirm Password', name: 'confirmpassword', width:160, allowBlank:false, inputType:'password' },
                                ]
                        },{xtype:'fieldset',  title: 'User Options', autoHeight:true,  autoWidth:true, defaults: {width: 190}, defaultType: 'textfield', layout:'hbox',
                                items :[
                                        {id:'lp12', xtype: 'fieldset', width: 178, itemCls:'usereditcont', items:
                                            [
                                                {fieldLabel: 'Self Studies Only',inputValue:1,uncheckedValue:0, id:'checkbox-self-only', name: 'selfonly',  allowBlank:false, xtype:"checkbox", 
                                                        listeners:{'check':function(self, checked){
                                                                if (checked) {
                                                                    Ext.getCmp('checkbox-admin').setValue(false);
                                                                }
                                                        }}
                                                },
                                                {fieldLabel: 'Admin', name: 'admin',inputValue:1,uncheckedValue:0, id:'checkbox-admin', allowBlank:false, xtype:"checkbox", 
                                                        listeners:{'check':function(self, checked){
                                                                if (checked) {
                                                                    Ext.getCmp('checkbox-self-only').setValue(false);
                                                                }
                                                        }}
                                                },
                                                {fieldLabel: 'Staff role', name: 'staffrole',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-staff-role', allowBlank:false, xtype:"checkbox", "handler":function()
                                                    {
                                                        if (this.checked)
                                                        {
                                                            try
                                                            {
                                                                Ext.getCmp('checkbox-can-mail-pdf').setValue(true);
                                                                Ext.getCmp('checkbox-can-print-pdf').setValue(true);
                                                                Ext.getCmp('checkbox-can-review-pdf').setValue(true);
                                                                Ext.getCmp('checkbox-can-burn-cd').setValue(true);
                                                                Ext.getCmp('checkbox-can-mark-critical').setValue(true);
                                                                Ext.getCmp('checkbox-can-add-note').setValue(true);
                                                                Ext.getCmp('checkbox-can-attach-order-').setValue(true);
                                                                Ext.getCmp('checkbox-staff-role').setValue(true);
                                                            }
                                                            catch(e){}
                                                        }
                                                    }
                                                },
                                                {fieldLabel: 'Password Expired', name: 'passwordexpired',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-password-expired', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can email a file', name: 'canmailpdf',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-mail-pdf', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can batch print', name: 'canbatchprintpdfs',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-print-pdf', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can burn CD', name: 'canburncd',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-burn-cd', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can mark as reviewed', name: 'canmarkasreviewed',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-review-pdf', allowBlank:false, xtype:"checkbox"}
                                            ]
                                        },
                                        {id:'lpr2', xtype: 'fieldset', width: 170, border:false, itemCls:'usereditcont', items:
                                            [
                                                {fieldLabel: 'Can mark study as critical', name: 'canmarkcritical',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-mark-critical', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can add note', name: 'canaddnote',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-add-note', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can attach order', name: 'canattachorder',inputValue:1,uncheckedValue:0, checked:true, id:'checkbox-can-attach-order-', allowBlank:false, xtype:"checkbox"}//,
                                                //this is no longer used as we will auto detect.
                                                //{fieldLabel: 'Can view html5 viewer', name: 'canviewhtml5viewer', checked:false, id:'checkbox-can-view-html5-viewer', allowBlank:false, xtype:"checkbox"},
                                            ]
                                        }
                                ]}
                        ]
                });
		if(config.data)
			userForm.getForm().loadRecord(config.data)
		self.items=userForm;
		this.callParent(arguments);
	}
})