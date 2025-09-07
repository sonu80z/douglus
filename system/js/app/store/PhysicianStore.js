Ext.define('MdiApp.store.PhysicianStore',{
	extend : 'Ext.data.Store',
	//model: 'User',
	fields: ['id', 'mail', 'username'],
	remoteSort:true,
	totalProperty: 'recordcount',
	rootProperty:'data',
	autoLoad:true,
	proxy: 
	{
		type: 'ajax',
		url: MdiApp.admin.dispatch+'?control=UserControl&method=ViewPhysicansGrid',
		actionMethods: {
		//	read: 'POST'
		},
		reader: 
		{
			type: 'json',
			rootProperty: 'data',
			totalProperty: 'recordcount'
		}
	}
})