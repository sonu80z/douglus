Ext.define('MdiApp.store.GroupFilterSourceStore',{
	extend : 'Ext.data.Store',
	//model: 'FilterSource',
	remoteSort:true,
	fields: ['data'],
	
	autoLoad:true,
	proxy: 
	{
		type: 'ajax',
		url: MdiApp.admin.dispatch+'?control=GroupControl&method=ViewGroupTypeCriteria',
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