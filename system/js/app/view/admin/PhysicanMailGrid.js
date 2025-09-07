Ext.define('MdiApp.view.admin.PhysicanMailGrid',{
	extend : 'Ext.grid.Panel',
	title:'Physician\'s emails',
    selModel:{mode: 'SINGLE'},
	constructor : function(config){
		var self=this;
		var remove=function(){
			var rs=self.getSelectionModel().getSelection();
			if(!rs.length){
				Ext.Msg.info('No record selected')
				return;
			}
			var params =
				{
					control:'UserControl',
					method:'remove',
					rid:rs[0].data.id
				};
			Ext.Msg.warn({msg:'Sure to delete',buttons:Ext.MessageBox.OKCANCEL,fn:function(btn){
				if(btn=='ok'){
					Ext.Ajax.request({
										url: 'system/dispatch.php',
										disableCaching: true,
										params: params,
										success: function(response)
										{
											var res = Ext.decode(response.responseText);
											self.getStore().load();
											if (!res.success)
													Ext.Msg.error(res.error_msg);
										},
										failure: function()
										{
											Ext.Msg.error("Failed to request");
										}
								});
				}
			}})
		}
		var addEdit=function(btn){
			if(btn.getText().toLowerCase()=='edit'){
				var rs=self.getSelectionModel().getSelection();
				if(rs.length==0){
					Ext.Msg.warn('No record selected')
					return;
				}
			}
			var physicianStore = Ext.create('Ext.data.Store', {
                                    id: 'physicianStore',
                                    //model: 'PhysicianFullname',
									fields: ['fullname'],
                                    remoteSort:true,
                                    autoLoad:true,
                                    totalProperty: 'recordcount',
                                    rootProperty:'data',
                                    proxy: 
                                    {
                                        type: 'ajax',
                                        url: 'system/dispatch.php?control=UserControl&method=ViewPhysicans',
                                        reader:
                                        {
                                            type: 'json',
                                            rootProperty: 'data'
                                        }
                                    }
                                });
			var targetPanel = new Ext.form.FormPanel({
                             //   id:'institution-mail-editor-form',
                                bodyStyle:'padding:5px 5px 0',
                                autoWidth:true,
                                autoHeight:true,
								url: 'system/dispatch.php',
                                items:[
                                           {xtype:'fieldset',  title: 'Physician info', autoHeight:true, autoWidth:true, defaultType: 'textfield',
                                                items :[
                                                        {
															store: physicianStore,
															fieldLabel: 'Physician name',
															xtype: 'combo',
															standardSubmit:true,
															valueField: 'fullname',
															displayField: 'fullname',
															triggerAction: 'all',
															id:'itemvalue_combo',
															width: 230,
															name:'user'
														},
                                                        {
															fieldLabel: 'Email', 
															width:230, 
															allowBlank:false, 
															id:'mail_inp',
															name:'mail'
														},
														{
															xtype:'hidden',
															name : 'control',
															value : 'UserControl'
														},
														{
															xtype:'hidden',
															name:'method',
															value:'save'
														},
														{
															xtype:'hidden',
															name:'rid',
															value:(rs&&rs.length)?rs[0].data['id']:0
														}
                                                ]}
                                      ]});
			if(rs && rs.length){
				rs[0].data['user']=rs[0].data['username']
				targetPanel.loadRecord(rs[0])
			}
			var win = Ext.create('Ext.Window',{
                                title:'Add new email',
//								closeAction:'hide',
                                iconCls: 'ico-preferences',
                                bodyStyle:'padding:5px',
                              //  id:'window-preferences',
                                width:385,
                                modal:true,
                                resizable:false,
                                items:[
                                        targetPanel
                                      ],
                                buttons:[{text:"Save", id:'button-institution-mail-edit', handler:function(){
									targetPanel.getForm().submit({
										success : function(form, action){
											self.getStore().reload();
											win.close()
										},
										failure:function(form, action){
											console.log(arguments)
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
									})
								}/*'saveEditor'*/}]
                        });
                    
                    win.show();
		}
		var tbar=[
				{text:'Add', /*id:"toolbar-button-user-add", */tooltip:'Add User', iconCls:'ico-user-add', handler:addEdit},
				{text:'Edit', /*id:"toolbar-button-user-edit",*/ tooltip:'Edit User', iconCls:'ico-user-edit', handler:addEdit/*'MdiApp.preferences.edit'*/},
				{text:'Delete', /*id:"toolbar-button-user-delete",*/ tooltip:'Remove User', iconCls:'ico-user-delete', handler:remove/*'MdiApp.preferences.remove'*/},
				'->',
				Ext.create('Ext.form.field.ComboBox',{
					width:210,
					store: config.store,
					//id:'preferences-window-filter'
				})
			]
		this.tbar=tbar;
		var columns= [
                                {header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'Mail address',  width: 200, sortable: true, dataIndex: 'mail'},
                                {header: 'Physician\'s name', width: 200, sortable: true, dataIndex: 'username'}
                        ];
		this.columns=columns;
		this.callParent(arguments);
	}
});