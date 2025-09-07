Ext.define('MdiApp.view.admin.GroupFilterSourceGrid',{
	extend : 'Ext.grid.Panel',
	title:'Criteria List',
                        multiselect:'true',
                        selModel:{mode: 'MULTI'},
                        region:"west",
                        columns: [
                                {header: 'Data', id:'data', width: 330, sortable: false, dataIndex: 'data'}
                        ],	
                        autoScroll:true,
                        stripeRows: true,
                        frame:true,
                        autoExpandColumn:'data',
                        ddGroup:"ddFilterGroup",
//                        enableDragDrop:true,
                        listeners:{
//                                'render':function(form){
//                                        this.dragzone = new Ext.ux.GridDropZone(this, {ddGroup:this.ddGroup || 'GridDD'});
//                                }
                        }
});