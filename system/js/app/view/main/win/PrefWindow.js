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
		self.controller = Ext.create('Ext.app.ViewController',{
		control: {
			'button[action=addUser]' : {
				click : 'addUser'
			},
			'button[action=addGroup]' : {
				click : 'addGroup'
			},
			'button[action=editUser]' : {
				click : 'editUser'
			},
			'button[action=editGroup]' : {
				click : 'editGroup'
			},
			'button[action=removeUser]' : {
				click : 'removeUser'
			},
			'button[action=removeGroup]' : {
				click : 'removeGroup'
			}
		},
		addUser: function(){
			console.log(arguments)
			var win=Ext.create('MdiApp.view.admin.AddEditUser',{})
			win.show()
		},
		addGroup: function(){
			console.log(arguments)
			var win=Ext.create('MdiApp.view.admin.AddEditGroup',{})
			win.show()
		},
		editUser: function(){
			if(userGrid.getSelection().length){
				var data=userGrid.getSelection()[0];
				var win=Ext.create('MdiApp.view.admin.AddEditUser',{data:data})
				win.show()
			}else{
				Ext.Msg.warn('Select the user you would like to edit.')
			}
			//Ext.Msg.confirm('Confirm', 'View', 'onConfirm', this);
		},
		editGroup: function(){
			if(groupGrid.getSelection().length){
				var data=groupGrid.getSelection()[0];
				var win=Ext.create('MdiApp.view.admin.AddEditGroup',{data:data})
				win.show()
			}else{
				Ext.Msg.warn('Select the group you would like to edit.')
			}
		},
		removeUser: function(){
			if(userGrid.getSelection().length){
				var selected=userGrid.getSelection();
				MdiApp.admin.request({params:Ext.applyIf({control:'UserControl', method:'Delete', 'username':selected[0].data.username, 'id':selected[0].data.id}), 
                            success:function(response)
                                    {
                                        if (response.success) 
                                        {
                                            userGrid.getStore().remove(userGrid.getSelected());
                                        }
                                        else if(response.msg)
                                        {
                                            Ext.Msg.error(response.msg);
                                        }
                                    }
                        });
			}else{
				Ext.Msg.warn('Select the user you would like to delete.')
			}
			
		},
		removeGroup: function(){
			if(groupGrid.getSelection().length){
				var selected=groupGrid.getSelection();
				MdiApp.admin.request({params:Ext.applyIf({control:'UserControl', method:'Delete', 'username':selected[0].data.username, 'id':selected[0].data.id}), 
                            success:function(response)
                                    {
                                        if (response.success) 
                                        {
                                            groupGrid.getStore().remove(groupGrid.getSelected());
                                        }
                                        else if(response.msg)
                                        {
                                            Ext.Msg.error(response.msg);
                                        }
                                    }
                        });
			}else{
				Ext.Msg.warn('Select the group you would like to delete.')
			}
		},
		onConfirm : function(){
			console.log(arguments)
		}
	});
		var userGrid=Ext.create('MdiApp.view.admin.UserGrid',{});
		var groupGrid=Ext.create('MdiApp.view.admin.GroupGrid',{});
		var userTabPanel = new Ext.tab.Panel({
                        id:'userTabPanel',
                        activeTab: 0,
                      //  width:650,
                       // height:400,
                        items:[userGrid, groupGrid ],
                     /*   tbar:[
                                {text:'Add', id:"toolbar-button-user-add", action:'add', tooltip:'Add User', iconCls:'ico-user-add', handler:'addClick'},
                                {text:'Edit', id:"toolbar-button-user-edit", action:'edit',tooltip:'Edit User', iconCls:'ico-user-edit', handler:'mdi.admin.edit'},
                                {text:'Delete', id:"toolbar-button-user-delete", tooltip:'Remove User', iconCls:'ico-user-delete', handler:'mdi.admin.remove'},
                                '->',
                                Ext.create('Ext.form.field.ComboBox',{
                                        width:210,
                                        store: userGrid.getStore(),
                                        id:'admin-users-search'
                                })                                
                        ],*/
                        listeners: {
                            'tabchange': function(tabPanel, toTab, fromTab, option)
                            {
								return
								var tabTitle=toTab.title.replace(/s$/, "");
								var buttons = ['Add', 'Edit', 'Delete'];
                                for (var i = 0; i < buttons.length; i++){
									console.log(buttons[i].toLowerCase())
                                    var toolbarButton = Ext.getCmp('toolbar-button-user-' + buttons[i].toLowerCase());
                                    toolbarButton.setTooltip(buttons[i] + " " + tabTitle);
                                    var ico = "ico-" + tabTitle.toLowerCase() + "-" + buttons[i].toLowerCase();
                                    toolbarButton.setIconCls(ico);
                                } 
                                if (tabTitle == 'User')
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