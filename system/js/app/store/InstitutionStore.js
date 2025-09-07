Ext.define('MdiApp.store.InstitutionStore',{
	extend : 'Ext.data.Store',
	
//	model: 'institutionDescrStore',
	fields: ['institution'],
	remoteSort:true,
	autoLoad:true,
	totalProperty: 'recordcount',
	rootProperty:'data',
	proxy: 
	{
	type: 'ajax',
	url: 'system/dispatch.php?control=MailControl&method=ViewInstitution',
	reader: 
		{
			type: 'json',
			rootProperty: 'data'
		}
	}
	
})