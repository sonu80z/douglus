Ext.define('MdiApp.view.main.Grid',{
	extend : 'Ext.grid.Panel',
	frame : true,
	constructor : function(config){
		var self=this;
		var columns= [
                                    {header: "Date", width:130, dataIndex: 'datetime'},
                                    {header: "Patient Id", dataIndex: 'patientid'},
                                    {header: '<span class="redtext" title="Marked as critical">&nbsp;!&nbsp;</span>', width:23, dataIndex: 'is_critical', sortable:false, renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value == '!')
                                            {
                                                var title = 'No email defined';
                                                if (record.data.critical_date)
                                                        title = 'Marked as critical and emailed on ' + record.data.critical_date;
                                                return '<div class="redtext" style="cursor:pointer;cursor:hand;" title="' + title + '">&nbsp;!&nbsp;</div>';
                                            }
                                            return '';
                                        }
                                    },
//                                  {header: "Patient Name", width:150, dataIndex: 'patientname'},
                                    {header: "Firstname", width:95, dataIndex: 'firstname'},
                                    {header: "Lastname", width:95, dataIndex: 'lastname'},
                                    {header: "DOB", width:85, dataIndex: 'dob'},
//                                  {header: "Study Id", width:150, dataIndex: 'id'},
                                    {header: "Study Description", width:150,  dataIndex: 'description'},
//                                  {header: "Images", dataIndex: 'images'},
                                    {header: "Referring Physician", width:130, dataIndex:'referringphysician'},
                                    {header: "Modality", dataIndex: 'modality'},
									{header: "institution", dataIndex: 'institution'},
                                    {header: "<span title=\"Images\">#</span>", width:20,  dataIndex:'images_cnt'},
                                    {header: '<img src="system/ico/order.png" height="16px" width="14px" title="Order"/>',menuDisabled:true, width:30, dataIndex: 'has_attached_orders', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value >= '1')
                                            {
                                                return '<a href="/orders/'+record.data.uid+'.pdf" target="_blank"><img src="system/ico/order.png" title="View Order" height="16px" width="14px"/></a>';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/emailed.png" height="16px" width="16px" title="Is emailed"/>',menuDisabled:true, width:30, dataIndex: 'mailed_date', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value != '')
                                            {
                                                return '<img src="system/ico/emailed.png" height="16px" width="16px" title="Emailed to facility on ' + record.data.mailed_date + '" style="cursor:pointer;cursor:hand;" />';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/eye_preview.png" title="Is reviewed" height="16px" width="16px"/>', width:30, dataIndex: 'reviewed_text', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value != '')
                                            {
                                                return '<img style="height:16px;width:16px;cursor:pointer;cursor:hand;" src="system/ico/eye_preview.png" title="' + value + '" />';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/note.png" title="Notes" height="16px" width="16px"/>', width:30, dataIndex: 'note_text', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value != '')
                                            {
                                                return '<div style="height:16px;width:16px;cursor:pointer;cursor:hand;" class="ico-note" onclick="mdi.study.showNoteWindow()" title="' + record.data.note_user + ' (' + record.data.note_date + ') :   ' + record.data.note_text + '"/>';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/tech_notes.png" title="Tech notes" height="16px" width="16px"/>', width:30, dataIndex: 'has_tech_notes', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value == '1')
                                            {
                                                return '<a href="' + mdi.admin.dispatch + '?control=StudyControl&method=getTechNote&study_id=' + record.data.uid + '"><img style="height:16px;width:16px;" src="/system/ico/tech_notes.png"/></a>';
                                            }
                                            return '';
                                        }},
                                    {id:'reviewed_text',header: "Reviewed Text", dataIndex: 'reviewed_text', width:200}
                    ]
					self.columns = columns;
					Ext.define('Studies', {
                extend: 'Ext.data.Model',
                fields: ['uid','patientid','modality', 'datetime', 'reviewed', /*'patientname',*/ 'lastname', 'firstname', 'institution' , 'referringphysician', 'description', 'reviewed_text', 'is_critical', 'critical_date', 'mailed_date', 'images_cnt', 'has_attached_orders', 'note_date', 'note_text', 'note_user', 'dob', 'has_tech_notes']
                });
					self.store=Ext.create('Ext.data.Store', {
                    autoLoad:false,
                    model: 'Studies',
                    remoteSort:true,
					autoLoad : true,
                    proxy:
                    {
                        type: 'ajax',
                        url: 'http://localhost/system/legacy/db/study.php?_dc=1569756896346',
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
						var pagingBar = new Ext.PagingToolbar({
//                    pageSize: 15,
                    store: self.store,
                    displayInfo: true,
                    displayMsg: 'Displaying studies {0} - {1} of {2}',
                    emptyMsg: "No Studies to display"
                });
		self.bbar = pagingBar;

		this.callParent(arguments);
	}
});