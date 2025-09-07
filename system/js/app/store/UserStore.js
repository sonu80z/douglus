Ext.define('MdiApp.store.UserStore',{
	extend : 'Ext.data.Store',
	//model: 'User',
	fields: ['id', 'firstname', 'middlename', 'lastname', 'username',  'selfonly', 'admin', 'passwordexpired', 'canmailpdf', 'canbatchprintpdfs', 'canmarkasreviewed', 'canburncd', 'canmarkcritical', 'canattachorder', 'canaddnote', 'canviewhtml5viewer', 'staffrole'],
	remoteSort:true,
	totalProperty: 'recordcount',
	rootProperty:'data',
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
			rootProperty: 'data'
		}
	}
})