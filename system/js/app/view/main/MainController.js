/**
 * This class is the controller for the main view for the application. It is specified as
 * the "controller" of the Main view class.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('MdiApp.view.main.MainController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.main',
	
	refs : [{
		ref : 'mainGrid',
		selector: 'grid'
	}],
	
	init : function(){
		var studyGrid = this.lookupReference('studyGrid');
		this.userSession()
	},

    onUserAdmin: function (sender, record) {
		var win=Ext.create('MdiApp.view.admin.UserGroupAdminWindow',{closeAction:'destroy'});
		win.show();
    },
	'mdi.preferences.show': function(){
		

					
					var mailStore=Ext.create('MdiApp.store.MailStore', {
						//id:'mailStore',
					});

                    

					
					var physicanMailStore = Ext.create('MdiApp.store.PhysicianStore', 
                    {
                        //id:'physicanMailStore'
                    });
					

                    var mailPagingBar = new Ext.PagingToolbar({
                        pageSize: 12,
                        store: mailStore,
                        displayInfo: true,
                        displayMsg: 'Displaying rows {0} - {1} of {2}',
                        emptyMsg: "No rows to display"
                    });

                    var mailGrid = Ext.create('MdiApp.view.admin.MailGrid',{
                        //id:'mailGrid',
                        store: mailStore,
                        width:550,
                        height:400
                    });
					
                    var physicanMailPagingBar = new Ext.PagingToolbar({
                        pageSize: 12,
                        store: mailStore,
                        displayInfo: true,
                        displayMsg: 'Displaying rows {0} - {1} of {2}',
                        emptyMsg: "No rows to display"
                    });

                    var physicanMailGrid = Ext.create('MdiApp.view.admin.PhysicanMailGrid',{
                        //id:'physicanMailGrid',
                        store: physicanMailStore,
                        width:550,
                        height:400
                    });
					
					var win=Ext.create('Ext.Window',{
						//id:'prefWindow',
                        iconCls:'ico-preferences',
                        modal:true,
                        border:false,
                        width:662,
                        height:430,
                        title:'Settings',
                        
						
						items:{xtype: 'tabpanel',items:[mailGrid,physicanMailGrid]},
						layout:'fit'
						
					});
					win.show();
	},
	'mdi.study.legend':function(){
		var win = Ext.create('Ext.Window',{
                                    title:'The legend',
                                    iconCls: 'ico-legend',
                                    bodyStyle:'padding:5px',
                                    id:'window-batch-print-pdf',
                                    width:385,
                                    modal:true,
                                    resizable:false,
                                    html:'<table style="width:100%"><tr><td id="td2">'+
                                            '<div class="study-unread" ><div>&mdash; new studies </div></div>'+
                                            '<div class="study-read" ><div>&mdash; read studies </div></div>'+
                                            '</td></tr></tale>'
                            });
                            win.show();
	},
	'mdi.study.logs':function(){
		var win=Ext.create('MdiApp.view.admin.LogWindow',{})
		win.show()
	},
	'passwordManager':function(){
		var studyGrid = this.lookupReference('studyGrid');
			var form = new Ext.FormPanel({
				//labelWidth: 110,
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
										studyGrid.getStore().loadData({data:[]});
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
						MdiApp.authenticate.logout(studyGrid)
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
	},
	logout:function(){
		var studyGrid = this.lookupReference('studyGrid');
		var self=this
		var p = { action: 'logout' };
		MdiApp.setAdmin(0);
		Ext.Ajax.request({
		   url: 'system/authenticate.php',
		   success: function(data)
		   {
				MdiApp.setLoadingText('Logout Successful...');
				var win = new Ext.Window({
						width: 355,
						modal: true,
						id:'logout-window',
						resizable:false,
						closable:false,
						closeAction: 'close',
						iconCls: 'ico-login',
						title: 'Logged out',
						items:[{frame:true,html:'You\'ve been successfully logged out!'}],
						buttons:[
						{text:'Re-login', 
							handler:function(){
								Ext.getCmp('logout-window').destroy();
								self.login();
							}
						},
						{text:'Close', 
							handler:function(){
									win.destroy();
							}
						}
						]
				});
				//this clears the grid when changing logins.
				//var mainGrid = MdiApp.study.getGrid();
				var mainGridStore = studyGrid.getStore();
				mainGridStore.load();
				win.show();
		   },
		   params: p
		});
		MdiApp.setLoadingText('Attempting to Logout...');
	},
	login :  function(){
		var studyGrid = this.lookupReference('studyGrid');
		var passwordexpired = "0";
                    var p = {action:'login'};
                    var submitLoginForm = function(){
                            Ext.getCmp('login-form').getForm().submit(
                            {
                                params:p,
                                success:function(form, action)
                                {
                                    try{
										passwordexpired = action.result.passwordexpired;
                                    MdiApp.setUsername(action.result.username);
                                    MdiApp.setAdmin(action.result.admin);
                                    MdiApp.setCanMailPDF(action.result.canmailpdf);
                                    MdiApp.setCanBatchPrintPDFs(action.result.canbatchprintpdfs);
                                    MdiApp.setCanBurnCD(action.result.canburncd);
                                    MdiApp.setCanMarkAsReviewed(action.result.canmarkasreviewed);
                                    MdiApp.setCanMarkCritical(action.result.canmarkcritical);
                                    MdiApp.setCanAttachOrder(action.result.canattachorder);
                                    MdiApp.setCanAddNote(action.result.canaddnote);
                                    MdiApp.setStaffRole(action.result.staffrole);
                                    //we no longer use this setting.
                                    //mdi.setCanViewHTML5Viewer(action.result.canviewhtml5viewer);
                                    MdiApp.setCanViewHTML5Viewer(MdiApp.getCanViewHTML5Viewer());
                                    
                                    Ext.getCmp('login-window').destroy();
                                    MdiApp.mask(false);
                                    MdiApp.setLoadingText('Login successful, Starting Application...')
                                    studyGrid.refresh();
                                    MdiApp.authenticate.loginOpen = false;
                                    disclaimerWindow.show();
									}catch(e){
										console.log(e)
									}
									window.location.reload();
                                },
                                failure:function(form, action){ 
                                    Ext.Msg.error({title:action.result.error.type, msg:action.result.error.message});
                                    var bform = Ext.getCmp('login-form').getForm();
                                    bform.reset();
                                    bform.isValid();
                                    Ext.getCmp('login-user').focus();
                                }
                            });
                    }

                    var form = new Ext.FormPanel({
                            labelWidth: 75,
                            id:'login-form',
                            url: 'system/authenticate.php',
                            frame:true,
                            bodyStyle: 'padding:5px;',
                            width: 350,
                            defaults: { width: 230 },
                            defaultType: 'textfield',
                            items: [
                                    { id:'login-user', fieldLabel: 'User Name', name: 'username', allowBlank: false, value:'' }, 
                                    { id:'login-password', fieldLabel: 'Password', name: 'password', inputType: 'password', allowBlank:false, value:''}
                            ],
                            buttons: [{
                                    text: 'Login',
                                    id:'login-submit',
                                    handler:function()
                                    {
                                        submitLoginForm();
                                    }
                            }]
                    });
                    var disclaimerWindow = new Ext.Window({
                    id:'disclaimer-window',
                    modal:true,
                    width:300,
                    height:300,
                    resizable:false,
                    closable:false,
                    autoScroll:true,
                    closeAction: 'close',
                    title:'Usage Agreement',
                    html:MdiApp.authenticate.disclaimer,
                    buttons:[
                      {text:'decline', handler:function(){
                        MdiApp.authenticate.logout();
                        Ext.getCmp('disclaimer-window').destroy();
                        }
                      },
                      {text:'accept', handler:function(){
                          Ext.getCmp('disclaimer-window').destroy();
                              if(passwordexpired == "1")
                              {
                                    MdiApp.passwordManager.show();
                                    Ext.Msg.warn("Your password has expired. Please create a new password for this account.")
                              }
                      }}
                    ]
                    });
                    var win = new Ext.Window({
                            width: 355,
                            modal: true,
                            id:'login-window',
                            resizable:false,
                            closable:false,
                            closeAction: 'close',
                            iconCls: 'ico-login',
                            title: MdiApp.authenticate.loginTitle,
                            items: [form],
                            listeners:
                            {
                                'show':function()
                                {
                                    Ext.getCmp('login-form').getForm().reset();
                                },
                                'close':function()
                                {
                                    return false;
                                    MdiApp.authentication.loginOpen = false;
                                }
                            }
                    });
                    win.show();
                    this.map = new Ext.KeyMap("login-window", [{
                        key : [10, 13],
                        scope : this,
                        fn : submitLoginForm
                    }]); 
	},
	userSession:function(){
		var self=this
		Ext.Ajax.request({
                        method:'POST',
                        url: 'system/authenticate.php',
                        success: function(data)
                        {
                           var result = Ext.decode(data.responseText);console.log(result)
                            if (result.success)
                            {
                                MdiApp.setLoadingText('Login Validated...');
                                MdiApp.setUsername(result.username);
                                MdiApp.setAdmin(result.admin);
                                MdiApp.setCanMailPDF(result.canmailpdf);
                                MdiApp.setCanBatchPrintPDFs(result.canbatchprintpdfs);
                                MdiApp.setCanMarkAsReviewed(result.canmarkasreviewed);
                                MdiApp.setCanBurnCD(result.canburncd);
                                MdiApp.setCanMarkCritical(result.canmarkcritical);
                                MdiApp.setCanAttachOrder(result.canattachorder);
                                MdiApp.setCanAddNote(result.canaddnote);
                                MdiApp.setStaffRole(result.staffrole);
                                //we no longer use this setting.
                                //mdi.setCanViewHTML5Viewer(result.canviewhtml5viewer);
                                MdiApp.setCanViewHTML5Viewer(MdiApp.getCanViewHTML5Viewer());
                                if(result.passwordexpired){
                                    MdiApp.passwordManager.show();
                                    Ext.Msg.warn("Your password has expired. Please create a new password for this account.")
                                }
                            }
                            else
                            {
                                MdiApp.setLoadingText('Login Failed...');
								self.login()
                               // MdiApp.authenticate.login();
							   
                            }
                        }
                    });
                    MdiApp.mask(false);
	}
});
