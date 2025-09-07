Ext.applyIf(MdiApp, {
	authenticate:{
 		loginOpen:false,
		disclaimer:'disclaimer goes here',
		loginTitle:'MD Imaging Login',
		userSession:function()
                {
                    Ext.Ajax.request({
                        method:'POST',
                        url: 'system/authenticate.php',
                        success: function(data)
                        {
                           var result = Ext.decode(data.responseText);
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
                                MdiApp.authenticate.login();
                            }
                        }
                    });
                    MdiApp.mask(false);
		},
		logout:function(studyGrid){
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
                                            MdiApp.authenticate.login();
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
		login:function()
                {
                    if(this.loginOpen)return "A login window is already open.";
                    this.loginOpen = true;
                    var passwordexpired = "0";
                    var p = {action:'login'};
                    var submitLoginForm = function(){
                            Ext.getCmp('login-form').getForm().submit(
                            {
                                params:p,
                                success:function(form, action)
                                {
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
                                    MdiApp.study.refresh();
                                    MdiApp.authenticate.loginOpen = false;
                                    disclaimerWindow.show();
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
		}
	}
})

