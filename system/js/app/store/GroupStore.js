Ext.define('MdiApp.store.GroupStore',{
	extend : 'Ext.data.Store',
	//model: 'User',
	fields: ['id', 'grouptypeid', 'name', 'type', 'description', 'filterdata'],
	remoteSort:true,
	//totalProperty: 'recordcount',
	rootProperty:'data',
	autoLoad:true,
	proxy: 
	{
		type: 'ajax',
		url: MdiApp.admin.dispatch+'?control=GroupControl&method=View',
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