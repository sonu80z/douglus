Ext.define('MdiApp.store.GroupUserStore',{
	extend : 'Ext.data.Store',
//	model: 'UserGroup',
	fields: ['id', 'username'],
	remoteSort:true,
	proxy: 
	{
		type: 'ajax',
		url: MdiApp.admin.dispatch+'?control=GroupControl&method=ViewGroupUsers',
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