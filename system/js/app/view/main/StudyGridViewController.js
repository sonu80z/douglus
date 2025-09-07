Ext.define('MdiApp.view.main.StudyGridViewController',{
	extend : 'Ext.app.ViewController',
	'mdi.study.viewDicom':function(){
			var grid=this.getView()
				if(grid.getSelectionModel().getCount()){
					if(MdiApp.isCanViewHTML5Viewer){
						MdiApp.study.performStandardAction("DicomViewer", 'system/html5viewer/index.php', grid);
					}else{    
						MdiApp.study.performStandardAction("DicomViewer", 'system/viewer/index.php', grid);
					}
				}else{
					Ext.Msg.warn("Select a study to view the DICOM image");
				}
			},
			'mdi.study.viewReport':function(){
				var grid=this.getView()
				if(grid.getSelectionModel().getCount()){
                                var id = grid.getSelection()[0].data.uid;
                                MdiApp.performAjaxAction({params:{actions:"ViewReports"}, 
                                    success:function(){
                                        window.open('../transcriptions/'+id+'.pdf');
                                    },
                                    failure:function(){
                                        Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
                                    }
                                }, grid);
                        }else{
                            Ext.Msg.warn("Select a study to view the report");
                        }
			},
			'mdi.study.mailReport':function(){
				var grid=this.getView()
				if(grid.getSelectionModel().getCount()){
                            MdiApp.performAjaxAction({params:{actions:"ViewReports"}, 
                                    success:function(){
                                        var aa = grid.getSelections();
                                        var id = aa[0].data.uid;
                                        var nw = window.open('/sendmail.php?study_id='+id);
                                        checkChildWindow(nw, function(){grid.refresh()});
                                    },
                                    failure:function(){
                                        Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
                                    }
                            }, grid);
                        }else{
                            Ext.Msg.warn("Please select the study");
                        }
			},
			'mdi.study.viewPriors':function(){
				var grid=this.getView()
				if(grid.getSelectionModel().getCount()){
					
					var patientStore = Ext.create('MdiApp.store.PatientStore', {
                    //model: 'PatienStudies',
                    listeners:
                    {
                        'load':function(self, records, success)
                        {
                            if (records && records[0] && records[0].data)
								patientGrid.setTitle(records[0].data.patientname)
                                //Ext.getCmp('patient-grid').setTitle(records[0].data.patientname);
                        }
                    }
					});
					var patientGrid=Ext.create('MdiApp.view.main.PatientHistoryGrid',{
						listeners:{
							'itemdblclick' : function(row,rowIndex,e){
								MdiApp.study.viewDicom(patientGrid);
								}
							},
							store:patientStore
						})
					var w=Ext.create('Ext.Window',{
						layout : 'fit',
						title : 'Priors',
						height:340,
						width:540,
						items : patientGrid
					})
					var a = grid.getSelection()[0].data.patientid;
					patientStore.getProxy().extraParams.search = a;
					patientStore.load();
					w.show();
                }else{
                    Ext.Msg.warn("Select a patient to view the priors");
                }
			},
			
			'mdi.study.burnCDShow':function(){
				var grid=this.getView()
				var go2BurnCDWaitPage = function(selStudies)
                        {
                            if (!selStudies)
                                    return;
                            var params = {
                                            selected_studies:selStudies,
                                            control: 'BurnCDControl',
                                            method: 'getProcessID'
                                          }
                            Ext.Ajax.request({
                                url: 'system/dispatch.php',
                                disableCaching: true,
                                params: params,
                                success: function(response)
                                {
                                    try
                                    {
                                        var res = Ext.decode(response.responseText);
                                    }
                                    catch (e)
                                    {
                                        Ext.Msg.error('Server error: ' + response.responseText);
                                    }
                                    if (res.success && !res.error_msg)
                                    {
                                        var a234 = '/system/viewer/CDBurnerWaitPage.php?processID=' + res.process_id;
                                        window.open(a234);
                                    }
                                    else
                                        Ext.Msg.error(res.error_msg);
                                },
                                failure: function()
                                {
                                    Ext.Msg.error("Failed to request");
                                }
                            });
                        }
                        var studiesID = '';
                        var i = 0;
						var entry = [];
                        
                        i = 0;
						// try to get studies fot burning CD from the history window
						var selection = Ext.getCmp('patient-grid');
						if (selection)
						{
							selection = mdi.getSelectedRows(selection);
							for(i = 0; i < selection.length;i++)
								entry.push(selection[i].data.uid);
							studiesID = entry.join(",");
						}
						
                        i = 0;
                        if (studiesID.length == 0)
                        {
							var selectedStudies = grid.getSelection()//mdi.getSelectedRows(Ext.getCmp('study-grid'));
							if (selectedStudies)
							{
								for (i = 0; i < selectedStudies.length; i++)
								{
									entry.push(selectedStudies[i].data.uid);
								}
								studiesID = entry.join(","); 
							}
                        }
                        
                        if (studiesID == '')
                        {
                                Ext.Msg.warn("Select the studies you would like to export");
                                return;
                        }
                        go2BurnCDWaitPage(studiesID);
			},
			'mdi.study.batchPrintDialogShow':function(){
				var grid=this.getView()
				var startDateEmptyText = new Date();
                            
                            var stopDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');
                            
                            startDateEmptyText.setDate(startDateEmptyText.getDate() - 1);
                            startDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');

                            var buttonBatchPrintGoHandler = function(btn)
                            {
                                var params = {
                                                control: 'BatchPrintControl',
                                                method: 'getProcessID'
                                             }
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
    //								win.close();
                                        }
                                        else
                                            Ext.Msg.error(res.error_msg);
                                    },
                                    failure: function()
                                    {
                                        Ext.Msg.error("Failed to request");
                                    }
                                });
                            }
                        
                            var win = new Ext.Window({
                                    title:'Batch printing of the PDFs',
                                    iconCls: 'ico-batch-print',
                                    bodyStyle:'padding:5px',
                                    id:'window-batch-print-pdf',
                                    height:250, 
                                    width:300,
                                    modal:true,
//                                    resizable:false,
                                    buttons: [{text:"Print pdfs", id:'buttonBatchPrintGo', listeners : {'click': function()
                                                                                                                    {
                                                                                                                        buttonBatchPrintGoHandler();
                                                                                                                    }}}],
                                    items:[
                                            new Ext.form.Label(
                                               {
                                                  cls: 'x-form-item myBold',
                                                  text: 'Please select start and stop dates and press "Print pdfs":'
                                               }),
                                            {
                                                id: 'batchprint_date_start',
                                                xtype: 'datefield',
                                                name: 'datestart',
                                                fieldLabel: 'Date Start',
                                                value: startDateEmptyText,
                                                labelWidth: 62,
                                                width: 200//,
//                                                listeners:{'enter': buttonBatchPrintGoHandler}
                                            },
                                            {
                                                id: 'batchprint_date_stop',
                                                xtype: 'datefield',
                                                name: 'datestop',
                                                fieldLabel: 'Date Stop',
                                                value: stopDateEmptyText,
                                                labelWidth: 62,
                                                width: 200//,
//                                                listeners:{'enter': buttonBatchPrintGoHandler}
                                            }
                                        ]

                            });
                            var doRequest = function(progressID)
                            { 
                                var params = {dstart:Ext.Date.format(Ext.getCmp('batchprint_date_start').getValue(), 'Y-m-d'), 
                                dstop: Ext.Date.format(Ext.getCmp('batchprint_date_stop').getValue(), 'Y-m-d'),
                                control: 'BatchPrintControl',
                                method: 'process',
                                progress_id : progressID
                                }
                                Ext.Ajax.request({
                                    url: 'system/dispatch.php',
                                    disableCaching: true,
                                    params: params,
                                    success: function(response)
                                    {
										console.log(response.responseText);
                                    },
                                    failure: function()
                                    {
                                    }
                                });
                            }

                            win.show();
			}
})