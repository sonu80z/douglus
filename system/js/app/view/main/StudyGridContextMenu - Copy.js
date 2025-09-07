Ext.define('MdiApp.view.main.StudyGridContextMenu',{
	extend : 'Ext.menu.Menu',
	constructor : function(config){
		var self=this;
		config=config||{};
		config.items=[];
		this.callParent(arguments);
	},
	initComponent: function(config) {
		//var grid=config.grid;
		//var record=config.record;
		this.callParent();
		var items=[];
		var priors={
					  text: 'Priors',
					  iconCls: 'ico-history',
					  handler: 'viewPriors'/*function(){
						  MdiApp.study.viewPriors(grid)
					//	mdi.study.viewPriors();
					  }*/
					}
		var	attachReport=Ext.create('Ext.menu.Item',{
					//	id:'context-menu-attach-pdf',
						text: 'Attach report',
						//hidden:(MdiApp.isAdmin == 0),
						iconCls: 'ico-file-pdf',

						handler: function(){
								var win = new Ext.Window({
										title:'Attach report',
										iconCls: 'ico-file-pdf',
										bodyStyle:'padding:5px',
										id:'window-attach-pdf',
										height:115,
										width:330,
										modal:true,
										resizable:false,
										items:[
												new Ext.form.FormPanel({
														id:'form-attach-pdf',
														frame:true,
														border:false,
														fileUpload:true,
														items:[
//                                                                                    {id: 'file-attachment',xtype:'fileuploadfield',name: 'file-attachment'}
																{
																	xtype: 'filefield',
																	name: 'file-attachment',
																	fieldLabel: '*.pdf file',
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
												{text:"Cancel", handler:function(){Ext.getCmp('window-attach-pdf').close()}},
												{text:"Attach", handler: function(){mdi.study.attachPDF('PDF')}}
												]
								});
								win.show();
						}
				})
		var emailTheReport=Ext.create('Ext.menu.Item',{
				  text: 'Email the report',
				  iconCls: 'ico-mail',
				  auth : 'isCanMailPDF',
				  handler: 'mailReport'/* function(){
						MdiApp.study.mailReport(grid);
				  }*/
				})
		var markReviewd = Ext.create('Ext.menu.Item',{
				text: 'Mark as reviewed',
				//id:'menu-item-mark-reviewd',
				iconCls: 'ico-review',
				auth : 'isCanMarkAsReviewed',
				handler: 'markAsReviewd'/*function(){
					MdiApp.study.markAsReviewd(grid);
				}*/
			});
		var markCritical = Ext.create('Ext.menu.Item',{
					text: 'Mark as critical',
				//	id:'menu-item-mark-critical',
					iconCls: 'ico-critical',
					auth : 'isCanMarkCritical',
					handler: 'markAsCritical'/*function(){
						MdiApp.study.markAsCritical(grid);
					}*/
			});
		var markUncritical = Ext.create('Ext.menu.Item',{
					text: 'Remove critical status',
				//	id:'menu-item-mark-uncritical',
					iconCls: 'ico-uncritical',
					auth : 'isCanMarkCritical',
					handler: 'markAsUnCritical'/*function(){
						MdiApp.study.markAsUnCritical(grid);
					}*/
			});
		var addNote = Ext.create('Ext.menu.Item',{
				text: 'Add Note',
		//		id:'menu-item-add-note',
				iconCls: 'ico-note',
				auth : 'isCanAddNote',
				handler: 'showNoteWindow'/*function(){
						MdiApp.study.showNoteWindow(grid);
				}*/
			});
		var attachOrder = Ext.create('Ext.menu.Item',{
				text: 'Attach an Order',
		//		id:'menu-item-attach-order',
				iconCls: 'ico-order',
				auth : 'isCanAttachOrder',
				handler: function()
				{
					MdiApp.study.fileUpload('ORDER',grid);
					//mdi.fileUpload('ORDER');
				}
			});
		var tech_notes = Ext.create('Ext.menu.Item',{
				text: 'Attach tech notes',
		//		id:'menu-item-attach-tech_notes',
				iconCls: 'ico-tech_notes',
				auth : 'isCanAttachOrder',
				handler: function()
				{
					MdiApp.study.fileUpload('TECH_NOTES',grid);
				}
			});
		items.push(priors);
		items.push(attachReport)
		items.push(emailTheReport)
		items.push(markReviewd);
		items.push(markCritical);
		items.push(markUncritical);
		items.push(addNote);
		items.push(attachOrder);
		items.push(tech_notes);
		items.push(
			Ext.create('Ext.menu.Item',{
				text: 'Mark as UNReviewed',
				iconCls: 'ico-un-review',
				auth : 'isAdmin',
				handler: function()
				{
					MdiApp.performAjaxAction({params:{actions:"Mark Study UNReviewed"},success:function(){
							console.log(arguments)
							grid.getStore().reload();
						},failure:function(){}},grid);
					grid.getStore().reload()
					//mdi.study.markAsUnReviewd();
				}
			}));

		items.push(
			Ext.create('Ext.menu.Item',{
				text: 'Show history',
				iconCls: 'ico-admin-logs',
				auth : 'isAdmin',
				handler: function()
				{
					var aID = grid.getSelection()//mdi.getSelectedRows();
					aID = aID[0].data.uid;
					MdiApp.study.logs(aID);
//                                console.log();
				}
			}));

			items.push(
			Ext.create('Ext.menu.Item',{
				text: 'Remove ...',
				auth : 'isAdmin',
				iconCls: 'ico-admin_remove_report',
				menu : [
					{
						text: 'report',
						iconCls: 'ico-admin_remove_report',
						handler: function()
						{
							MdiApp.study.admin_remove_item('report',grid);
						}
					},
					{
						text: 'order',
						iconCls: 'ico-admin_remove_order',
						handler: function()
						{
							MdiApp.study.admin_remove_item('order',grid);
						}
					},
					{
						text: 'note',
						iconCls: 'ico-admin_remove_order',
						handler: function()
						{
							MdiApp.study.addNote("",grid);
						}
					},
					{
						text: 'tech note',
						iconCls: 'ico-admin_remove_order',
						handler: function()
						{
							MdiApp.study.admin_remove_item('TECH_NOTES',grid);
						}
					}
				]
			}));
		var self=this
		Ext.Array.forEach(items,function(item){
					self.add(item)
				})
		
		
		this.on('show',function(){
			console.log('*****')
			Ext.Array.forEach(items,function(item){
					if(item.auth){
						if(MdiApp[item.auth]){
							item.show()
						}else{
							item.hide()
						}
					}
				})
		})
        
    }
});