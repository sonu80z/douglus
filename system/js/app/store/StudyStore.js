Ext.define('MdiApp.store.StudyStore', {
	extend : 'Ext.data.Store',
	autoLoad:false,
                   // model: 'Studies',
	fields: ['uid','patientid','modality', 'datetime', 'reviewed', /*'patientname',*/ 'lastname', 'firstname', 'institution' , 'referringphysician', 'description', 'reviewed_text', 'is_critical', 'critical_date', 'mailed_date', 'images_cnt', 'has_attached_orders', 'note_date', 'note_text', 'note_user', 'dob', 'has_tech_notes'],
	remoteSort:true,
	autoLoad : true,
                    proxy:
                    {
                        type: 'ajax',
                        url: '/system/legacy/db/study.php?_dc=1569756896346',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: new Ext.data.JsonReader(
                        {
                            remoteSort:true,
                            totalProperty: 'recordcount',
                            rootProperty:'data'
                        })
                    },
                    pageSize: 20
                });