Ext.applyIf(mdi, {
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
                                mdi.setLoadingText('Login Validated...');
                                mdi.setUsername(result.username);
                                mdi.setAdmin(result.admin);
                                mdi.setCanMailPDF(result.canmailpdf);
                                mdi.setCanBatchPrintPDFs(result.canbatchprintpdfs);
                                mdi.setCanMarkAsReviewed(result.canmarkasreviewed);
                                mdi.setCanBurnCD(result.canburncd);
                                mdi.setCanMarkCritical(result.canmarkcritical);
                                mdi.setCanAttachOrder(result.canattachorder);
                                mdi.setCanAddNote(result.canaddnote);
                                mdi.setStaffRole(result.staffrole);
                                //we no longer use this setting.
                                //mdi.setCanViewHTML5Viewer(result.canviewhtml5viewer);
                                mdi.setCanViewHTML5Viewer(mdi.getCanViewHTML5Viewer());
                                if(result.passwordexpired){
                                    mdi.passwordManager.show();
                                    Ext.Msg.warn("Your password has expired. Please create a new password for this account.")
                                }
                            }
                            else
                            {
                                mdi.setLoadingText('Login Failed...');
                                mdi.authenticate.login();
                            }
                        }
                    });
                    mdi.mask(false);
		},
		logout:function(){
                    var p = { action: 'logout' };
                    mdi.setAdmin(0);
                    Ext.Ajax.request({
                       url: 'system/authenticate.php',
                       success: function(data)
                       {
                            mdi.setLoadingText('Logout Successful...');
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
                                            mdi.authenticate.login();
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
                            var mainGrid = mdi.study.getGrid();
                            var mainGridStore = mainGrid.getStore();
                            mainGridStore.load();
                            win.show();
                       },
                       params: p
                    });
                    mdi.setLoadingText('Attempting to Logout...');
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
                                    mdi.setUsername(action.result.username);
                                    mdi.setAdmin(action.result.admin);
                                    mdi.setCanMailPDF(action.result.canmailpdf);
                                    mdi.setCanBatchPrintPDFs(action.result.canbatchprintpdfs);
                                    mdi.setCanBurnCD(action.result.canburncd);
                                    mdi.setCanMarkAsReviewed(action.result.canmarkasreviewed);
                                    mdi.setCanMarkCritical(action.result.canmarkcritical);
                                    mdi.setCanAttachOrder(action.result.canattachorder);
                                    mdi.setCanAddNote(action.result.canaddnote);
                                    mdi.setStaffRole(action.result.staffrole);
                                    //we no longer use this setting.
                                    //mdi.setCanViewHTML5Viewer(action.result.canviewhtml5viewer);
                                    mdi.setCanViewHTML5Viewer(mdi.getCanViewHTML5Viewer());
                                    
                                    Ext.getCmp('login-window').destroy();
                                    mdi.mask(false);
                                    mdi.setLoadingText('Login successful, Starting Application...')
                                    mdi.study.refresh();
                                    mdi.authenticate.loginOpen = false;
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
                    html:mdi.authenticate.disclaimer,
                    buttons:[
                      {text:'decline', handler:function(){
                        mdi.authenticate.logout();
                        Ext.getCmp('disclaimer-window').destroy();
                        }
                      },
                      {text:'accept', handler:function(){
                          Ext.getCmp('disclaimer-window').destroy();
                              if(passwordexpired == "1")
                              {
                                    mdi.passwordManager.show();
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
                            title: mdi.authenticate.loginTitle,
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
                                    mdi.authentication.loginOpen = false;
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

