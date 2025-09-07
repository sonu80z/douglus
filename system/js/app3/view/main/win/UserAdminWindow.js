Ext.define('MdiApp.view.main.win.UserAdminWindow',{
	extend : 'Ext.Window',
	iconCls:'ico-user-admin',
    closeAction:'hide', 
    autoScroll:true,
    modal:true,
    border:false,
	width:660,
	height:435,
	resizable:false,
	title:'User Administration',
	constructor : function(config){
		var self=this;
		var userTabPanel = new Ext.tab.Panel({
                        id:'userTabPanel',
                        activeTab: 0,
                      //  width:650,
                       // height:400,
                        items:[Ext.create('MdiApp.view.admin.UserGrid',{}), Ext.create('MdiApp.view.admin.GroupGrid',{})],
                        tbar:[
                        //        {text:'Add', id:"toolbar-button-user-add", tooltip:'Add User', iconCls:'ico-user-add', handler:'mdi.admin.add'},
                          //      {text:'Edit', id:"toolbar-button-user-edit", tooltip:'Edit User', iconCls:'ico-user-edit'/*, handler:mdi.admin.edit*/},
                            //    {text:'Delete', id:"toolbar-button-user-delete", tooltip:'Remove User', iconCls:'ico-user-delete'/*, handler:mdi.admin.remove*/},
                                '->',
                              /*  new Ext.ux.SearchField({
                                        width:210,
                                    //    store: userStore,
                                        id:'admin-users-search'
                                })       */                         
                        ],
                        listeners: {
                            'tabchange': function(tabPanel, toTab, fromTab, option)
                            {
                                var buttons = ['Add', 'Edit', 'Delete'];
                                for (var i = 0; i < buttons.length; i++){
                                    var toolbarButton = Ext.getCmp('toolbar-button-user-' + buttons[i].toLowerCase());
                                    toolbarButton.setTooltip(buttons[i] + " " + mdi.admin.getActiveTabName());
                                    var ico = "ico-" + mdi.admin.getActiveTabName().toLowerCase() + "-" + buttons[i].toLowerCase();
                                    toolbarButton.setIconCls(ico);
                                } 
                                if (mdi.admin.getActiveTabName() == 'User')
                                    Ext.getCmp('admin-users-search').store = userStore;
                                else
                                    Ext.getCmp('admin-users-search').store = groupStore;
                            }
                        }
                });
		self.items=userTabPanel;
		this.callParent(arguments);
	}
})