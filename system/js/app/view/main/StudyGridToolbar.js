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
			var searchCombo=Ext.create('Ext.form.field.Text',{
                                        store: config.mainGridStore,
                                        width:210,
										//id : 'searchCombo',
										displayField : 'patientname',
										listeners:{'specialkey': function(field,e){
											if (e.getKey() == e.ENTER){
												var cmbSearchType=Ext.getCmp('cmbSearchType').getValue();
											 mainGridStore.getProxy().extraParams['search'] = field.getValue();
											 mainGridStore.getProxy().extraParams['showAll'] = '';
											 mainGridStore.getProxy().extraParams['searchColumn']=cmbSearchType;
											 mainGridStore.loadPage(1);
											}
										}}
										
                                    })		
			var items=[
			
//                                    {text:'Notes/Attachments', iconCls:'ico-study-note-attachment', handler:function(){mdi.study.viewNotesAndAttachments(Ext.getCmp('study-grid'));}},
                                    {text:'View', iconCls:'ico-study-viewer', handler: function(){MdiApp.study.viewDicom(grid)}},
                                    {text:'Report', iconCls:'ico-study-view-report', handler:function(){MdiApp.study.viewReport(grid)}},
									{text:'Email', iconCls:'ico-mail', handler:function(){MdiApp.study.mailReport(grid);}},
                                    {text:'Priors', iconCls:'ico-history', handler:function(){MdiApp.study.viewPriors(grid)}},
                                    {text:'Batch print', iconCls:'ico-batch-print', id:"menu-batch-print_button", handler:function(){MdiApp.study.batchPrintDialogShow(grid);}},
                                    {text:'CD/ISO', iconCls:'ico-disk', id:'men-burn-cd-button', handler:function(){MdiApp.study.burnCDShow(grid)}},
									{text:'Zip', iconCls:'ico-study-download', handler:function(){MdiApp.study.download(grid)}},
									{text:'Jobs', iconCls:'ico-burncd', id:'menu-ready-cd-button', handler:function(){
										MdiApp.study.readyCDShow(grid);
									}},
                                    {text:'New Study', iconCls:'ico-newstudy', hidden:true, id:'menu-create-new-study', handler:function(){
										var parent=self.findParentByType('studymainview')
										parent.setActiveItem('create-study')
									}},
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
                                        width: 100,
										listeners : {
											select : function(combo,r){
												var displayField=null;
												//console.log(r.data)
												switch(r.data['search_column']){
													case "study.studydate":
														displayField = 'datetime'
														break;
													case "(select modality from series where series.studyuid = study.uuid limit 1)":
														displayField = 'modality'
														break;
													case "study.patientId":
														displayField = 'patientid'
														break;
													case "patient.patientname":
														displayField = 'patientname'
														break;
													case "study.referringphysician":
														displayField = 'referringphysician'
														break;
												}
												if(displayField){
													searchCombo.setDisplayField(displayField)
												}
											}
										}
                                    },
                                    searchCombo
                            ]
			this.items=items;
			this.callParent(arguments);
		}
});