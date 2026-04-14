Ext.define('MdiApp.store.GroupUserSourceStore',{
	extend : 'Ext.data.Store',
	//model: 'UserList',
	//fields: ['id', 'firstname', 'middlename', 'lastname', 'username',  'selfonly', 'admin', 'staffrole'],
	fields: ['id',  'username'],
	remoteSort:true,
	proxy: 
	{
		type: 'ajax',
		//url: MdiApp.admin.dispatch+'?control=UserControl&method=View',
		url: MdiApp.admin.dispatch+'?control=GroupControl&method=ViewGroupNotUsers',
		actionMethods: {
			read: 'POST'
		},
		reader: 
		{
			type: 'json',
			rootProperty: 'data',
			totalProperty: 'recordcount'
		}
	}
})