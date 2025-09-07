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
                                {id:'id', header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'User Name',  width: 100, sortable: true, dataIndex: 'username'},
                                {header: 'First Name', width: 100, sortable: true, dataIndex: 'firstname'},
                                {header: 'Middle Name',  width: 75, sortable: true, dataIndex: 'middlename'},
                                {header: 'Last Name', width: 100, sortable: true, dataIndex: 'lastname'},
                                {header: 'Self Only', width: 75, sortable: false, dataIndex: 'selfonly'},
                                {header: 'Admin', width: 75, sortable: false, dataIndex: 'admin'},
                                {header: 'Staff role', width: 75, sortable: false, dataIndex: 'staffrole'}
                        ];
						
	/*	var userStore = Ext.create('Ext.data.Store', {
                    id: 'userStore',
                    model: 'User',
                    remoteSort:true,
                    totalProperty: 'recordcount',
                    root:'data',
                    autoLoad:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: MdiApp.admin.dispatch+'?control=UserControl&method=View',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data'
                        }
                    }
                }); */
		
		var userStore=Ext.create('MdiApp.store.UserStore',{});
                        
                        //height:400
		this.columns=columns;
		this.store=userStore;
		this.callParent(arguments);
	}
});