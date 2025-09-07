Ext.define('MdiApp.store.GroupTypeStore',{
	extend : 'Ext.data.Store',
	//id:'groupTypeStore',
                    //model: 'GroupTypes',
					fields: ['id', 'name'],
                    remoteSort:true,
                    totalProperty: 'recordcount',
                    rootProperty:'data',
                    autoLoad:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: MdiApp.admin.dispatch+'?control=GroupControl&method=ViewGroupTypes',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            rootProperty: 'data'
                        }
                    }
})