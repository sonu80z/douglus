Ext.define('MdiApp.view.admin.GroupUserGrid',{
	extend : 'Ext.grid.Panel',
	title:"Group Users",
	multiselect:'true',
	selModel:{mode: 'MULTI'},
	region:"east",
	columns: [
			{header: 'ID',  width: 100, sortable: false, dataIndex: 'id', hidden:true},
			{header: 'User Name', /*id:'username',*/  width: 320, sortable: true, dataIndex: 'username'}
	],	
	autoScroll:true,
	stripeRows: true,
	frame:true,
	ddGroup:"ddUserGroup",
	autoExpandColumn:'username',
	width:350,
//                        enableDragDrop:true,
	height:170
//                        listeners:{
//                                    'render':function(form){
//                                        this.dragzone = new Ext.ux.GridDropZone(this, {ddGroup:this.ddGroup || 'GridDD'});
//                                }
//                        }
});