Ext.define('MdiApp.view.admin.GroupFilterGrid',{
	extend : 'Ext.grid.Panel',
	title:'Group Criteria',
                        multiselect:'true',
                        selModel:{mode: 'MULTI'},
                        region:"east",
                        columns: [
                                {header: 'Data',  width: 320, sortable: false, dataIndex: 'data'}
                        ],	
                        autoScroll:true,
                        stripeRows: true,
                        autoExpandColumn:'data',
                        frame:true,
                        ddGroup:"ddFilterGroup",
                        width: 350,
                        autoWidth:true,
//                        enableDragDrop:true,
                        height:170
});