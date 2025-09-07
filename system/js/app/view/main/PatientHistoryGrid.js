Ext.define('MdiApp.view.main.PatientHistoryGrid',{
	extend : 'Ext.grid.Panel',
	iconCls:'ico-study',
	columns: [
			{header: "Date", width:100, sortable: true, dataIndex: 'datetime'},
			{header: "Study Id", width:100, sortable: true, dataIndex: 'uid'},
			{header: "Modality", sortable: true, dataIndex: 'modality'},
			{header: "Study Description", width:100,  sortable: true, dataIndex: 'description', flex: true}
	],
	stripeRows: true,
	viewConfig:{
		getRowClass : function (row, index, rowParams, store) {
			var cls = 'study-read';
			if(row.data.reviewed == "") cls = 'study-unread';
				return cls;
		}
    },
	//id:'patient-grid',
	//title:'Patient History',
	selModel:{mode: 'MULTI'},
	multiselect:'true',
	constructor : function(config){
		var self=this;
		config=config||{};
		var patientPagingBar = new Ext.PagingToolbar({
                        pageSize: 15,
                        store: config.store,
                        displayInfo: true,
                        displayMsg: 'Displaying studies {0} - {1} of {2}',
                        emptyMsg: "No Studies to display"
                });
		var patientsMenuBar = new Ext.toolbar.Toolbar({
                            id:'patients-menu',
                            items:[
                                    {text:'Notes/Attachments', iconCls:'ico-study-note-attachment', handler:function(){MdiApp.study.viewNotesAndAttachments(self);}},					
                                    //{text:'View', iconCls:'ico-study-viewer', handler: function(){ mdi.study.viewDicom();}},
                                    {text:'Report', iconCls:'ico-study-view-report', handler:function(){MdiApp.study.viewReport(self);}},
                                    {text:'Batch print', iconCls:'ico-batch-print' 
                                        ,handler:function()
                                        {
                                            var selection = self.getSelectionModel().getSelection();
                                            var entry = [];
                                            for(var i = 0; i < selection.length;i++)entry.push(selection[i].data.uid);
                                            var studiesID = entry.join(",");

                                            if (!studiesID)
                                            {
                                                Ext.Msg.error("Please select one or more studies");
                                                return 1;
                                            }
                                            var doRequest = function(progressID)
                                            {
                                                var params = {studies: studiesID,
                                                                control: 'BatchPrintControl',
                                                                method: 'processPriors',
                                                                progress_id : progressID};
                                                Ext.Ajax.request({
                                                        url: 'system/dispatch.php',
                                                        disableCaching: true,
                                                        params: params,
                                                        success: function(response)
                                                        {
                                                        },
                                                        failure: function()
                                                        {
                                                        }
                                                });
                                            };	
                                            var params={control: 'BatchPrintControl',
                                                                    method: 'getProcessID'};
                                            Ext.Ajax.request({
                                                                url: 'system/dispatch.php',
                                                                disableCaching: true,
                                                                params: params,
                                                                success: function(response)
                                                                {
                                                                    var res = Ext.decode(response.responseText);
                                                                    if (res.success && !res.error_msg)
                                                                    {

                                                                        doRequest(res.process_id);
                                                                        window.open('/system/viewer/batchPrintView2.php?processID=' + res.process_id);
        //                                                                                  win.close();
                                                                    }
                                                                    else
                                                                        Ext.Msg.error(res.error_msg);
                                                                },
                                                                failure: function()
                                                                {
                                                                    Ext.Msg.error("Failed to request");
                                                                }
                                                            });
                                            return 1;
                                        }
                                    },
                                    {text:'Burn CD', iconCls:'ico-burncd', id:'menu-burn-cd-button2', handler:function(){MdiApp.study.burnCDShow(self)}}
                            ]
                  })
		config.tbar=patientsMenuBar;
		config.bbar=patientPagingBar
		this.callParent(arguments);
	}
});