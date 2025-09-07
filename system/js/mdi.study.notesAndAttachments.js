Ext.applyIf(mdi.study, {
	attachmentDirectory:"../attachments/",
	viewNotesAndAttachments:function(grid){
		uid = "";
                var selRows = mdi.getSelectedRows(grid);
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
											mdi.study.performAjaxAction({params:{actions:"DownloadAttachment", attachedFile:selected.data.filename}, 
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
