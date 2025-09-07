Ext.define('MdiApp.view.main.StudyGridToolbar',{
	extend : 'Ext.toolbar.Toolbar',
	
		constructor : function(config){
			var self=this;
			config=config||{}
			var grid=config.grid;
			var mainGridStore=config.grid.getStore();
			 Ext.define('searchTypes',
                    {
                        extend: 'Ext.data.Model',
                        fields: ['type', 'search_column']
                    });
                    
                    var searchTypeStore = Ext.create('Ext.data.Store', 
                    {
                        model: 'searchTypes',
                        remoteSort:true,
                        totalProperty: 'recordcount',
                        rootProperty:'data',
                        autoLoad:false,
                        proxy: 
                        {
                        type: 'ajax',
                        url: 'system/dispatch.php?control=SearchControl&method=ViewSearchTypes',
                        reader: 
                            {
                                type: 'json',
                                rootProperty: 'data'
                            }
                        }
                    });
			var items=[
			{text:'Download', iconCls:'ico-study-download', handler:'mdi.study.download'},
//                                    {text:'Notes/Attachments', iconCls:'ico-study-note-attachment', handler:function(){mdi.study.viewNotesAndAttachments(Ext.getCmp('study-grid'));}},
                                    {text:'View', iconCls:'ico-study-viewer', handler: function(){MdiApp.study.viewDicom(grid)}},
                                    {text:'Report', iconCls:'ico-study-view-report', handler:function(){MdiApp.study.viewReport(grid)}},
									{text:'Email', iconCls:'ico-mail', handler:'mailReport'/*function(){MdiApp.study.mailReport(grid);}*/},
                                    {text:'Priors', iconCls:'ico-history', handler:'viewPriors'/*function(){MdiApp.study.viewPriors(grid)}*/},
                                    {text:'Batch print', iconCls:'ico-batch-print', id:"menu-batch-print_button", handler:'batchPrintDialogShow'/*function(){MdiApp.study.batchPrintDialogShow(grid);*/},
                                    {text:'Burn CD', iconCls:'ico-burncd', id:'menu-burn-cd-button', handler:'burnCDShow'},
                                    {text:'New Study', iconCls:'ico-newstudy', hidden:true, id:'menu-create-new-study', handler:'mdi.study.createStudy'},
                                    '->',
                                    {text:'Advanced Search', id:'filter-advanced-search', handler: function()
                                        {
											var grid=config.grid
											grid.getComponent('adv-search').setVisible(!grid.getComponent('adv-search').isVisible())
                                        }    
                                    },
                                    {text:'Show All', id:'filter-show-all', handler: function()
                                    { 
                                        mainGridStore.getProxy().extraParams.fromDate = null;
                                        mainGridStore.getProxy().extraParams.toDate = null;
                                        mainGridStore.getProxy().extraParams.search = null;
                                        for (var i = 1; i < 9; i++)
                                        {
                                            key = 'search' + i;
                                            mainGridStore.getProxy().extraParams[key] = null;
                                            key = 'searchColumn' + i;
                                            mainGridStore.getProxy().extraParams[key] = null;
                                        }
										var store = mainGridStore
										if(store.getProxy().extraParams.showAll == undefined || store.getProxy().extraParams.showAll == false)
											store.getProxy().extraParams.showAll = true;
										else 
											store.getProxy().extraParams.showAll = null;
										store.load();
                                        //mdi.study.toggleShowAll();
                                    }},
                                    //filterMenu,
                                    {
                                        xtype: 'combo',
                                        id: 'cmbSearchType',
                                        store: searchTypeStore,
                                        triggerAction: 'all',
                                        valueField: 'search_column',
                                        displayField: 'type',
                                        emptyText:'Patient Name',
										matchFieldWidth:false,
                                        width: 100
                                    },
                                    Ext.create('Ext.form.ComboBox',{
                                        store: config.mainGridStore,
                                        width:210
                                    })
                            ]
			this.items=items;
			this.callParent(arguments);
		}
});