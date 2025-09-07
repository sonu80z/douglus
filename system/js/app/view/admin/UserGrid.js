Ext.define('MdiApp.view.admin.UserGrid',{
	extend : 'Ext.grid.Panel',
	frame : true,
	stripeRows: true,
	title:'Users',
	selModel:{mode: 'SINGLE'},
	multiselect:'false',
	constructor : function(config){
		var self=this;
		var columns = [
                                { header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'User Name',  width: 100, sortable: true, dataIndex: 'username'},
                                {header: 'First Name', width: 100, sortable: true, dataIndex: 'firstname'},
                                {header: 'Middle Name',  width: 75, sortable: true, dataIndex: 'middlename'},
                                {header: 'Last Name', width: 100, sortable: true, dataIndex: 'lastname'},
                                {header: 'Self Only', width: 75, sortable: false, dataIndex: 'selfonly'},
                                {header: 'Admin', width: 75, sortable: false, dataIndex: 'admin'},
                                {header: 'Staff role', width: 75, sortable: false, dataIndex: 'staffrole'}
                        ];
						
		
		var userStore=Ext.create('MdiApp.store.UserStore',{});
		
		self.tbar=[
                                {text:'Add', id:"toolbar-button-user-add-1", action:'addUser', tooltip:'Add User', iconCls:'ico-user-add'},
                                {text:'Edit', id:"toolbar-button-user-edit-1", action:'editUser',tooltip:'Edit User', iconCls:'ico-user-edit'/*, handler:'mdi.admin.edit'*/},
                                {text:'Delete', id:"toolbar-button-user-delete-1", action:'removeUser', tooltip:'Remove User', iconCls:'ico-user-delete'/*, handler:'mdi.admin.remove'*/},
                                '->',
                                Ext.create('Ext.form.field.Text',{
                                        width:210,
                                        store: Ext.create('MdiApp.store.UserStore',{}),
										displayField : 'username',
										valueField:'id',
										listeners : {
											specialkey : function(field,e){
												if (e.getKey() == e.ENTER) {
													//userStore
													userStore.reload({params:{search:field.getValue()}});
												}
											}
										}
                                        //id:'admin-users-search'
                                })                                
                        ]
                        
                        //height:400
		config.columns=columns;
		config.store=userStore;
		this.callParent(arguments);
	}
});