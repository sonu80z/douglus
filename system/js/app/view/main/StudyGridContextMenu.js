Ext.define('MdiApp.view.main.StudyGridContextMenu',{
	extend : 'Ext.menu.Menu',
	constructor : function(config){
		var self=this;
		config=config||{};
		var grid=config.grid;
		var record=config.record;
		var items=[];
		var priors={
					  text: 'Priors',
					  iconCls: 'ico-history',
					  studyAction : 'viewPriors',
					  handler: function(){
						  MdiApp.study.viewPriors(grid)
					//	mdi.study.viewPriors();
					  }
					}
		var	attachReport={
					//	id:'context-menu-attach-pdf',
						text: 'Attach report',
						hidden:(MdiApp.isAdmin == 0),
						iconCls: 'ico-file-pdf',
						handler: function(){
								var win = new Ext.Window({
										title:'Attach report',
										iconCls: 'ico-file-pdf',
										bodyStyle:'padding:5px',
										id:'window-attach-pdf',
										height:200,
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
												{text:"Attach", handler: function(){MdiApp.study.attachPDF('PDF',grid)}}
												]
								});
								win.show();
						}
				}
		var emailTheReport={
				  text: 'Email the report',
				  iconCls: 'ico-mail',
				  auth : 'isCanMailPDF',
				  handler: function(){
						MdiApp.study.mailReport(grid);
				  }
				}
		items.push(priors);
		items.push(attachReport)
		items.push(emailTheReport)
		
		menuItem = new Ext.menu.Item({
			text: 'Mark as reviewed',
			//id:'menu-item-mark-reviewd',
			iconCls: 'ico-review',
			auth : 'isCanMarkAsReviewed',
			handler: function(){
				MdiApp.study.markAsReviewd(grid);
			}
		});
		items.push(menuItem);
		
		menuItem = new Ext.menu.Item({
				text: 'Mark as critical',
			//	id:'menu-item-mark-critical',
				iconCls: 'ico-critical',
				auth : 'isCanMarkCritical',
				handler: function(){
					MdiApp.study.markAsCritical(grid);
				}
		});
		items.push(menuItem);

		menuItem = new Ext.menu.Item({
				text: 'Remove critical status',
			//	id:'menu-item-mark-uncritical',
				iconCls: 'ico-uncritical',
				auth : 'isCanMarkCritical',
				handler: function(){
					MdiApp.study.markAsUnCritical(grid);
				}
		});
		items.push(menuItem);
		
		menuItem = new Ext.menu.Item({
			text: 'Add Note',
	//		id:'menu-item-add-note',
			iconCls: 'ico-note',
			auth : 'isCanAddNote',
			handler: function(){
					MdiApp.study.showNoteWindow(grid);
			}

		});
		items.push(menuItem);
		

		menuItem = new Ext.menu.Item({
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
		items.push(menuItem);
		

		menuItem = new Ext.menu.Item({
			text: 'Attach tech notes',
	//		id:'menu-item-attach-tech_notes',
			iconCls: 'ico-tech_notes',
			auth : 'isCanAttachOrder',
			handler: function()
			{
				MdiApp.study.fileUpload('TECH_NOTES',grid);
			}
		});
		items.push(menuItem);
		
		items.push(
		{
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
		});
	  
		items.push(
		{
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
		});

		items.push(
		{
			text: 'Remove ...',
			iconCls: 'ico-admin_remove_report',
			auth : 'isAdmin',
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
		});
  
		config.items=items;
		this.callParent(arguments);
	},
	initComponent : function(){
		var self=this
		this.callParent(arguments);
		this.items.each(function(item,index,len){
			if(item.initialConfig.auth && MdiApp[item.initialConfig.auth]){
				if(MdiApp[item.initialConfig.auth])
					item.show()
				else
					item.hide()
			}
		})
	}
});