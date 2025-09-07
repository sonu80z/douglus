Ext.define('MdiApp.view.main.StudyGridViewController',{
	extend : 'Ext.app.ViewController',
	markAsReviewd : function(){
		var grid=this.getView()
		MdiApp.performAjaxAction({params:{actions:"Mark Study Reviewed"},success:function(){
								console.log(arguments)
								grid.getStore().reload()
							},
							failure:function(result){
								Ext.Msg.warn(result.failure);
							}},grid);
	},
	markAsCritical : function(){
		var grid=this.getView()
		MdiApp.performAjaxAction({params:{actions:"Mark Study as Critical"},success:function(){
								console.log(arguments)
								grid.getStore().reload()
							},
							failure:function(){}},grid);
	},
	markAsUnCritical:function(){
		var grid=this.getView()
		MdiApp.performAjaxAction({params:{actions:"Mark Study as Uncritical"},success:function(){
							console.log(arguments)
							grid.getStore().reload();
						},failure:function(){}},grid);
	},
	showNoteWindow:function(grid){
		var grid=this.getView()
		 var noteTextOld = '';
                            var tpl=new Ext.XTemplate('<table style="width:100%">',
                            '<tr><td> Patient Name: </td><td> <b>{name}</b> </td><td></td>',
                                    '<td> Note added by:</td><td> <b>{added_user}</b> </td>',
                            '</tr>',
                            '<tr><td> Study Date: </td><td><b> <b>{date}</b> </td><td></td>',
                                    '<td> Note added on:</td><td> <b>{added_date}</b></td>',
                            '</tr>',
                            '<tr><td> Referring Physician: </td><td> <b>{physician}</b> </td><td></td>',
                                    '<td> Modality: </td><td> <b>{modality}</b> </td>',
                            '</tr>',
                            '<tr><td> Study Description: </td><td> <b>{descr}</b> </td><td></td>',
                                    '<td></td>',
                            '</tr>',
                            '</table>')
						//	var tpl=new Ext.XTemplate(htmlContent)
                            if(grid.getSelectionModel().getCount())
                            {
                                    var selectedStudies = grid.getSelection();
                                    var curStudy = selectedStudies[0];
                                    noteTextOld = curStudy.data.note_text;
									var data={
										name:curStudy.data.lastname + ' ' + curStudy.data.firstname,
										added_user:curStudy.data.note_user,
										date:curStudy.data.datetime,
										added_date:curStudy.data.note_date,
										physician:curStudy.data.referringphysician,
										descr:curStudy.data.description,
										modality:curStudy.data.modality
									}
                                    if (Ext.getCmp('window-add-note'))
                                            Ext.getCmp('window-add-note').hide();
                            }

                            var tb = new Ext.Toolbar({
                                    /*//id: 'note-tb',*/
                                    items:[
                                                {text:'Print the Note', iconCls: 'ico-batch-print', handler:function(){window.open('/system/dispatch.php?control=UserControl&method=printStudyNote&studyID=' + curStudy.data.uid);}},
                                                {text:'Remove the Note', /*id: 'remove-note-btn',*/ hidden:(MdiApp.isAdmin == 0), iconCls: 'ico-un-review', handler:function()
                                                    {
                                                        var resultHandler = function(param)
                                                        {
                                                            if (param == "yes")
                                                            {
                                                                MdiApp.study.addNote("",grid);
																win.close()
                                                                //Ext.getCmp('window-add-note').hide();
                                                            }
                                                        }
                                                        Ext.MessageBox.confirm('Confirm', 'Are you really want to remove the Note?', resultHandler);
                                                    }
                                                }
                                          ]
                            });
							var note_textarea=Ext.create('Ext.form.field.TextArea',{
								  xtype     : 'textareafield',
								  //grow      : true,
								  name      : 'message',
								  anchor    : '100%',
							 /*//     id        : 'note_textarea',*/
								  value     : noteTextOld
								})
							var study_note_footer_text=Ext.create('Ext.form.Label',{html:' '})
                            var win = new Ext.Window({
                                    title:'Note',
                                    iconCls: 'ico-note',
                                    bodyStyle:'padding:5px; background: #6F839A;',
                                   // id:'window-add-note',
									closeAction : 'destroy',
                                    height:400,
                                    width:600,
                                    modal:true,
                                    resizable:false,
                                    tbar:tb,						
                                    items:[
                                            {
                                                /*//    id:'study-note-header',*/
                                                    xtype: 'panel',
                                                    bodyStyle  : '{background: #6F839A; float:left;width:100%;padding:10px 0; color:white;}',
                                                    //html: htmlContent
													tpl : tpl,
													data : data
                                              },
                                              new Ext.form.FormPanel({
                                              //width      : 588,
                                              bodyPadding: 2,
                                              items: [note_textarea]
                                                }),
                                                {
                                                    /*//id:'study-note-footer',*/
                                                    xtype: 'panel',
                                                    bodyStyle  : '{background: #6F839A; float:left;width:100%;padding:10px 0; color:white;}',
                                                    items:[study_note_footer_text]
                                                }
                                    ],
                                    buttons:[
                                            {text:"Cancel", id:'note-btn-cancel', handler:function()
                                                    {
                                                        win.close()}
                                                    },
                                            {text:"Save", id:'note-btn-save', handler: function()
                                                    {
                                                        MdiApp.study.addNote(note_textarea.getValue(),grid); 
                                                        win.close()
                                                    }
                                            }
                                    ]
                            });
                            win.show();
                           
                                    note_textarea.on('keypress',function(e)
                                                    {
                                                        var a = note_textarea.getValue();
                                                        // does not works in IE
                                                        if (a)
                                                            study_note_footer_text.getEl().update((a.length) + ' / 400 Chars');
                                                    }); 	
                            if (curStudy.data.note_text)
                                if (Ext.getCmp('note-btn-save'))
                                        Ext.getCmp('note-btn-save').hide();
	},
	'viewDicom':function(){
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
			'viewReport':function(){
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
			'mailReport':function(){
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
			
			'burnCDShow':function(){
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