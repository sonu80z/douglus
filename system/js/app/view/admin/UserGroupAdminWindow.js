Ext.define('MdiApp.view.admin.UserGroupAdminWindow',{
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
			var win=Ext.create('MdiApp.view.admin.AddEditUserWindow',{grid:userGrid})
			win.show()
		},
		addGroup: function(){
			var win=Ext.create('MdiApp.view.admin.AddEditGroupWindow',{grid:groupGrid})
			win.show()
		},
		editUser: function(){
			if(userGrid.getSelection().length){
				var data=userGrid.getSelection()[0];
				var win=Ext.create('MdiApp.view.admin.AddEditUserWindow',{data:data,grid:userGrid})
				win.show()
			}else{
				Ext.Msg.warn('Select the user you would like to edit.')
			}
			//Ext.Msg.confirm('Confirm', 'View', 'onConfirm', this);
		},
		editGroup: function(){
			if(groupGrid.getSelection().length){
				var data=groupGrid.getSelection()[0];
				console.log(data)
				var win=Ext.create('MdiApp.view.admin.AddEditGroupWindow',{data:data,grid:groupGrid})
				win.show()
			}else{
				Ext.Msg.warn('Select the group you would like to edit.')
			}
		},
		removeUser: function(){
			if(userGrid.getSelection().length){
				var selected=userGrid.getSelection();
				Ext.Msg.warn({msg:'Sure to delete selected user',buttons:Ext.MessageBox.OKCANCEL,fn:function(btn){
					if(btn=='ok'){
						MdiApp.admin.request({params:Ext.applyIf({control:'UserControl', method:'Delete', 'username':selected[0].data.username, 'id':selected[0].data.id}), 
									success:function(response)
											{
												if (response.success) 
												{
													userGrid.getStore().remove(selected[0]);
												}
												else if(response.msg)
												{
													Ext.Msg.error(response.msg);
												}
											}
								});
					}
				}})
			}else{
				Ext.Msg.warn('Select the user you would like to delete.')
			}
			
		},
		removeGroup: function(){
			if(groupGrid.getSelection().length){
				var selected=groupGrid.getSelection();
				Ext.Msg.warn({msg:'Sure to delete selected group',buttons:Ext.MessageBox.OKCANCEL,fn:function(btn){
					if(btn=='ok'){
						MdiApp.admin.request({params:Ext.applyIf({control:'GroupControl', method:'Delete', 'username':selected[0].data.username, 'id':selected[0].data.id}), 
									success:function(response)
											{
												if (response.success) 
												{
													groupGrid.getStore().remove(selected[0]);
												}
												else if(response.msg)
												{
													Ext.Msg.error(response.msg);
												}
											}
								});
					}
				}});
			}else{
				Ext.Msg.warn('Select the group you would like to delete.')
			}
		}
	});
		var userGrid=Ext.create('MdiApp.view.admin.UserGrid',{});
		var groupGrid=Ext.create('MdiApp.view.admin.GroupGrid',{});
		var userTabPanel = new Ext.tab.Panel({
                        id:'userTabPanel',
                        activeTab: 0,
                      //  width:650,
                       // height:400,
                        items:[userGrid, groupGrid ]
                });
		self.items=userTabPanel;
		this.callParent(arguments);
	}
})