Ext.define('MdiApp.view.admin.AddEditGroupWindow',{
	extend : 'Ext.Window',
	//closeAction:'hide', 
	modal:true,
	width:750,
	height:592,
	resizable:false,
	title:'Add Group',
	layout:'fit',
	//items:[userForm],
	//buttons:[{text:"Cancel",action:'close', handler:'mdi.admin.closeWindow'}, {text:"Save",action:'edit', id:'button-user-edit', handler:'mdi.admin.editUser'}, {text:"Add",action:'add', id:'button-user-add', handler:'mdi.admin.addUser'}],
	//controller : ,
	constructor : function(config){
		MdiApp.admin.user = null;
		var self=this;
		var grid=config.grid
		var buttons=[];
		var getGridStoreData= function(grid, field){
                var store = grid.getStore();
                var records = store.getRange(0, store.getCount());
                var data = [];
                for(var i = 0; i < records.length;i++){
                        data[i] = records[i].data[field];
                }
                return data;
        }
		addRowsToLeftSide= function(leftGridID, rightGridID)
        {
            var lGridRows = leftGridID.getSelectionModel().getSelection()
            var store = rightGridID.getStore();
            var i = 0;
            Ext.each(lGridRows, function(r)
            {
                i = store.indexOf(r);
                if (i == -1)
                    store.add(r);
            });
        }
		buttons.push({text:"Cancel",action:'close', handler:function(){
			self.close();
		}})
		var addGroup=function(){
			var form = groupForm.getForm();
                var values = form.getValues();
                values["filterdata"] = getGridStoreData(groupFilterGrid, "data").join("|");
                values["userid"] = getGridStoreData(groupUserGrid, "id").join("|");
                MdiApp.admin.request({params:Ext.applyIf({control:'GroupControl', method:'Add'}, values), success:function(response){
                                if (response.success) {
                                        grid.getStore().load();
                                        self.close();
                                }else if(response.msg){
                                        Ext.Msg.error(response.msg);
                                }
                }});
		}
		var editGroup=function(){
			var form = groupForm.getForm();
                var values = form.getValues();
                values["filterdata"] = getGridStoreData(groupFilterGrid, "data").join("|");
                values["userid"] = getGridStoreData(groupUserGrid, "id").join("|");
				MdiApp.admin.request({params:Ext.applyIf({control:'GroupControl', method:'Update'}, values), success:function(response){
								if (response.success) {
										grid.getStore().load();
										self.close();
								}else if(response.msg){
										Ext.Msg.error(response.msg);
								}
				}});
		}
		if(config.data){
			buttons.push({text:"Save",action:'edit', id:'button-user-edit', handler:editGroup})
		}else{
			buttons.push({text:"Add",action:'add', id:'button-user-add', handler:addGroup})
		}
		
		
		var groupTypeStore=Ext.create('MdiApp.store.GroupTypeStore',{
			id:'groupTypeStore'
		})
		var groupFilterSourceStore = Ext.create('MdiApp.store.GroupFilterSourceStore', {
              //      id: 'groupFilterSourceStore',
					autoLoad : true,
					
                    
                }); 
		var groupFilterSourceGrid=Ext.create('MdiApp.view.admin.GroupFilterSourceGrid',{
			id:'groupFilterSourceGrid',
			store: groupFilterSourceStore,
			width:360,
			height:170
		});
		var groupFilterStore = Ext.create('MdiApp.store.GroupFilterStore', {
                    //model: 'Filter',
                    remoteSort:true,
                    data:[]
                });
		var groupFilterGrid = Ext.create('MdiApp.view.admin.GroupFilterGrid',{
                        id:'groupFilterGrid',
                        store: groupFilterStore,
                        
                });
		var groupUserSourceStore = Ext.create('MdiApp.store.GroupUserSourceStore', {
                    id: 'groupUserSourceStore',
                    autoLoad : true
                    
                });
		var groupUserSourceGrid = Ext.create('MdiApp.view.admin.GroupUserSourceGrid',{
                        id:'groupUserSourceGrid',
						store: groupUserSourceStore,
						title:"User List",
	
                        
                });
		var groupUserStore = Ext.create('MdiApp.store.GroupUserStore', {
                  //  id:'groupUserStore',
                    
                    
                });
		var groupUserGrid = Ext.create('MdiApp.view.admin.GroupUserGrid',{
                        id:'groupUserGrid',
                        store: groupUserStore,
                        title:"Group Users",
                        
                });
		var groupTypeComboBox = new Ext.form.ComboBox({
                        store: groupTypeStore,
                        //editable:false,
                        forceSelection:true,
                        valueField:'id',
                        hiddenName:'grouptypeid', 
                        displayField:'name',
                        fieldLabel:"Group Type",
                        emptyText:"Select a type of group...",
                        triggerAction:'all',
                        selectOnFocus:true,
                        allowBlank:false,
                        name:"grouptypeid",
                        width: 350,
                        labelWidth: 90,
                        mode: 'remote',
                        listeners:{
                                'select':function(self, record, index){
                                        groupFilterSourceStore.getProxy().extraParams.grouptypeid = record.data.id;
                                        groupFilterSourceStore.load();
                                        groupFilterStore.removeAll();
                                        if(MdiApp.admin.user != null){
                                                if(record.data.id == MdiApp.admin.user[0].data.grouptypeid){
                                                        var filterdata = [MdiApp.admin.user[0].data.filterdata.split("|")];
                                                        groupFilterStore.loadData(filterdata);
                                                }
                                        }
                                }
                        }

                });
		var groupForm = new Ext.form.FormPanel({
                        id:'groupForm',
                        labelWidth: 105, // label settings here cascade unless overridden
                        bodyStyle:'padding:5px 5px 0',
                        autoWidth:true,
                        autoHeight:true,
                        defaultType: 'textfield',
						scrollable : true,
                        items: [{fieldLabel: 'ID', name: 'id', allowBlank:false, xtype:'hidden' },
                                { xtype:'fieldset', title: 'Group Information', autoHeight:true, autoWidth:true, defaults: {width: 180}, labelWidth:105, defaultType: 'textfield',
                                items :[
                                        {fieldLabel: 'Name', name: 'name', allowBlank:false, width: 350, labelWidth: 90},
                                        {fieldLabel: 'Description', name: 'description', xtype:'textarea', height:40, width: 350, labelWidth: 90}
                                ]
                        },{
                                xtype:'fieldset',  title: 'Group Details', autoHeight:true, autoWidth:true, defaults: {width: 160}, defaultType: 'textfield',
                                    items :[
                                        groupTypeComboBox,
                                        Ext.create('Ext.form.field.ComboBox',{ 
											store: groupFilterSourceStore,
											displayField:'data',
											valueField:'data', 
											fieldLabel:'Search Criteria', 
											width: 350, 
											labelWidth: 90,
											typeAhead : true,
											queryParam : 'search',
											minChars : 2,
											queryCaching : false,
											queryMode : 'remote' //'local'
										}),
                                        new Ext.Panel({layout:"column",
                                                       height:175,
                                                       autoWidth:true, 
                                                       items:[
                                                                groupFilterSourceGrid,
                                                                {xtype:'panel',
                                                                region:"center",
                                                                height:170,
//                                                                bodyPadding: 2,
                                                                bodyStyle: 'padding:2px; padding-top: 70px',
                                                                width: 36,
                                                                autoWidth:true,
                                                                items: [
                                                                        {text:'>>', xtype:'button', id:"BtnItemAdd1", handler: function()
                                                                            {
                                                                                addRowsToLeftSide(groupFilterSourceGrid, groupFilterGrid);
                                                                            }},
                                                                        {text:'<<', xtype:'button', id:"BtnItemRemove1", handler: function()
                                                                            {
                                                                                mdi.admin.removeRowsFromLeftSide('groupFilterSourceGrid', 'groupFilterGrid');
                                                                            }
                                                                        }
                                                                        ]},
                                                                groupFilterGrid
                                                            ],
                                                       width:850}),
                                        new Ext.Panel({layout:"column", 
                                                        height:160, 
                                                        autoWidth:true, 
                                                        items:[
                                                                groupUserSourceGrid,
                                                                {xtype:'panel',
                                                                region:"center",
                                                                height:170,
//                                                                bodyPadding: 2,
                                                                bodyStyle: 'padding:2px; padding-top: 70px',
                                                                width: 36,
                                                                autoWidth:true,
                                                                items: [
                                                                        {text:'>>', xtype:'button', id:"BtnItemAdd2", handler: function()
                                                                            {
                                                                                addRowsToLeftSide(groupUserSourceGrid, groupUserGrid);
                                                                            }},
                                                                        {text:'<<', xtype:'button', id:"BtnItemRemove2", handler: function()
                                                                            {
                                                                                mdi.admin.removeRowsFromLeftSide('groupUserSourceGrid', 'groupUserGrid');
                                                                            }
                                                                        }
                                                                        ]},
                                                                groupUserGrid
                                                                ], 
                                                        width:850})
                                    ]
                        }]
                });
		if(config.data){
			console.log(config.data)
			if (config.data.data.filterdata != "") 
			{
				var filterdata = config.data.data.filterdata.split("|");
				var filterstoredata = [];
				for(var i = 0; i < filterdata.length;i++)
						filterstoredata.push([filterdata[i]]);
				groupFilterGrid.getStore().loadData(filterstoredata);
				groupForm.getForm().loadRecord(config.data)
			}
			groupFilterSourceStore.getProxy().extraParams.grouptypeid = config.data.data.grouptypeid;
			groupFilterSourceStore.load();
		}
		config.items=groupForm;
		config.buttons=buttons
		this.callParent(arguments);
	}
})