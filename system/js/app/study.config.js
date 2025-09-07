Ext.namespace('MdiApp').study = {
	viewDicom:function(grid)
	  {		  
			if(grid.getSelectionModel().getCount()){
					if(MdiApp.isCanViewHTML5Viewer){
						MdiApp.study.performStandardAction("DicomViewer", 'system/html5viewer/index.php', grid.getEntries());
					}else{    
						MdiApp.study.performStandardAction("DicomViewer", 'system/viewer/index.php', grid.getEntries());
					}
			}else{
					Ext.Msg.warn("Select a study to view the DICOM image");
			}
		},
	viewReport:function(grid){
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
	viewPriors:function(grid){
				//var grid=this.getView()
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
					var patientid = grid.getSelection()[0].data.patientid;
					patientStore.getProxy().extraParams.search = patientid;
					patientStore.load();
					w.show();
                }else{
                    Ext.Msg.warn("Select a patient to view the priors");
                }
			},
			mailReport:function(grid){				
				if(grid.getSelectionModel().getCount()){
                            MdiApp.performAjaxAction({params:{actions:"ViewReports"}, 
                                    success:function(){
                                        var aa = grid.getSelection();
                                        var id = aa[0].data.uid;
										var studymainview=grid.findParentByType('studymainview')
										//Ext.getCmp('sendmail-container').show()
										studymainview.setActiveItem(1)
                                        //var nw = window.open('/sendmail.php?study_id='+id);
                                        //checkChildWindow(nw, function(){grid.getStore().reload()});
                                    },
                                    failure:function(){
                                        Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
                                    }
                            }, grid);
                        }else{
                            Ext.Msg.warn("Please select the study");
                        }
			},
			createStudy:function(grid)
            {
				var nw = window.open('/new_study.php');
				checkChildWindow(nw, function(){grid.getStore().reload()});
		    },
			batchPrintDialogShow:function(grid){
				
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
			},
	logs:function(itemID)
		{
			var win=Ext.create('MdiApp.view.admin.LogWindow',{
				study_id : itemID
			})
			win.show()			
		},
	admin_remove_item:function(itemType,grid)
		{
			var self = this;
			var resultHandler = function(param)
			{
				if (param == "yes")
				{
//                                alert('aaaaaaaaaaa');
//                                5555555555555
					if(grid.getSelectionModel().getCount())
					{
						MdiApp.performAjaxAction({params:{actions:"remove the " + itemType},success:function(){
								grid.getStore().reload()
							},
							failure:function(result){
								Ext.Msg.warn(result.failure);
						}},grid);
					}
					else
					{
						Ext.Msg.warn("Operation filed");
					}
//                                mdi.study.addNote("");
//                                Ext.getCmp('window-add-note').hide();
				}
			}
			Ext.MessageBox.confirm('Confirm', 'Are you really want to remove the ' + itemType + '?', resultHandler);
		},
	fileUpload:function(formType,grid){
		var winTitle;
		var iconCls;
		var fieldLabel;
		if (formType == 'ORDER')
		{
			winTitle = 'Attach an Order';
			iconCls = 'ico-file-pdf';
			fieldLabel = 'Order';
		}
		if (formType == 'TECH_NOTES')
		{
			winTitle = 'Attach file with Tech Notes';
			iconCls = 'ico-file-pdf';
			fieldLabel = 'Tech notes';
		}
		var win = new Ext.Window({
			title:winTitle,
			iconCls: iconCls,
			bodyStyle:'padding:5px',
			//id:'window-attach-pdf-order',
			//height:250,
			width:330,
			modal:true,
			resizable:false,
			layout:'fit',
			items:[
					new Ext.form.FormPanel({
						id:'form-attach-pdf',
						frame:true,
						border:false,
						fileUpload:true,
						items:[
								{
									xtype: 'filefield',
									name: 'file-attachment',
									fieldLabel: fieldLabel,
									labelWidth: 100,
									msgTarget: 'side',
									allowBlank: false,
									anchor: '100%',
									buttonText: 'Select'
								}
						]
					})
			],
			buttons:[
					{text:"Cancel", handler:function(){win.close()}},
					{text:"Attach", handler: function(){MdiApp.study.attachPDF(formType,grid)}}
			]
		});
		win.show();
	},
	attachPDF:function(type,grid)
	{
			if(grid.getSelectionModel().getCount()){
					var urlParams = {entry:grid.getEntries(), option:'Study'};
					if (type == 'PDF')
							urlParams.actions='AttachPDF';
					if (type == 'ORDER')
							urlParams.actions='AttachORDER';
					if (type == 'TECH_NOTES')
							urlParams.actions='AttachTECH_NOTES';
					if (!urlParams.actions)
							Ext.Msg.warn('An error occured in function attachPDF. Parameter is "'+type+'"');

					Ext.getCmp('form-attach-pdf').getForm().submit({
							url:MdiApp.config.action, 
							params:urlParams,
							success:function(form, response){
									Ext.Msg.info("File attached successfully");
									var a = Ext.getCmp('window-attach-pdf');
									if (a)
											a.close();
									a = Ext.getCmp('window-attach-pdf-order');
									if (a)
										a.close();
									grid.getStore().reload()
							},
							failure:function(form, response){
									console.log(response.result);
									Ext.Msg.error("Failed to upload the file.<br />"+response.result.failure);
							}

					});
			}else{
					Ext.Msg.warn("Select the study to attach a pdf");
			}
		if (type == 'PDF')
			MdiApp.study.mailReportAttach(grid);
	},
	mailReportAttach:function(grid)
	{
		if(grid.getSelectionModel().getCount()){
			MdiApp.performAjaxAction({params:{actions:"ViewReports"}, 
					success:function(){
						var aa = grid.getSelection();
						var id = aa[0].data.uid;
						var nw = window.open('/automail.php?study_id='+id);
						checkChildWindow(nw, function(){grid.getStore().reload()});
					},
					failure:function(){
						Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
					}
			}, grid);
		}else{
			Ext.Msg.warn("Please select the study");
		}
	},
	download:function(grid)
	{
		
		var rs=grid.getSelection();
		var r=rs[0];
		if(rs.length==0){
			Ext.Msg.alert('Record not selected','Please select record');
			return ;
		}
		var entries=grid.getEntries();
		var win=Ext.create('Ext.Window',{
			height : 200,
			width : 300,
			layout : 'fit',
			title : 'Add zip to job',
			
			items : 
				{
					xtype : 'form',
					margin : 5,					
					url : '/system/actionItem.php',
					//standardSubmit : true,
					items : [
						{
							xtype : 'textfield',
							fieldLabel : 'ZIP file name',
							allowBlank : false,
							name : 'option'
						}/*,{
							xtype : 'textfield',
							fieldLabel : 'Extra directory',
							name : 'dir'
						}*/,{
							xtype : 'hidden',
							value : r.data.uid,
							name : 'entry[]'
						},{
							xtype : 'hidden',
							value : 'createZIP',
							name : 'actions'
						}
					],
					buttons : [
						{
							text : 'Add to job',
							handler : function(btn){
								var form=btn.findParentByType('form')
								form.getForm().submit({
									waitMsg : 'Wait files are being packed',
									waitTitle : 'Downloading...',
									success : function(form, action){
										win.close();
									//	window.open('/system/actionItem.php?action=DownloadZip&id='+action.result.id,'_blank');
									},
									failure:function(form, action){
										switch (action.failureType) {
											case Ext.form.action.Action.CLIENT_INVALID:
												Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
												break;
											case Ext.form.action.Action.CONNECT_FAILURE:
												Ext.Msg.alert('Failure', 'Ajax communication failed');
												break;
											case Ext.form.action.Action.SERVER_INVALID:
											   Ext.Msg.alert('Failure', action.result.msg);
									   }
										
									}
								});
							}
						},
						{
							text : 'Close',
							handler : function(){
								win.close()
							}
						}
					]
				}
			
		});
		win.show()
	},
	readyCDShow:function(grid){
		var cdStore = Ext.create('Ext.data.Store', 
                    {
                        //model: 'searchTypes',
                        remoteSort:true,
                        totalProperty: 'recordcount',
                        rootProperty:'data',
                        autoLoad:true,
                        proxy: 
                        {
                        type: 'ajax',
                        url: '/system/actionItem.php?actions=getJobQue',
						listeners : {
							exception : function(){
								win.close();
								Ext.Msg.alert('Error...','Fail to load data, try again');
							}
						},
                        reader: 
                            {
                                type: 'json',
                                rootProperty: 'data',
								totalProperty : 'total'
                            }
                        },
						fields: [
							 {name: 'id', type: 'int'},
							 {name: 'user_id',  type: 'int'},
							 {name: 'dbjob_id',       type: 'int'},
							 {name: 'status',  type: 'string'},
							 {name: 'type',  type: 'string'},
							 {name: 'filename',  type: 'string'}
						 ]
                    });
		var win=Ext.create('Ext.Window',{
			height : 300,
			width : 500,
			layout : 'fit',
			title : 'Job',
			items : {
				xtype : 'grid',
				listeners : {
					cellclick : function(view, td, cellIndex, record, tr, rowIndex, e, eOpts){
						console.log(e.getTarget('a'))
					}
				},
				columns: [
					{ text: 'File name', dataIndex: 'filename' ,renderer:function(value){
						if(value=='')
							return '(no name)';
						return value;
					} },
					{text:'Status',width:150,dataIndex: 'status', renderer: function(value,meta,record){
						switch(value){
							case 'complete':
								return Ext.String.format('<a target="_blank" href="/system/actionItem.php?actions=DownloadZipIso&id={0}">Download</a>',record.data.id);
							case 'pending':
								return 'pending'
							default:
								return value;
						}
					}},
					{text : 'Type', dataIndex:'type'},
					{text : 'Action',xtype: 'actioncolumn',icon:'/system/ico/delete_task.png', dataIndex:'id',handler:function(view,row,col,item,e,record){
						Ext.Msg.confirm('Delete','Are you sure to delete',function(btn){
							if(btn=='yes'){
								Ext.Ajax.request({
									url : 'system/actionItem.php',
									params:{actions:'deleteJobQue',id:record.data.id},
									success : function(response, opts){
										var obj = Ext.decode(response.responseText);
										if(obj.success==true){
											cdStore.reload();
										}else{
											Ext.Msg.alert('Error...','Fail to delete');
										}
									},
									failure : function(){
										
									}
								})
							}
						});
					}}
				],
				store : cdStore,
				bbar: {
					xtype: 'pagingtoolbar',
					displayInfo: true,
					store : cdStore
				}
			}
		});
		win.show();
	},
	burnCDShow:function(grid)
	{
		var rs=grid.getSelection();
		var r=rs[0];
		
		if(rs.length==0){
			Ext.Msg.alert('Record not selected','Please select record');
			return ;
		}
		
		var entries=grid.getEntries();
		var win=Ext.create('Ext.Window',{
			height : 200,
			width : 300,
			layout : 'fit',
			title : 'Add ISO to job',
			
			items : 
				{
					xtype : 'form',
					margin : 5,					
					url : '/system/actionItem.php',
					//standardSubmit : true,
					items : [
						{
							xtype : 'textfield',
							fieldLabel : 'CD ISO file name',
							allowBlank : false,
							name : 'option'
						},/*{
							xtype : 'textfield',
							fieldLabel : 'Extra directory',
							name : 'dir'
						},*/{
							xtype : 'hidden',
							value : entries,
							name : 'entry'
						},{
							xtype : 'hidden',
							value : 'createISO',
							name : 'actions'
						}
					],
					buttons : [
						{
							text : 'Add to job',
							handler : function(btn){
								var form=btn.findParentByType('form')
								form.getForm().submit({
									waitMsg : 'Wait files are being packed',
									waitTitle : 'Adding...',
									success : function(form, action){
										console.log(action.result.files)
										win.close();
										Ext.Msg.alert('Success','Study added to schedule')
										//window.open('/system/actionItem.php?action=DownloadIso&id='+action.result.id,'_blank');
									},
									failure:function(form, action){
										switch (action.failureType) {
											case Ext.form.action.Action.CLIENT_INVALID:
												Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
												break;
											case Ext.form.action.Action.CONNECT_FAILURE:
												Ext.Msg.alert('Failure', 'Ajax communication failed');
												break;
											case Ext.form.action.Action.SERVER_INVALID:
											   Ext.Msg.alert('Failure', action.result.msg);
									   }
										
									}
								});
							}
						},
						{
							text : 'Close',
							handler : function(){
								win.close()
							}
						}
					]
				}
			
		});
		win.show()
	},
	burnCDShow_old:function(grid)
	{
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
		var selection = grid//Ext.getCmp('patient-grid');
		if (selection)
		{
			selection = grid.getSelection()//mdi.getSelectedRows(selection);
			for(i = 0; i < selection.length;i++)
				entry.push(selection[i].data.uid);
			studiesID = entry.join(",");
		}
		
		i = 0;
		if (studiesID.length == 0)
		{
			var selectedStudies = mdi.getSelectedRows(Ext.getCmp('studyGrid'));
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
	markAsReviewd : function(grid){
		MdiApp.performAjaxAction({params:{actions:"Mark Study Reviewed"},success:function(){
								console.log(arguments)
								grid.getStore().reload()
							},
							failure:function(result){
								Ext.Msg.warn(result.failure);
							}},grid);
	},
	markAsCritical : function(grid){
		MdiApp.performAjaxAction({params:{actions:"Mark Study as Critical"},success:function(){
								console.log(arguments)
								grid.getStore().reload()
							},
							failure:function(){}},grid);
	},
	markAsUnCritical:function(grid){
		MdiApp.performAjaxAction({params:{actions:"Mark Study as Uncritical"},success:function(){
							console.log(arguments)
							grid.getStore().reload();
						},failure:function(){}},grid);
	},
	showNoteWindow:function(grid){
		if(!grid)
			grid=Ext.getCmp('studyGrid')
		var win=Ext.create('MdiApp.view.main.NoteWindow',{
			grid:grid
		})
		win.show();
	},
	addNote:function(noteText,grid)
	{
			if(grid.getSelectionModel().getCount())
			{
					var urlParams = {entry:grid.getEntries(), option:'Study', text:noteText, actions:'addNOTE'};
					Ext.Ajax.post({
							url:MdiApp.config.action,
							params:urlParams,
							success:function(result)
							{
								grid.getStore().reload();
								if(!result.success) 
								{
									Ext.Msg.warn(result.failure);
								}
							},
							failure:function(form, response)
							{
								Ext.Msg.error("Failed to request.");
							}
					});
			}
	},
	performStandardAction:function(action, url, itemStr)
                    {//console.log(grid.getSelectionModel())
                        if(typeof url == "undefined")
                                url = MdiApp.config.action;
                     /*   var selectedItems = grid.getSelection()//mdi.getSelectedRows(grid);
                        var l = selectedItems.length;
                        var item;
                        var itemStr = '';
                        for (i = 0; i < l; i++)
                        {
                            item = selectedItems[i];
                            if (itemStr.length)
                                itemStr += ',' + item.data.uid;
                            else
                                itemStr = item.data.uid;
                        }*/

                        url = url + '?entry=' + itemStr + '&actions=' + action;
						console.log(url)
						console.log(MdiApp.Application.getApplication)
						//Ext.getDom ('iframe-tag').src=url
						//Ext.getCmp('mainview').setActiveItem(1)
						window.open(url,'_blank' );
                        //window.location.href = url;
                    }
	}
	
	Ext.applyIf(MdiApp.study, {
	attachmentDirectory:"../attachments/",
	viewNotesAndAttachments:function(grid){
		uid = "";
                var selRows = grid.getSelection()//mdi.getSelectedRows(grid);
		if(selRows.length)
                {
                    uid = selRows[0].data.uid; 
		}
                else
                {
                    Ext.Msg.warn("Select a study to view the attachment");
                    return false;
		}
		Ext.Ajax.request({
		   method:'POST',
		   url: 'system/legacy/db/studyAttachments.php',
		   params:{uid:uid},
		   success: function(data){
		   		var result = Ext.decode(data.responseText);
				if (result.success) {
					if(result.notes.length == 0 && result.attachments.length == 0){
						Ext.Msg.info("There are no attachments.");
						return false;
					}
					var tabs = [];
					for(var i = 0; i < result.notes.length;i++){
						tabs.push(new Ext.Panel({
							title:result.notes[i].headline,
							iconCls:'ico-study-note',
							html:result.notes[i].notes
						}))
					}
					var data = [];
					if (result.attachments.length != 0) {
						for (var i = 0; i < result.attachments.length; i++) {
							var filename = result.attachments[i].path.replace(/^.+\//, "");
							data.push([result.attachments[i].id, result.attachments[i].size, mdi.attachmentDirectory+filename, filename]);
						}
						var attachmentStore = new Ext.data.SimpleStore({
							data: data,
							id:'id',
							fields: [{
								name: 'id'
							}, {
								name: 'size'
							},{
								name: 'path'
							},{
								name: 'name'
							}]
						});
						var attachments = new Ext.grid.GridPanel({
							id:'study-attachments-grid',
							store: attachmentStore,
							iconCls: 'ico-study-attachment',
							sm: new Ext.grid.RowSelectionModel({
								singleSelect: true
							}),
							columns: [{
								header: "Size",
								width: 65,
								sortable: true,
								dataIndex: 'size'
							}, {
								header: "Name",
								width: 160,
								sortable: true,
								dataIndex: 'name'
							}],
							autoExpandColumn: 1,
							title: 'Attachments',
							tbar: new Ext.Toolbar({
								items: [{
									text: 'download',
									iconCls: 'ico-download',
									handler:function(){
										var selected = Ext.getCmp('study-attachments-grid').getSelectionModel().getSelected();
										if(selected != null){
											//window.open(selected.data.path);
											MdiApp.study.performAjaxAction({params:{actions:"DownloadAttachment", attachedFile:selected.data.filename}, 
												success:function(){
													window.open(selected.data.path);
												},
												failure:function(){
													Ext.Msg.error('The attached file cannot be downloaded (file was not found).');
												}
											});
										}else{
											Ext.Msg.warn("Select the attachment, that you want to download.");
										}
										
									}	
								}]
							})
						});
						tabs.push(attachments);
					}
					var tabPanel = new Ext.TabPanel({
						region:'center', 
						activeTab:0, 
						title:'center',
						defaults: {autoScroll:true},
						enableTabScroll:true, 
						items:tabs
					});
					var win = new Ext.Window({
						id:'study-attachments',
						width:300,
						height:300,
						resizable:false,
						layout:'border',
						title:'Notes & Attachments',
						items:[tabPanel],
						buttons:[{text:'close', handler:function(){Ext.getCmp('study-attachments').close();}}]
					});
					win.show();
				}
		   }
		});						
	}
});