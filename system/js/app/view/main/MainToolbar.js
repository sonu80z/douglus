Ext.define('MdiApp.view.main.MainToolbar',{
	extend : 'Ext.toolbar.Toolbar',
	items:[
			{text:'Home', iconCls:'ico-home', handler:function(){window.location = MdiApp.config.view}},
			{text:'User Administration', iconCls:'ico-user-admin', id:'toolbar-button-user-admin'/*,listeners:{click:'onItemSelected'}*/, handler:'onUserAdmin'},
                        //{text:'Profile', handler: function(){ window.location = 'profile.php?username=' + mdi.study.userName}},
			{text:'Settings', iconCls:'ico-preferences', id:'toolbar-button-preferences', handler:'mdi.preferences.show'},
			{text:'Legend', iconCls:'ico-legend', id:'toolbar-button-legend', handler:'mdi.study.legend'},
			{text:'Logs', iconCls:'ico-admin-logs', id:'toolbar-button-logs', handler:'mdi.study.logs'/*function(){mdi.study.logs('');}*/},
                        '->',
			{text:'Change Password', iconCls:'ico-password', handler:'passwordManager'/*function(){mdi.passwordManager.show();}*/},
			{text:'Logout', iconCls:'ico-login', handler:'logout'/*function(){mdi.authenticate.logout();}*/},
			'-',
			'<div id="login-icon" class="ico-user-login"><span id="login-status">test</span></div>',
			{text:'Manual', iconCls:'ico-help', handler:function(){window.open('/manual.pdf')}}
		]
});