Ext.define('MdiApp.store.MailStore',{
	extend : 'Ext.data.Store',
	//model: 'User',
	fields: ['id', 'mail', 'institution','autofax'],
	remoteSort:true,
	totalProperty: 'recordcount',
	rootProperty:'data',
	autoLoad:true,
	proxy: 
	{
		type: 'ajax',
		url: MdiApp.admin.dispatch+'?control=MailControl&method=ViewInstifutionMails',
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