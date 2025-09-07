Ext.define('MdiApp.view.admin.GroupGrid',{
	extend : 'Ext.grid.Panel',
	frame : true,
	stripeRows: true,
	title:'Groups',
	selModel:{mode: 'SINGLE'},
	multiselect:'false',
	constructor : function(config){
		var self=this;
		//id:'groupGrid',
		 
        self.store= Ext.create('MdiApp.store.GroupStore',{});
		var columns = [
                                {header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'Name',  width: 175, sortable: true, dataIndex: 'name'},
                                {header: 'Type', width: 75, sortable: true, dataIndex: 'type'},
                                {header: 'Description', width: 75, sortable: false, dataIndex: 'description'},
                                {header: 'Criteria',  width: 325, sortable: false, dataIndex: 'filterdata'}
                        ];	
//                        bbar:groupPagingBar,
		self.tbar=[
				{text:'Add', id:"toolbar-button-group-add", action:'addGroup', tooltip:'Add Group', iconCls:'ico-group-add'},
				{text:'Edit', id:"toolbar-button-group-edit", action:'editGroup',tooltip:'Edit Group', iconCls:'ico-group-edit'},
				{text:'Delete', id:"toolbar-button-group-delete",action:'removeGroup', tooltip:'Remove Group', iconCls:'ico-group-delete'},
				'->',
				Ext.create('Ext.form.field.Text',{
						width:210,
						store: self.store,
						//id:'admin-users-search'
						listeners : {
										specialkey : function(field,e){
											if (e.getKey() == e.ENTER) {
												//userStore
												self.store.reload({params:{search:field.getValue()}});
											}
										}
									}
				})                                
		]
        config.columns=columns;
		self.callParent(arguments);
	}
});