Ext.applyIf(mdi, {
	passwordManager:{
		show:function(){
			var form = new Ext.FormPanel({
				labelWidth: 110,
				id:'change-password-form',
				frame:true,
				bodyStyle: 'padding:5px;',
				width: 370,
				defaults: { width: 200 },
				defaultType: 'textfield',
				items :[
				        { id:'new-password', fieldLabel: 'New Password', name: 'newpassword', inputType: 'password',  allowBlank:false },
				        { id:'confirm-password', fieldLabel: 'Confirm Password', name: 'confirmpassword', inputType: 'password',  allowBlank:false }
				],
				buttons: [{
					text: 'Apply',
					id:'apply-password',
					handler:function(){
						//submitLoginForm();
						var basicForm = form.getForm();
						if(basicForm.isValid()){
							var values = basicForm.getValues();
							if(values["newpassword"] != values["confirmpassword"]){
								Ext.Msg.error("The new password didn't match the password confirmation");
								basicForm.markInvalid({
									newpassword:'New password must match password confirmation.',
									confirmpassword:'Password confirmation must match new password.'
								})
							}else{
								Ext.Ajax.request({
								   url: 'system/authenticate.php',
								   success: function(data){
								   		Ext.Msg.info({title:"Password Manager", msg:"Password successfully updated."});
										Ext.getCmp("change-password-window").destroy();
										//this clears the grid when changing logins.
										mdi.study.getGrid().getStore().loadData({data:[]});
										win.show();
										
								   },
								   params: { action: 'changepassword', newpassword: values["newpassword"] }
								});
							}
							
						}
					}
				},
				{
					text: 'Cancel',
					id:'cancel-password',
					handler:function(){
						Ext.getCmp("change-password-window").destroy();
						mdi.authenticate.logout()
					}
				}]
			});
				var win = new Ext.Window({
				width: 375,
				modal: true,
				id:'change-password-window',
				resizable:false,
				closable:false,
				modal:true,
				closeAction: 'close',
				title: 'Change Password',
				items: [form]
				});
				win.show();
		}
	}
});