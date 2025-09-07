Ext.define('MdiApp.store.PatientStore', {
	extend : 'Ext.data.Store',
	fields: ['uid','id','patientid','modality','images', 'datetime', 'reviewed', 'patientname', 'lastname', 'firstname', 'institution' ,'referringphysician', 'description', 'reviewed_text', 'is_critical', 'critical_date', 'mailed_date', 'images_cnt'],
	// model: 'PatienStudies',
	remoteSort:true,
	totalProperty: 'recordcount',
	autoLoad: false,
	proxy: 
	{
		type: 'ajax',
		url: MdiApp.config.db,
		reader: 
		{
			type: 'json',
			rootProperty: 'data'
		}
	}
});