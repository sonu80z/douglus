/**
 * This class is the controller for the main view for the application. It is specified as
 * the "controller" of the Main view class.
 *
 * TODO - Replace this content of this view to suite the needs of your application.
 */
Ext.define('MdiApp.view.main.MainController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.main',

    onUserAdmin: function (sender, record) {
		var win=Ext.create('MdiApp.view.main.win.UserAdminWindow',{})
		win.show();
        //Ext.Msg.confirm('Confirm', 'Are you sure?', 'onConfirm', this);
    },
	
	onItemSelected: function (sender, record) {
		
        Ext.Msg.confirm('Confirm', 'Are you sure?', 'onConfirm', this);
    },

    onConfirm: function (choice) {
        if (choice === 'yes') {
            //
        }
    },
	'mdi.admin.add' : function(){
		Ext.Msg.confirm('Confirm', 'mdi.admin.add', 'onConfirm', this);
		return
		mdi.admin.user = null;
                mdi.admin.getWindow().show();
                mdi.admin.getWindow().setTitle("Add User");
                Ext.getCmp('button-'+ mdi.admin.getActiveTabName().toLowerCase() + '-edit').hide();
                Ext.getCmp('button-'+ mdi.admin.getActiveTabName().toLowerCase() + '-add').show();
                if (mdi.admin.getActiveTabName() == "Group") {
                        mdi.admin.getWindow().setTitle("Add Group");
                        mdi.admin.setGridBaseParam("groupUserSourceGrid", {groupid:null});
                        mdi.admin.setGridBaseParam("groupFilterSourceGrid", {groupid:null, grouptypeid:null});
                        mdi.admin.clearGridData("groupUserGrid");
                        mdi.admin.clearGridData("groupFilterGrid");
                }
                mdi.admin.getForm().getForm().reset();
		//Ext.Msg.confirm('Confirm', 'Are you sure???', 'onConfirm', this);
	},
	'mdi.preferences.show': function(){
		Ext.Msg.confirm('Confirm', 'mdi.preferences.show', 'onConfirm', this);
	}
});
