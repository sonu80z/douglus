Ext.define('MdiApp.view.admin.GroupUserSourceGrid',{
	extend : 'Ext.grid.Panel',
	multiselect:'true',
	selModel:{mode: 'MULTI'},
	region:"west",
	columns: [
			{header: 'ID',  width: 100, sortable: false, dataIndex: 'id', hidden:true},
			{header: 'User Name', id:'username',  width: 330, sortable: true, dataIndex: 'username'}
	],	
	autoScroll:true,
	stripeRows: true,
	frame:true,
	autoExpandColumn:'username',
	ddGroup:"ddUserGroup",
//                        enableDragDrop:true,
//                        listeners:{
//                                'render':function(form){
//                                    this.dragzone = new Ext.ux.GridDropZone(this, {ddGroup:this.ddGroup || 'GridDD'});
//                                }
//                        },
	width:360,
	height:170
});