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
                                {id:'id', header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'Name',  width: 175, sortable: true, dataIndex: 'name'},
                                {header: 'Type', width: 75, sortable: true, dataIndex: 'type'},
                                {header: 'Description', width: 75, sortable: false, dataIndex: 'description'},
                                {header: 'Criteria',  width: 325, sortable: false, dataIndex: 'filterdata'}
                        ];	
//                        bbar:groupPagingBar,
                        
        self.columns=columns;
		self.callParent(arguments);
	}
});