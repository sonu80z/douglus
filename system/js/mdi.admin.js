Ext.applyIf(mdi, {
admin: {
        user:null,
        dispatch:"system/dispatch.php",
        request: function(options){
                Ext.Ajax.request({
                        url: options.url || mdi.admin.dispatch,
                        method: options.urlMethodType || "POST",
                        disableCaching: true,
                        params: options.params,
                        success: function(response){
                                if (typeof options.success == "function") {
                                options.success(Ext.decode(response.responseText));
                          }
                        }
                });
        },
        addRowsToLeftSide: function(leftGridID, rightGridID)
        {
            var lGridRows = mdi.getSelectedRows(leftGridID);
            var store = Ext.getCmp(rightGridID).getStore();
            var i = 0;
            Ext.each(lGridRows, function(r)
            {
                i = store.indexOf(r);
                if (i == -1)
                    store.add(r);
            });
        },
        removeRowsFromLeftSide: function(leftGridID, rightGridID)
        {
            var rGridRows = mdi.getSelectedRows(rightGridID);
            Ext.each(rGridRows, function(r) 
            {
                Ext.getCmp(rightGridID).getStore().remove(r);
            });
        },
        add: function(){
        //mdi.admin.request({params:{}})
                mdi.admin.user = null;
                mdi.admin.getWindow().show();
                mdi.admin.getWindow().setTitle("Add User");
                Ext.getCmp('button-'+ mdi.admin.getActiveTabName().toLowerCase() + '-edit').hide();
                Ext.getCmp('button-'+ mdi.admin.getActiveTabName().toLowerCase() + '-add').show();
                if (mdi.admin.getActiveTabName() == "Group") {
                        mdi.admin.getWindow().setTitle("Add Group");
                        mdi.admin.setGridBaseParam("groupUserSourceGrid", {groupid:null});
                        mdi.admin.setGridBaseParam("groupFilterSourceGrid", {groupid:null, grouptypeid:null});
                        mdi.admin.clearGridData("groupUserGrid");
                        mdi.admin.clearGridData("groupFilterGrid");
                }
                mdi.admin.getForm().getForm().reset();
        },
        addGroup:function(){
                var form = mdi.admin.getForm().getForm();
                var values = form.getValues();
                values["filterdata"] = mdi.admin.getGridStoreData("groupFilterGrid", "data").join("|");
                values["userid"] = mdi.admin.getGridStoreData("groupUserGrid", "id").join("|");
                mdi.admin.request({params:Ext.applyIf({control:mdi.admin.getControl(), method:'Add'}, values), success:function(response){
                                if (response.success) {
                                        mdi.admin.getStore().load();
                                        mdi.admin.closeWindow();
                                }else if(response.msg){
                                        Ext.Msg.error(response.msg);
                                }
                }});
        },
        addUser:function(){
                var form = mdi.admin.getForm().getForm();
                var values = form.getValues();
                values.admin = mdi.admin.convertToBoolean(values.admin);
                values.selfonly = mdi.admin.convertToBoolean(values.selfonly);
                values.passwordexpired = mdi.admin.convertToBoolean(values.passwordexpired);
                values.canmailpdf = mdi.admin.convertToBoolean(values.canmailpdf);
                values.canbatchprintpdfs = mdi.admin.convertToBoolean(values.canbatchprintpdfs);
                values.canmarkasreviewed = mdi.admin.convertToBoolean(values.canmarkasreviewed);
                values.canburncd = mdi.admin.convertToBoolean(values.canburncd);
                values.canmarkcritical = mdi.admin.convertToBoolean(values.canmarkcritical);
                values.canattachorder = mdi.admin.convertToBoolean(values.canattachorder);
                values.staffrole= mdi.admin.convertToBoolean(values.staffrole);
                values.canaddnote = mdi.admin.convertToBoolean(values.canaddnote);
                values.canviewhtml5viewer = mdi.admin.convertToBoolean(values.canviewhtml5viewer);
                mdi.admin.request({params:Ext.applyIf({control:mdi.admin.getControl(), method:'Add'}, values), success:function(response){
                                if (response.success) 
                                {
                                    mdi.setCanMailPDF(values.canmailpdf);
                                    mdi.setCanBatchPrintPDFs(values.canbatchprintpdfs);
                                    mdi.setCanMarkAsReviewed(values.canmarkasreviewed);
                                    mdi.setCanBurnCD(values.canburncd);
                                    mdi.setCanMarkCritical(values.canmarkcritical);
                                    mdi.setCanAttachOrder(values.canattachorder);
                                    mdi.setStaffRole(values.staffrole);
                                    mdi.setCanAddNote(values.canaddnote);
                                    //we no longer use this setting.
                                    //mdi.setCanViewHTML5Viewer(values.canviewhtml5viewer);
                                    mdi.setCanViewHTML5Viewer(mdi.getCanViewHTML5Viewer());
                                    
                                    mdi.admin.getStore().load();
                                    Ext.getCmp('groupUserSourceGrid').getStore().load();
                                    mdi.admin.closeWindow();
                                }
                                    else if(response.msg){
                                        Ext.Msg.error(response.msg);
                                }
                }});
        },
        edit: function()
        {
            if (mdi.admin.hasSelection()) 
            {
                var selected = mdi.admin.getSelected();
                mdi.admin.user = selected;
                mdi.admin.getWindow().show();
                
                mdi.admin.getWindow().setTitle("Edit User");
                Ext.getCmp('button-' + mdi.admin.getActiveTabName().toLowerCase() + '-add').hide();
                Ext.getCmp('button-' + mdi.admin.getActiveTabName().toLowerCase() + '-edit').show();
                mdi.admin.getForm().getForm().reset();
                if (mdi.admin.getActiveTabName() == "Group") 
                {
                    mdi.admin.getWindow().setTitle("Edit Group");
                    if (selected[0].data.filterdata != "") 
                    {
                        var filterdata = selected[0].data.filterdata.split("|");
                        var filterstoredata = [];
                        for(var i = 0; i < filterdata.length;i++)
                                filterstoredata.push([filterdata[i]]);
                        Ext.getCmp("groupFilterGrid").getStore().loadData(filterstoredata);
                    }
                    else
                    {
                            mdi.admin.clearGridData("groupFilterGrid");
                    }
                    mdi.admin.setGridBaseParam("groupUserGrid", {groupid:selected[0].data.id});
                    mdi.admin.setGridBaseParam("groupUserSourceGrid", {groupid:selected[0].data.id});
//                    debugger;
                    mdi.admin.setGridBaseParam("groupFilterSourceGrid", {groupid:selected[0].data.id, grouptypeid:selected[0].data.grouptypeid});
                }
                
                var sel = selected[0].data;
                var fm = mdi.admin.getForm().getForm();
                
//                debugger;
                var user = Ext.create('User', sel);
                fm.loadRecord(user);
            }
            else 
            {
                Ext.Msg.warn("Select the "+mdi.admin.getActiveTabName().toLowerCase()+" you would like to edit.");
            }
        },
        editGroup:function(){
                var form = mdi.admin.getForm().getForm();
                var values = form.getValues();
                values["filterdata"] = mdi.admin.getGridStoreData("groupFilterGrid", "data").join("|");
                values["userid"] = mdi.admin.getGridStoreData("groupUserGrid", "id").join("|");
                if (mdi.admin.hasSelection()) {
                        mdi.admin.request({params:Ext.applyIf({control:mdi.admin.getActiveTabName()+'Control', method:'Update'}, values), success:function(response){
                                        if (response.success) {
                                                mdi.admin.getStore().load();
                                                mdi.admin.closeWindow();
                                        }else if(response.msg){
                                                Ext.Msg.error(response.msg);
                                        }
                        }});
                        }
        },
        editUser:function(){
                var form = mdi.admin.getForm().getForm();
                var values = form.getValues();
                values.admin = mdi.admin.convertToBoolean(values.admin);
                values.selfonly = mdi.admin.convertToBoolean(values.selfonly);
                values.passwordexpired = mdi.admin.convertToBoolean(values.passwordexpired);
                values.canmailpdf = mdi.admin.convertToBoolean(values.canmailpdf);
                values.canbatchprintpdfs = mdi.admin.convertToBoolean(values.canbatchprintpdfs);
                values.canmarkasreviewed = mdi.admin.convertToBoolean(values.canmarkasreviewed);
                values.canburncd = mdi.admin.convertToBoolean(values.canburncd);
                values.canmarkcritical = mdi.admin.convertToBoolean(values.canmarkcritical);
                values.canattachorder = mdi.admin.convertToBoolean(values.canattachorder);
                values.staffrole = mdi.admin.convertToBoolean(values.staffrole);
                values.canaddnote = mdi.admin.convertToBoolean(values.canaddnote);
                values.canviewhtml5viewer = mdi.admin.convertToBoolean(values.canviewhtml5viewer);
                if (mdi.admin.hasSelection()) {
                        mdi.admin.request({params:Ext.applyIf({control:mdi.admin.getActiveTabName()+'Control', method:'Update'}, values), success:function(response){
                                        if (response.success) {
                                                mdi.admin.getStore().load();
                                                Ext.getCmp('groupUserSourceGrid').getStore().load();
                                                mdi.admin.closeWindow();
                                        }else if(response.msg){
                                                Ext.Msg.error(response.msg);
                                        }

                        }});
                        }
        },
        remove: function(){
                var selected = mdi.admin.getSelected();
                if (mdi.admin.hasSelection()) {
                        mdi.admin.request({params:Ext.applyIf({control:mdi.admin.getActiveTabName()+'Control', method:'Delete', 'username':selected[0].data.username, 'id':selected[0].data.id}), 
                            success:function(response)
                                    {
                                        if (response.success) 
                                        {
                                            mdi.admin.getStore().remove(mdi.admin.getSelected());
                                        }
                                        else if(response.msg)
                                        {
                                            Ext.Msg.error(response.msg);
                                        }
                                    }
                        });
                }else {
                        Ext.Msg.warn("Select the "+mdi.admin.getActiveTabName().toLowerCase()+" you would like to delete.");
                }
        },
        closeWindow:function(){
                mdi.admin.getWindow().hide();	
        },
        setGridBaseParam:function(grid, params){
                var store = Ext.getCmp(grid).getStore();
                for(param in params)
                        store.getProxy().extraParams[param] = params[param];
                store.load();
        },
        clearGridData:function(grid){
                var store = Ext.getCmp(grid).getStore();
                store.removeAll();
        },
        convertToBoolean:function(val){
                return (val == "on") ? 1 : 0;
        },
        hasSelection: function(){
                return this.getGrid().getSelectionModel().hasSelection();
        },
        getGridStoreData: function(grid, field){
                var store = Ext.getCmp(grid).getStore();
                var records = store.getRange(0, store.getCount());
                var data = [];
                for(var i = 0; i < records.length;i++){
                        data[i] = records[i].data[field];
                }
                return data;
        },
        getControl: function(){
                return mdi.admin.getActiveTabName()+'Control';
        },
        getSelected: function(){
                return this.getGrid().getSelectionModel().getSelection();
        },
        getForm:function(){
                return Ext.getCmp(this.getActiveTabName().toLowerCase()+"Form");
        },
        getWindow:function(){
                return Ext.getCmp(mdi.admin.getActiveTabName().toLowerCase()+'Window');
        },
        getStore:function (){
                return this.getGrid().getStore();
        },
        getTabPanel:function(){
                return Ext.getCmp('userTabPanel');
        },
        getActiveTabName:function(){
                 var a = this.getTabPanel();
                 a = a.getActiveTab();
                 a = a.title;
                 a = a.replace(/s$/, "");
                 return a;
        },
        getGrid: function(){
                return Ext.getCmp(this.getActiveTabName().toLowerCase()+"Grid");
        },

        show:function()
        {
            if(Ext.getCmp("userAdminWindow") == undefined)
            {
                Ext.tip.QuickTipManager.init();
                Ext.Button.override({
                    setTooltip: function(qtipText)
                    {
                        var btnEl = this.getEl(); 
                        Ext.create('Ext.container.Container', {
                            target: btnEl.id,
                            text: qtipText,
                            dismissDelay: 3000
                        });             
                    }
                });
                
                Ext.define('GroupTypes', 
                {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'name']
                });
                
                Ext.define('User', 
                {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'firstname', 'middlename', 'lastname', 'username',  'selfonly', 'admin', 'passwordexpired', 'canmailpdf', 'canbatchprintpdfs', 'canmarkasreviewed', 'canburncd', 'canmarkcritical', 'canattachorder', 'canaddnote', 'canviewhtml5viewer', 'staffrole']
                });
                
                Ext.define('Group', 
                {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'grouptypeid', 'name', 'type', 'description', 'filterdata']
                });
                
                Ext.define('UserList', 
                {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'firstname', 'middlename', 'lastname', 'username',  'selfonly', 'admin', 'staffrole']
                });
                
                Ext.define('UserGroup',
                {
                    extend: 'Ext.data.Model',
                    fields: ['id', 'username']
                });
                
                Ext.define('FilterSource',
                {
                    extend: 'Ext.data.Model',
                    fields: ['data']
                });
                
                Ext.define('Filter',
                {
                    extend: 'Ext.data.Model',
                    fields: ['data']
                });

                var groupTypeStore = Ext.create('Ext.data.Store', {
                    id:'groupTypeStore',
                    model: 'GroupTypes',
                    remoteSort:true,
                    totalProperty: 'recordcount',
                    root:'data',
                    autoLoad:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=GroupControl&method=ViewGroupTypes',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

                var userStore = Ext.create('Ext.data.Store', {
                    id: 'userStore',
                    model: 'User',
                    remoteSort:true,
                    totalProperty: 'recordcount',
                    root:'data',
                    autoLoad:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=UserControl&method=View',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data'
                        }
                    }
                });

                var groupStore = Ext.create('Ext.data.Store', {
                    id: 'groupStore',
                    model: 'Group',
                    remoteSort:true,
                    autoLoad:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=GroupControl&method=View',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data',
                            totalProperty: 'recordcount'
                        }
                    }
                });

                var groupUserSourceStore = Ext.create('Ext.data.Store', {
                    id: 'groupUserSourceStore',
                    model: 'UserList',
                    remoteSort:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=UserControl&method=View',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data',
                            totalProperty: 'recordcount'
                        }
                    }
                });
               
                var groupUserStore = Ext.create('Ext.data.Store', {
                    id:'groupUserStore',
                    model: 'UserGroup',
                    remoteSort:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=GroupControl&method=ViewGroupUsers',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data',
                            totalProperty: 'recordcount'
                        }
                    }
                });                
 
                var groupFilterSourceStore = Ext.create('Ext.data.Store', {
                    id: 'groupFilterSourceStore',
                    model: 'FilterSource',
                    remoteSort:true,
                    
                    autoLoad:true,
                    proxy: 
                    {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=GroupControl&method=ViewGroupTypeCriteria',
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: 
                        {
                            type: 'json',
                            root: 'data',
                            totalProperty: 'recordcount'
                        }
                    }
                });  
                
                var groupFilterStore = Ext.create('Ext.data.Store', {
                    model: 'Filter',
                    remoteSort:true,
                    data:[]
                });

                var userPagingBar = new Ext.PagingToolbar({
                    pageSize: 12,
                    store: userStore,
                    displayInfo: true,
                    displayMsg: 'Displaying Users {0} - {1} of {2}',
                    emptyMsg: "No Users to display"
                });
                var groupPagingBar = new Ext.PagingToolbar({
                    pageSize: 12,
                    store: groupStore,
                    displayInfo: true,
                    displayMsg: 'Displaying Groups {0} - {1} of {2}',
                    emptyMsg: "No Groups to display"
                });
                var userGrid = new Ext.grid.Panel({
                        id:'userGrid',
                                store: userStore,
                                columns: [
                                {id:'id', header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'User Name',  width: 100, sortable: true, dataIndex: 'username'},
                                {header: 'First Name', width: 100, sortable: true, dataIndex: 'firstname'},
                                {header: 'Middle Name',  width: 75, sortable: true, dataIndex: 'middlename'},
                                {header: 'Last Name', width: 100, sortable: true, dataIndex: 'lastname'},
                                {header: 'Self Only', width: 75, sortable: false, dataIndex: 'selfonly'},
                                {header: 'Admin', width: 75, sortable: false, dataIndex: 'admin'},
                                {header: 'Staff role', width: 75, sortable: false, dataIndex: 'staffrole'}
                        ],
                        frame:true,
                        bbar:userPagingBar,
                        stripeRows: true,
                        title:'Users',
                        selModel:{mode: 'SINGLE'},
                        multiselect:'false',
                        height:400
                });

                var groupGrid = new Ext.grid.GridPanel({
                        id:'groupGrid',
                        store: groupStore,
                        columns: [
                                {id:'id', header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'Name',  width: 175, sortable: true, dataIndex: 'name'},
                                {header: 'Type', width: 75, sortable: true, dataIndex: 'type'},
                                {header: 'Description', width: 75, sortable: false, dataIndex: 'description'},
                                {header: 'Criteria',  width: 325, sortable: false, dataIndex: 'filterdata'}
                        ],	
                        frame:true,
                        bbar:groupPagingBar,
                        stripeRows: true,
                        title:'Groups',
                        selModel:{mode: 'SINGLE'},
                        multiselect:'false',
                        width:550,
                        height:400
                });

                var groupFilterSourceGrid = new Ext.grid.GridPanel({
                        id:'groupFilterSourceGrid',
                        store: groupFilterSourceStore,
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
                        },
                        width:360,
                        height:170
                });

                var groupFilterGrid = new Ext.grid.GridPanel({
                        id:'groupFilterGrid',
                        store: groupFilterStore,
                        title:'Group Criteria',
                        multiselect:'true',
                        selModel:{mode: 'MULTI'},
                        region:"east",
                        columns: [
                                {header: 'Data', id:'data', width: 320, sortable: false, dataIndex: 'data'}
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
//                        listeners:{
//                                'render':function(form){
//                                    this.dragzone = new Ext.ux.GridDropZone(this, {ddGroup:this.ddGroup || 'GridDD'});
//                                }
//                        }
                });
                var groupUserSourceGrid = new Ext.grid.GridPanel({
                        id:'groupUserSourceGrid',
                        store: groupUserSourceStore,
                        title:"User List",
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
                var groupUserGrid = new Ext.grid.GridPanel({
                        id:'groupUserGrid',
                        store: groupUserStore,
                        title:"Group Users",
                        multiselect:'true',
                        selModel:{mode: 'MULTI'},
                        region:"east",
                        columns: [
                                {header: 'ID',  width: 100, sortable: false, dataIndex: 'id', hidden:true},
                                {header: 'User Name', id:'username',  width: 320, sortable: true, dataIndex: 'username'}
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

                var userTabPanel = new Ext.tab.Panel({
                        id:'userTabPanel',
                        activeTab: 0,
                        width:650,
                        height:400,
                        items:[userGrid, groupGrid],
                        tbar:[
                                {text:'Add', id:"toolbar-button-user-add", tooltip:'Add User', iconCls:'ico-user-add', handler:mdi.admin.add},
                                {text:'Edit', id:"toolbar-button-user-edit", tooltip:'Edit User', iconCls:'ico-user-edit', handler:mdi.admin.edit},
                                {text:'Delete', id:"toolbar-button-user-delete", tooltip:'Remove User', iconCls:'ico-user-delete', handler:mdi.admin.remove},
                                '->',
                                new Ext.ux.SearchField({
                                        width:210,
                                        store: userStore,
                                        id:'admin-users-search'
                                })                                
                        ],
                        listeners: {
                            'tabchange': function(tabPanel, toTab, fromTab, option)
                            {
                                var buttons = ['Add', 'Edit', 'Delete'];
                                for (var i = 0; i < buttons.length; i++){
                                    var toolbarButton = Ext.getCmp('toolbar-button-user-' + buttons[i].toLowerCase());
                                    toolbarButton.setTooltip(buttons[i] + " " + mdi.admin.getActiveTabName());
                                    var ico = "ico-" + mdi.admin.getActiveTabName().toLowerCase() + "-" + buttons[i].toLowerCase();
                                    toolbarButton.setIconCls(ico);
                                } 
                                if (mdi.admin.getActiveTabName() == 'User')
                                    Ext.getCmp('admin-users-search').store = userStore;
                                else
                                    Ext.getCmp('admin-users-search').store = groupStore;
                            }
                        }
                });
                var groupComboBox = new Ext.form.ComboBox({
                        store: groupStore,
                        editable:false,
                        forceSelection:true,
                        valueField:'id', 
                        displayField:'name',
                        fieldLabel:"User Group",
                        emptyText:"Select a group...",
                        triggerAction:'all',
                        selectOnFocus:true,
                        allowBlank:false,
                        id:"id",
                        mode: 'remote'
                });
                var groupTypeComboBox = new Ext.form.ComboBox({
                        store: groupTypeStore,
                        editable:false,
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
                                        groupFilterSourceStore.getProxy().extraParams.grouptypeid = record[0].data.id;
                                        groupFilterSourceStore.load();
                                        groupFilterStore.removeAll();
                                        if(mdi.admin.user != null){
                                                if(record[0].data.id == mdi.admin.user[0].data.grouptypeid){
                                                        var filterdata = [mdi.admin.user[0].data.filterdata.split("|")];
                                                        groupFilterStore.loadData(filterdata);
                                                }
                                        }
                                }
                        }

                });
                var userForm = new Ext.form.FormPanel({
                        id:'userForm',
                        labelWidth: 175, // label settings here cascade unless overridden
                        bodyStyle:'padding:5px 5px 0',
                        autoWidth:true,
                        autoHeight:true,
                        defaultType: 'textfield',
                        items: [{fieldLabel: 'ID', name: 'id', allowBlank:false, xtype:'hidden' },
                                { xtype:'fieldset', title: 'User Information', autoHeight:true, autoWidth:true, defaults: {width: 180}, labelWidth:105, defaultType: 'textfield',
                                items :[
                                        {fieldLabel: 'First Name', name: 'firstname', allowBlank:false, width:280},
                                        {fieldLabel: 'Middle Name', name: 'middlename', width:280},
                                        {fieldLabel: 'Last Name', name: 'lastname', allowBlank:false, width:280}
                                ]
                        },{xtype:'fieldset',  title: 'User Login', autoHeight:true, autoWidth:true, defaultType: 'textfield',
                                items :[
                                        {fieldLabel: 'User Name', name: 'username', width:280, allowBlank:false },
                                        {fieldLabel: 'Password', name: 'password', width:280, allowBlank:false, inputType:'password' }
                                //	{fieldLabel: 'Confirm Password', name: 'confirmpassword', width:160, allowBlank:false, inputType:'password' },
                                ]
                        },{xtype:'fieldset',  title: 'User Options', autoHeight:true,  autoWidth:true, defaults: {width: 190}, defaultType: 'textfield', layout:'hbox',
                                items :[
                                        {id:'lp12', xtype: 'fieldset', width: 178, itemCls:'usereditcont', items:
                                            [
                                                {fieldLabel: 'Self Studies Only', id:'checkbox-self-only', name: 'selfonly',  allowBlank:false, xtype:"checkbox", 
                                                        listeners:{'check':function(self, checked){
                                                                if (checked) {
                                                                    Ext.getCmp('checkbox-admin').setValue(false);
                                                                }
                                                        }}
                                                },
                                                {fieldLabel: 'Admin', name: 'admin', id:'checkbox-admin', allowBlank:false, xtype:"checkbox", 
                                                        listeners:{'check':function(self, checked){
                                                                if (checked) {
                                                                    Ext.getCmp('checkbox-self-only').setValue(false);
                                                                }
                                                        }}
                                                },
                                                {fieldLabel: 'Staff role', name: 'staffrole', checked:true, id:'checkbox-staff-role', allowBlank:false, xtype:"checkbox", "handler":function()
                                                    {
                                                        if (this.checked)
                                                        {
                                                            try
                                                            {
                                                                Ext.getCmp('checkbox-can-mail-pdf').setValue(true);
                                                                Ext.getCmp('checkbox-can-print-pdf').setValue(true);
                                                                Ext.getCmp('checkbox-can-review-pdf').setValue(true);
                                                                Ext.getCmp('checkbox-can-burn-cd').setValue(true);
                                                                Ext.getCmp('checkbox-can-mark-critical').setValue(true);
                                                                Ext.getCmp('checkbox-can-add-note').setValue(true);
                                                                Ext.getCmp('checkbox-can-attach-order-').setValue(true);
                                                                Ext.getCmp('checkbox-staff-role').setValue(true);
                                                            }
                                                            catch(e){}
                                                        }
                                                    }
                                                },
                                                {fieldLabel: 'Password Expired', name: 'passwordexpired', checked:true, id:'checkbox-password-expired', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can email a file', name: 'canmailpdf', checked:true, id:'checkbox-can-mail-pdf', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can batch print', name: 'canbatchprintpdfs', checked:true, id:'checkbox-can-print-pdf', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can burn CD', name: 'canburncd', checked:true, id:'checkbox-can-burn-cd', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can mark as reviewed', name: 'canmarkasreviewed', checked:true, id:'checkbox-can-review-pdf', allowBlank:false, xtype:"checkbox"}
                                            ]
                                        },
                                        {id:'lpr2', xtype: 'fieldset', width: 170, border:false, itemCls:'usereditcont', items:
                                            [
                                                {fieldLabel: 'Can mark study as critical', name: 'canmarkcritical', checked:true, id:'checkbox-can-mark-critical', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can add note', name: 'canaddnote', checked:true, id:'checkbox-can-add-note', allowBlank:false, xtype:"checkbox"},
                                                {fieldLabel: 'Can attach order', name: 'canattachorder', checked:true, id:'checkbox-can-attach-order-', allowBlank:false, xtype:"checkbox"}//,
                                                //this is no longer used as we will auto detect.
                                                //{fieldLabel: 'Can view html5 viewer', name: 'canviewhtml5viewer', checked:false, id:'checkbox-can-view-html5-viewer', allowBlank:false, xtype:"checkbox"},
                                            ]
                                        }
                                ]}
                        ]
                });
                var groupForm = new Ext.form.FormPanel({
                        id:'groupForm',
                        labelWidth: 105, // label settings here cascade unless overridden
                        bodyStyle:'padding:5px 5px 0',
                        autoWidth:true,
                        autoHeight:true,
                        defaultType: 'textfield',
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
                                        new Ext.ux.SearchField({ store: groupFilterSourceStore, fieldLabel:'Search Criteria', width: 350, labelWidth: 90}),
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
                                                                                mdi.admin.addRowsToLeftSide('groupFilterSourceGrid', 'groupFilterGrid');
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
                                                                                mdi.admin.addRowsToLeftSide('groupUserSourceGrid', 'groupUserGrid');
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
                var userAdminWindow = new Ext.Window({
                        id:'userAdminWindow',
                        iconCls:'ico-user-admin',
                        closeAction:'hide', 
                        autoScroll:true,
                        modal:true,
                        border:false,
                        width:660,
                        height:435,
                        resizable:false,
                        title:'User Administration',
                        items:[userTabPanel]
                });
                var userWindow = new Ext.Window({
                        id:'userWindow',
                        closeAction:'hide', 
                        modal:true,
                        width:400,
                        height:592,
                        resizable:false,
                        title:'Add User',
                        items:[userForm],
                        buttons:[{text:"Cancel", handler:mdi.admin.closeWindow}, {text:"Save", id:'button-user-edit', handler:mdi.admin.editUser}, {text:"Add", id:'button-user-add', handler:mdi.admin.addUser}]
                });
                var groupWindow = new Ext.Window({
                        id:'groupWindow',
                        closeAction:'hide', 
                        modal:true,
                        width:790,
                        height:605,
                        resizable:false,
                        title:'Add Group',
                        items:[groupForm],
                        buttons:[{text:"Cancel", handler:mdi.admin.closeWindow}, {text:"Save", id:'button-group-edit', handler:mdi.admin.editGroup}, {text:"Add", id:'button-group-add', handler:mdi.admin.addGroup}]
                });
            }
            
            var w = Ext.getCmp("userAdminWindow");
            w.show();
        }
    }
});


Ext.applyIf(mdi, {
	preferences: 
	{
		show: function()
		{
                    var a = Ext.getCmp("prefWindow");
                    if (!a)
                        mdi.preferences.show1();
                    Ext.getCmp("prefWindow").show();
		},
		show1: function()
		{
                    Ext.define('instifutionMails',
                    {
                        extend: 'Ext.data.Model',
                        fields: ['id', 'mail', 'institution','autofax']
                    });

                    var mailStore = Ext.create('Ext.data.Store', {
                        id:'mailStore',
                        model: 'instifutionMails',
                        remoteSort:true,
                        totalProperty: 'recordcount',
                        root:'data',
                        autoLoad:true,
                        proxy: 
                        {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=MailControl&method=ViewInstifutionMails',
                        reader: 
                            {
                                type: 'json',
                                root: 'data'
                            }
                        }
                    });                    

                    Ext.define('Physicians',
                    {
                        extend: 'Ext.data.Model',
                        fields: ['id', 'mail', 'username']
                    });

                    var physicanMailStore = Ext.create('Ext.data.Store', 
                    {
                        model: 'Physicians',
                        id:'physicanMailStore',
                        remoteSort:true,
                        totalProperty: 'recordcount',
                        root:'data',
                        autoLoad:true,
                        proxy: 
                        {
                        type: 'ajax',
                        url: mdi.admin.dispatch+'?control=UserControl&method=ViewPhysicansGrid',
                        reader: 
                            {
                                type: 'json',
                                root: 'data'
                            }
                        }
                    });

                    var mailPagingBar = new Ext.PagingToolbar({
                        pageSize: 12,
                        store: mailStore,
                        displayInfo: true,
                        displayMsg: 'Displaying rows {0} - {1} of {2}',
                        emptyMsg: "No rows to display"
                    });

                    var mailGrid = new Ext.grid.GridPanel({
                        id:'mailGrid',
                        store: mailStore,
                        columns: [
                                {id:'id', header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'Mail address',  width: 200, sortable: true, dataIndex: 'mail'},
                                {header: 'Institution', width: 200, sortable: true, dataIndex: 'institution'},
								{header: 'Autofax', width: 250, dataIndex: 'autofax'}
                        ],
                        //frame:true,
                        //bbar:mailPagingBar,
                        //stripeRows: true,
                        title:'Institution emails',
                        selModel:{mode: 'SINGLE'},
                        width:550,
                        height:400
                    });

                    var physicanMailPagingBar = new Ext.PagingToolbar({
                        pageSize: 12,
                        store: mailStore,
                        displayInfo: true,
                        displayMsg: 'Displaying rows {0} - {1} of {2}',
                        emptyMsg: "No rows to display"
                    });

                    var physicanMailGrid = new Ext.grid.GridPanel({
                        id:'physicanMailGrid',
                        store: physicanMailStore,
                        columns: [
                                {id:'id', header:'ID', dataIndex:'id',  hidden:true},
                                {header: 'Mail address',  width: 200, sortable: true, dataIndex: 'mail'},
                                {header: 'Physician\'s name', width: 200, sortable: true, dataIndex: 'username'}
                        ],
                        //frame:true,
                        //bbar:physicanMailPagingBar,
                        //stripeRows: true,
                        title:'Physician\'s emails',
                        selModel:{mode: 'SINGLE'},
                        width:550,
                        height:400
                    });

                    var prefTabPanel = new Ext.tab.Panel({
                        id:'prefTabPanel',
                        activeTab: 0,
                        width:650,
                        height:400,
                        //title:"Preferences",
                        id:"preferences-window-tab-control",
                        items:[mailGrid, physicanMailGrid],
                        tbar:[
                                {text:'Add', id:"toolbar-button-user-add", tooltip:'Add User', iconCls:'ico-user-add', handler:mdi.preferences.add},
                                {text:'Edit', id:"toolbar-button-user-edit", tooltip:'Edit User', iconCls:'ico-user-edit', handler:mdi.preferences.edit},
                                {text:'Delete', id:"toolbar-button-user-delete", tooltip:'Remove User', iconCls:'ico-user-delete', handler:mdi.preferences.remove},
                            '->',
                                new Ext.ux.SearchField({
                                        width:210,
                                        store: mailStore,
                                        id:'preferences-window-filter'
                                })
                             ],
                        listeners:
                        {
                            'tabchange': function(tabPanel, toTab, fromTab, option)
                            {
                                switch (toTab.title)
                                {
                                    case "Institution emails":
                                    {
                                        var s = Ext.getCmp('preferences-window-filter');
                                        s.store = mailStore;
                                        break;
                                    }
                                    case "Physician's emails":
                                    {
                                        var s = Ext.getCmp('preferences-window-filter');
                                        s.store = physicanMailStore;
                                        break;
                                    }
                                }
                            }
                        }
                    });

                    var preferencesWindow = new Ext.Window({
                        id:'prefWindow',
                        iconCls:'ico-preferences',
                        modal:true,
                        border:false,
                        width:662,
                        height:430,
                        title:'Settings',
                        closeAction:'hide',
                        items:[prefTabPanel]
                    });
		},
		edit:function()
		{
                    var tabTitle = Ext.getCmp('preferences-window-tab-control').activeTab.title;
                    switch (tabTitle)
                    {
                        case "Institution emails":
                        {
                            var s = Ext.getCmp('mailGrid');
                            break;
                        }
                        case "Physician's emails":
                        {
                            var s = Ext.getCmp('physicanMailGrid');
                            break;
                        }
                    }
                    if (s)
                    {
                        var rows = s.getSelectionModel().getSelection();
                        if (rows.length)
                        {
                            var row = rows[0].data;
                            mdi.preferences.modify(row);
                        }
                    }
		},
		add:function()
		{
                    mdi.preferences.modify(null);
		},
		remove:function()
		{
                    var tabTitle = Ext.getCmp('preferences-window-tab-control').activeTab.title;
                    switch (tabTitle)
                    {
                        case "Institution emails":
                        {				
                            var s = Ext.getCmp('mailGrid').getSelectionModel().getSelection();
                            var params = {
                                                            control:'MailControl',
                                                            method:'remove',
                                                            rid:s[0].data.id
                                                    };
                            break;
                        }
                        case "Physician's emails":
                        {
                            var s = Ext.getCmp('physicanMailGrid').getSelectionModel().getSelection();
                            var params = {
                                                            control:'UserControl',
                                                            method:'remove',
                                                            rid:s[0].data.id
                                                    };
                            break;
                        }
                    }
                    if (s)
                    {
                        Ext.Ajax.request({
                                url: 'system/dispatch.php',
                                disableCaching: true,
                                params: params,
                                success: function(response)
                                {
                                    var res = Ext.decode(response.responseText);
                                    Ext.getCmp('mailGrid').getStore().load();
                                    Ext.getCmp('physicanMailGrid').getStore().load();
                                    if (!res.success)
                                            Ext.Msg.error(res.error_msg);
                                },
                                failure: function()
                                {
                                    Ext.Msg.error("Failed to request");
                                }
                        });
                    }
		},
		modify:function(currentRow)
                {
                    var win = Ext.getCmp("window-preferences");
                    if (win)
                    {
                        win.title = 'Add new email';
                    }
                    else
                    {
                        function saveEditor()
                        {
                            var el =  Ext.getCmp('itemvalue_combo');
                            var tabTitle = Ext.getCmp('preferences-window-tab-control').activeTab.title;
                            switch (tabTitle)
                            {
                                case "Institution emails":
                                {				
                                    var params = {
                                                    control:'MailControl',
                                                    method:'save',
                                                    institution:Ext.getCmp('itemvalue_combo').getValue(),
                                                    mail:Ext.getCmp('mail_inp').getValue(),
													autofax:Ext.getCmp('auto_fax').getValue()
                                                    };
                                    break;
                                }
                                case "Physician's emails":
                                {
                                    var params = {
                                                    control:'UserControl',
                                                    method:'save',
                                                    user:Ext.getCmp('itemvalue_combo').getValue(),
                                                    mail:Ext.getCmp('mail_inp').getValue()
                                                    };
                                    break;
                                }
                            }

                            if (currentRow)
                                    params.rid = currentRow.id; 
                            Ext.Ajax.request({
                                    url: 'system/dispatch.php',
                                    disableCaching: true,
                                    params: params,
                                    success: function(response)
                                    {
                                        var res = Ext.decode(response.responseText);
                                        Ext.getCmp('mailGrid').getStore().load();
                                        Ext.getCmp('physicanMailGrid').getStore().load();
                                        if (res.success)
                                        {
                                            win.close();
                                        }
                                        else
                                            Ext.Msg.error(res.error_msg);
                                    },
                                    failure: function()
                                    {
                                        Ext.Msg.error("Failed to request");
                                    }
                            });
                        }
                        
                        var tabTitle = Ext.getCmp('preferences-window-tab-control').activeTab.title;
                        switch (tabTitle)
                        {
                            case "Institution emails":
                            {
                                Ext.define('institutionDescrStore',
                                {
                                    extend: 'Ext.data.Model',
                                    fields: ['institution']
                                });

                                var institutionStore = Ext.create('Ext.data.Store', {
                                    id:'institutionStore',
                                    model: 'institutionDescrStore',
                                    remoteSort:true,
                                    autoLoad:true,
                                    totalProperty: 'recordcount',
                                    root:'data',
                                    proxy: 
                                    {
                                    type: 'ajax',
                                    url: 'system/dispatch.php?control=MailControl&method=ViewInstitution',
                                    reader: 
                                        {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    },
                                    listeners: {
                                            'load': function(store)
                                            {
                                                var el = Ext.getCmp('itemvalue_combo');
                                                var a = el.getValue();
                                                el.setValue(a);
                                            }
                                    }
                                });

                                targetPanel = new Ext.form.FormPanel({
                                id:'nstitution infution-mail-editor-form',
                                bodyStyle:'padding:5px 5px 0',
                                autoWidth:true,
                                autoHeight:true,
                                items:[
                                           {xtype:'fieldset',  title: 'Institution info', autoHeight:true, autoWidth:true, defaultType: 'textfield',
                                                items :[
                                                        {store: institutionStore,
                                                                fieldLabel: 'Institution',
                                                                xtype: 'combo',
                                                                standardSubmit:true,
                                                                valueField: 'institution',
                                                                displayField: 'institution',
                                                                triggerAction: 'all',
                                                                id:'itemvalue_combo',
                                                                width: 230},
                                                        {fieldLabel: 'Email', width:230, allowBlank:false, id:'mail_inp'},
														{xtype:'checkbox', fieldLabel: 'Autofax', id:'auto_fax'}
                                                ]}
                                           ]});
                               break;
                            }
                            case "Physician's emails":
                            {
                                Ext.define('PhysicianFullname',
                                {
                                    extend: 'Ext.data.Model',
                                    fields: ['fullname']
                                });

                                var physicianStore = Ext.create('Ext.data.Store', {
                                    id: 'physicianStore',
                                    model: 'PhysicianFullname',
                                    remoteSort:true,
                                    autoLoad:true,
                                    totalProperty: 'recordcount',
                                    root:'data',
                                    proxy: 
                                    {
                                        type: 'ajax',
                                        url: 'system/dispatch.php?control=UserControl&method=ViewPhysicans',
                                        reader:
                                        {
                                            type: 'json',
                                            root: 'data'
                                        }
                                    },
                                    listeners: 
                                    {
                                        'load': function(store)
                                        {
                                            var el = Ext.getCmp('itemvalue_combo');
                                            var a = el.getValue();
                                            el.setValue(a);
                                        }
                                    }
                                });

                                targetPanel = new Ext.form.FormPanel({
                                id:'institution-mail-editor-form',
                                bodyStyle:'padding:5px 5px 0',
                                autoWidth:true,
                                autoHeight:true,
                                items:[
                                           {xtype:'fieldset',  title: 'Physician info', autoHeight:true, autoWidth:true, defaultType: 'textfield',
                                                items :[
                                                        {store: physicianStore,
                                                                fieldLabel: 'Physician name',
                                                                xtype: 'combo',
                                                                standardSubmit:true,
                                                                valueField: 'fullname',
                                                                displayField: 'fullname',
                                                                triggerAction: 'all',
                                                                id:'itemvalue_combo',
                                                                width: 230},
                                                        {fieldLabel: 'Email', width:230, allowBlank:false, id:'mail_inp'}
                                                ]}
                                      ]});
                               break;
                            }
                        }
                        var win = new Ext.Window({
                                title:'Add new email',
//					closeAction:'hide',
                                iconCls: 'ico-preferences',
                                bodyStyle:'padding:5px',
                                id:'window-preferences',
                                width:385,
                                modal:true,
                                resizable:false,
                                items:[
                                        targetPanel
                                      ],
                                buttons:[{text:"Save", id:'button-institution-mail-edit', handler:saveEditor}]
                        });
                    }
                    win.show();
                    if (currentRow)
                    {
                        win.title = 'Edit institution info';
                        if (currentRow.institution)
                        {
                            Ext.getCmp('itemvalue_combo').setValue(currentRow.institution);
                        }
                        if (currentRow.username)
                        {
                            Ext.getCmp('itemvalue_combo').setValue(currentRow.username);
                        }
                        Ext.getCmp('mail_inp').setValue(currentRow.mail);
						if(currentRow.autofax == 'true')
							Ext.getCmp('auto_fax').setValue('true');
						else
							Ext.getCmp('auto_fax').setValue('false');
                    }
                    else
                    {
                        Ext.getCmp('itemvalue_combo').setValue('');
                        Ext.getCmp('mail_inp').setValue('');
						Ext.getCmp('auto_fax').setValue('false');
						
                    }
                }
	}
});