Ext.define('MdiApp.view.main.win.UserAdminCtrl',{
	extend : 'Ext.app.ViewController',
	control: {
			'button[action=close]' : {
				click : 'close'
			}
		},
		close: function(){
			console.log(this);
			console.log(arguments);
			console.log(this.getView());
		},
		onConfirm : function(){
			console.log(arguments)
		}
})