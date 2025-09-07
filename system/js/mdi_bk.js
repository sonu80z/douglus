//set blank url image.
Ext.BLANK_IMAGE_URL = 'system/core/ext/resources/images/default/s.gif';

//This allows us to provide a much easier way to popup alerts with out so much configuration.
Ext.applyIf(Ext.Msg, {
	checkOptions:function(options)
        {
            if(typeof options == "undefined")
                options = {};
            if(typeof options == "string")
                options = {msg:options};
            return Ext.applyIf(options, {msg:'Message'});
	},
	error:function(options){
            this.show(Ext.applyIf(this.checkOptions(options), {title:'Error', buttons:Ext.Msg.OK, icon:Ext.Msg.ERROR}));
	},
	warn:function(options){
            this.show(Ext.applyIf(this.checkOptions(options), {title:'Warning', buttons:Ext.Msg.OK, icon:Ext.Msg.WARNING}));
	},
	info:function(options){
            this.show(Ext.applyIf(this.checkOptions(options), {title:'Message', buttons:Ext.Msg.OK, icon:Ext.Msg.INFO}));
	},
	invalid:function(options){
            this.error(Ext.applyIf(this.checkOptions(options), {title:'Validation Error'}));
	}
});
Ext.applyIf(Ext.Ajax, {
	//options
	post:function(o){
		this.request({
			url:o.url,
			disableCaching:true,
			method: "post",
			params:o.params||{},
			autoAbort:o.autoAbort||true,
			success:function(result, request)
                        {
                            var bb = o;
                            if(typeof o.success == "function")
                            {
                                var resp = result.responseText;
                                var respDec = Ext.decode(resp);
                                o.success(respDec);
                            }
			},
			failure:function(result, request)
                        {
                            var bb = o;
                            if(typeof o.failure == "function")
                                o.failure(Ext.decode(result.responseText));
			}
		});
	}
});
username = "";
Ext.namespace('mdi');
mdi = function(){
    var config = {
            db: "system/legacy/db/study.php",
            view:"index.php",
            action:"system/actionItem.php",
            logs:"system/dispatch.php?control=LogsControl&method=getLogs"
    };
    return {
            isAdmin:0,
            isCanMailPDF:0,
            isCanBatchPrintPDFs:0,
            isCanMarkAsReviewed:0,
            isCanBurnCD:0,
            isCanMarkCritical:0,
            isCanAttachOrder:0,
            isStaffRole:0,
            isCanAddNote:0,
            isCanViewHTML5Viewer:0,
            setUsername:function(user)
            {
                username = user;
                var el = document.getElementById('login-status');
                if (el)
                    el.innerHTML = username;
            },
            setAdmin:function(isAdmin){
                this.isAdmin = isAdmin;
                var el;
                if (!isAdmin) 
                {
                    el = Ext.getCmp('toolbar-button-user-admin');
                    if (el)
                        el.hide();
                    el = Ext.getCmp('toolbar-button-preferences');
                    if (el)
                        el.hide();
                    el = Ext.getCmp('toolbar-button-logs');
                    if (el)
                        el.hide();
                }
                else 
                {
                    el = Ext.getCmp('toolbar-button-user-admin');
                    if (el)
                        el.show();
                    el = Ext.getCmp('toolbar-button-preferences');
                    if (el)
                        el.show();
                    el = Ext.getCmp('toolbar-button-logs');
                    if (el)
                        el.show();
//                    Ext.getCmp('menu-create-new-study').show();

                }
            },

            setCanMailPDF:function(val){
                this.isCanMailPDF = val;
            },
            setCanBatchPrintPDFs:function(val){
                if (!val)
                    document.getElementById('menu-batch-print_button').style.display="none";

                this.isCanBatchPrintPDFs = val;
            },
            setCanBurnCD:function(val){
                if (!val)
                    document.getElementById('menu-burn-cd-button').style.display="none";
                this.CanBurnCD = val;
            },
            setCanMarkCritical:function(val){
                var r = false;
                if (val == '1')
                    r = true;
                this.isCanMarkCritical = r;
            },
            setCanAttachOrder:function(val){
                var r = false;
                if (val == '1')
                    r = true;
                this.isCanAttachOrder = r;
            },
            setStaffRole:function(val){
                var r = false;
                if (val == '1')
                        r = true;
                this.isStaffRole = r;
                if (this.isStaffRole) 
                {
                    Ext.getCmp('toolbar-button-preferences').show();
//                    Ext.getCmp('menu-create-new-study').show();
                }
            },
            getSelectedRows:function(grid)
            {
				if (!grid)
                    grid = Ext.getCmp('study-grid');
                if (typeof(grid) == 'string')
                {
                    grid = Ext.getCmp(grid);
                }
                var selection = [];
                var sel = grid.getSelectionModel();
                selection = sel.getSelection();
                return selection;
            },
            setCanAddNote:function(val){
                var r = false;
                if (val == '1')
                        r = true;
                this.isCanAddNote = r;
            },
            setCanViewHTML5Viewer:function(val){
                var r = false;
                if(val == '1')
                    r = true;
                this.isCanViewHTML5Viewer = r;
            },
            getCanViewHTML5Viewer:function(){
                var canvasEl = document.createElement('canvas'); //create the canvas object
                    if(!canvasEl.getContext) //if the method is not supported, i.e canvas is not supported
                        return false;
                return true;
            },
            setCanMarkAsReviewed:function(val){
                this.isCanMarkAsReviewed = val;
            },
            setLoadingText:function(txt){
                document.getElementById('loading-msg').innerHTML = txt;
            },
            textMask:function(on)
            {
                if(on)
                {
                    Ext.get('loading').show();
                }else
                {
                    Ext.get('loading').hide();
                }
            },
            mask:function(on, cb){
                this.textMask(on);
                if(on){
                        Ext.get('loading-mask').show();
                }else{
                        Ext.get('loading-mask').hide();
                }
            },
            study:{

                    data:[],
                    option:"Study",
                    autoRefreshInterval:240, //seconds
                    autoRefeshId:null,
                    attachPDF:function(type)
                    {
                            if(this.hasSelection()){
									var urlParams = {entry:this.getEntries(Ext.getCmp('study-grid')), option:'Study'};
                                    if (type == 'PDF')
                                            urlParams.actions='AttachPDF';
                                    if (type == 'ORDER')
                                            urlParams.actions='AttachORDER';
                                    if (type == 'TECH_NOTES')
                                            urlParams.actions='AttachTECH_NOTES';
                                    if (!urlParams.actions)
                                            Ext.Msg.warn('An error occured in function attachPDF. Parameter is "'+type+'"');

                                    Ext.getCmp('form-attach-pdf').getForm().submit({
                                            url:config.action, 
                                            params:urlParams,
                                            success:function(form, response){
                                                    Ext.Msg.info("File attached successfully");
                                                    var a = Ext.getCmp('window-attach-pdf');
                                                    if (a)
                                                            a.close();
                                                    a = Ext.getCmp('window-attach-pdf-order');
                                                    if (a)
                                                        a.close();
                                                    mdi.study.refresh();
                                            },
                                            failure:function(form, response){
													//console.log(response.result);
                                                    Ext.Msg.error("Failed to upload the file.");
                                            }

                                    });
                            }else{
                                    Ext.Msg.warn("Select the study to attach a pdf");
                            }
							if (type == 'PDF')
									mdi.study.mailReportAttach();
                    },
                    addNote:function(noteText)
                    {
                            if(this.hasSelection())
                            {
                                    var urlParams = {entry:this.getEntries(), option:'Study', text:noteText, actions:'addNOTE'};
                                    Ext.Ajax.post({
                                            url:config.action,
                                            params:urlParams,
                                            success:function(result)
                                            {
                                                mdi.study.refresh();
                                                if(!result.success) 
                                                {
                                                    Ext.Msg.warn(result.failure);
                                                }
                                            },
                                            failure:function(form, response)
                                            {
                                                Ext.Msg.error("Failed to request.");
                                            }
                                    });
                            }
                    },
                    loadData:function(studyData){
                        this.data = studyData;
                        //this.getGrid().getStore().loadData(this.data);
                        var a = this.getGrid();
                        a = a.getStore();
                        a = a.loadData(this.data);
                    },
                    refresh:function(grid)
                    {
                        var a = this.getGrid(grid);
                        a = a.getStore();
                        a = a.load();
                    },
                    getGrid: function(grid)
                    {
                        if(!grid) grid = 'study-grid';
                            return Ext.getCmp(grid);
                    },
                    getGridToolbar: function(gridtoolbar)
                    {
                        if(!gridtoolbar) gridtoolbar = 'grid-menu';
                            return Ext.getCmp(gridtoolbar);
                    },
                    clearSelections:function(grid)
                    {
                        this.getGrid(grid).getSelectionModel().clearSelections();
                    },
                    getSelections: function(grid)
                    {
                        return mdi.getSelectedRows(grid);
                    },
                    getEntries:function(grid)
                    {
                        var selection = this.getSelections(grid);
                        var entry = [];
                        for(var i = 0; i < selection.length;i++)entry.push(selection[i].data.uid);
                        return entry.join(",");
                    },
                    hasSelection:function(grid){
                        var selection = mdi.getSelectedRows(grid);
                        return selection.length > 0;
                    },
                    performStandardAction:function(action, url, grid)
                    {
                        if(typeof url == "undefined")
                                url = config.action;
                        var selectedItems = mdi.getSelectedRows(grid);
                        var l = selectedItems.length;
                        var item;
                        var itemStr = '';
                        for (i = 0; i < l; i++)
                        {
                            item = selectedItems[i];
                            if (itemStr.length)
                                itemStr += ',' + item.data.uid;
                            else
                                itemStr = item.data.uid;
                        }

                        url = url + '?entry=' + itemStr + '&actions=' + action;
                        window.location.href = url;
                    },
                    performAjaxAction:function(action, grid, params)
                    {
						//base options
                        var o = {url:config.action, params:Ext.applyIf({entry:this.getEntries(grid),actions:action,option:this.option}, params)};
                        //checking to see if a config object is passed instead of action
                        if(typeof action == "object"){
                                if(typeof action.params == "object")
                                        action.params = Ext.apply({}, action.params, o.params);	
                                o = Ext.applyIf(action, o);	
                        }
                        Ext.Ajax.post({
                                url:o.url, 
                                params:o.params,
                                success:function(result)
                                {
                                    if (result.success) 
                                    {
                                        if (typeof o.success == "function") 
                                            o.success(result);
                                    }
                                    else if(!result.success) 
                                    {
                                        if (typeof (o.failure) == "function")
                                            o.failure(result)
                                        else
                                        {
                                            Ext.Msg.warn(result.failure);
                                        }
                                    }
                                    mdi.study.refresh();
                                }
                        });
                    },
                    markAsReviewd:function(){
                            if(this.hasSelection()){
                                this.performAjaxAction("Mark Study Reviewed");
                            }else{
                                Ext.Msg.warn("Study  does not marked as reviewed");
                            }
                    },
                    markAsUnReviewd:function()
                    {
                            if(this.hasSelection()){
                                this.performAjaxAction("Mark Study UNReviewed");
                            }else{
                                Ext.Msg.warn("Study does not marked as unreviewed");
                            }
                    },
                    markAsRead:function(){
                            if(this.hasSelection()){
                                this.performAjaxAction("Mark Study Read");
                            }else{
                                Ext.Msg.warn("Select the studies you would like to mark as read");
                            }
                    },
                    markAsUnread:function(){
                            if(this.hasSelection()){
                                this.performAjaxAction("Mark Study Un-Read");
                            }else{
                                Ext.Msg.warn("Select the studies you would like to mark as un-read");
                            }
                    },
                    xport:function(){
                        if(this.hasSelection()){
                                this.performStandardAction("Export");
                        }else{
                                Ext.Msg.warn("Select the studies you would like to export");
                        }
                    },
                    forward:function()
                    {
                        if(this.hasSelection()){
                                this.performStandardAction("Forward");
                        }else{
                                Ext.Msg.warn("Select the studies you would like to forward");
                        }
                    },
                    remove:function()
                    {
                        if(this.hasSelection()){
                                this.performAjaxAction("Delete");
                        }else{
                                Ext.Msg.warn("Select the studies you would like to delete");
                        }
                    },
                    viewDicom:function(grid)
                    {
                        if(this.hasSelection(grid)){
                                if(mdi.isCanViewHTML5Viewer){
                                    this.performStandardAction("DicomViewer", 'system/html5viewer/index.php', grid);
                                }else{    
                                    this.performStandardAction("DicomViewer", 'system/viewer/index.php', grid);
                                }
                        }else{
                                Ext.Msg.warn("Select a study to view the DICOM image");
                        }
                    },
                    viewPriors:function()
                    {
                        if(this.hasSelection()){
                            var patientStore = Ext.getCmp('patient-grid').getStore();
                        var a = mdi.study.getSelections()[0].data.patientid;
                        patientStore.getProxy().extraParams.search = a;
                        patientStore.load();
                        var w = Ext.getCmp('patient-history-window');
                        w.show();
                    }else{
                      Ext.Msg.warn("Select a patient to view the priors");
                    }
                    },
                    burnCDShow:function()
                    {
                        var go2BurnCDWaitPage = function(selStudies)
                        {
                            if (!selStudies)
                                    return;
                            var params = {
                                            selected_studies:selStudies,
                                            control: 'BurnCDControl',
                                            method: 'getProcessID'
                                          }
                            Ext.Ajax.request({
                                url: 'system/dispatch.php',
                                disableCaching: true,
                                params: params,
                                success: function(response)
                                {
                                    try
                                    {
                                        var res = Ext.decode(response.responseText);
                                    }
                                    catch (e)
                                    {
                                        Ext.Msg.error('Server error: ' + response.responseText);
                                    }
                                    if (res.success && !res.error_msg)
                                    {
                                        var a234 = '/system/viewer/CDBurnerWaitPage.php?processID=' + res.process_id;
                                        window.open(a234);
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
                        var studiesID = '';
                        var i = 0;
						var entry = [];
                        
                        i = 0;
						// try to get studies fot burning CD from the history window
						var selection = Ext.getCmp('patient-grid');
						if (selection)
						{
							selection = mdi.getSelectedRows(selection);
							for(i = 0; i < selection.length;i++)
								entry.push(selection[i].data.uid);
							studiesID = entry.join(",");
						}
						
                        i = 0;
                        if (studiesID.length == 0)
                        {
							var selectedStudies = mdi.getSelectedRows(Ext.getCmp('study-grid'));
							if (selectedStudies)
							{
								for (i = 0; i < selectedStudies.length; i++)
								{
									entry.push(selectedStudies[i].data.uid);
								}
								studiesID = entry.join(","); 
							}
                        }
                        
                        if (studiesID == '')
                        {
                                Ext.Msg.warn("Select the studies you would like to export");
                                return;
                        }
                        go2BurnCDWaitPage(studiesID);
                    },
                    createStudy:function()
                    {
                        var patientSex = new Ext.data.Store({
                           data:[{'sex': 'F'}, {'sex': 'M'}],
                           fields: ['sex']
                        });
                        
                        var patientStore = new Ext.data.JsonStore({
                            storeId: 'groupTypeStore',
                            //autoLoad: true,
                            proxy: {
                                type: 'ajax',
                                url: mdi.admin.dispatch+'?control=StudyControl&method=PatientList',
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    idProperty: 'origid'
                                }
                            },
                            fields: ['origid', 'firstname', 'lastname', 'birthdate', 'patientname']
                        });
                        
                        var refPhysicianStore = new Ext.data.JsonStore({
                            storeId: 'groupTypeStore',
                            //autoLoad: true,
                            proxy: {
                                type: 'ajax',
                                url: mdi.admin.dispatch+'?control=StudyControl&method=referringPhysicianList',
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    idProperty: 'origid'
                                }
                            },
                            fields: ['referringphysician']
                        });
                        
                        var modalityStore = Ext.create('Ext.data.Store', {
                            fields: ['modality'],
                            model: 'modalityModel',
                            data : [
                                        {"modality": "CR"}, {"modality": "US"}, {"modality": "EKG"}, {"modality": "LAB"}, {"modality": "ECHO"}
                                    ]
                        });
                        
                        var win = new Ext.Window({
                        title:'Create new study',
                        iconCls: 'ico-batch-print',
                        bodyStyle:'padding:5px',
                        id:'window-create_study',
                        width:500,
                        modal:true,
                        buttons: [{ text:"Add this study", 
                                    id:'buttonAddStudy', 
                                    listeners : 
                                        {'click': function()
                                            {
                                                var form = Ext.getCmp('createStudyForm').getForm();
                                                var params = form.getValues();

                                                params.control = 'StudyControl';
                                                params.method = 'Add';
                                                params.studydate = Ext.Date.format(Ext.getCmp('studyDateControl').getValue(), 'Y-m-d');
                                                params.birthday = Ext.Date.format(Ext.getCmp('_field_birthday').getValue(), 'Y-m-d');
                                                
                                                Ext.Ajax.request({
                                                url: 'system/dispatch.php',
                                                disableCaching: true,
                                                params: params,
                                                success: function(response)
                                                    {
                                                        try
                                                        {
                                                            var res = Ext.decode(response.responseText);
                                                        }
                                                        catch (e)
                                                        {
                                                            Ext.Msg.error('Server error: ' + response.responseText);
                                                            return;
                                                        }
                                                        if (res.success && !res.error_msg)
                                                        {
                                                            mdi.study.refresh()
                                                            win.close();
                                                        }
                                                        else
                                                            Ext.Msg.error(res.error_msg);

                                                    },
                                                failure: function()
                                                    {
                                                        Ext.Msg.error("Failed to request");
                                                    }
                                                }) 
                                            }
                                        }
                                }],
                        items:[
                                  new Ext.form.FormPanel({
                                        id:'createStudyForm',
                                        labelWidth: 175, // label settings here cascade unless overridden
                                        bodyStyle:'padding:5px 5px 0',
                                        autoWidth:true,
                                        autoHeight:true,
                                        items: [{fieldLabel: 'ID', name: 'id', allowBlank:false, xtype:'hidden' },
                                                { xtype:'fieldset', 
                                                    title: 'Study Information', 
                                                    autoHeight:true, 
                                                    autoWidth:true, 
                                                    defaults: {width: 180}, 
                                                    labelWidth:105, 
                                                    defaultType: 'textfield',
                                                    items :[
                                                        {fieldLabel: 'Study Date', name: 'studydate', id: 'studyDateControl', allowBlank:false, xtype:"datefield", format: 'm/d/Y', width: 200, value: new Date()},
                                                        {fieldLabel: 'Study Time', name: 'studytime', allowBlank:false, xtype:"timefield", increment: 30, width: 200, format: 'H:i', value: new Date()},
                                                        {fieldLabel: 'Study Description', name: 'description', allowBlank:true, anchor:'100%', xtype:"textareafield"},
                                                        {fieldLabel: 'Referring Physician', name: 'referringphysician', 
                                                                    allowBlank:false, 
                                                                    xtype: 'combo',
                                                                    store: refPhysicianStore,
                                                                    id: 'cmbReferringPhysician',
                                                                    minChars:2,
                                                                    width:400,
                                                                    autoSelect: false,
                                                                    valueField: 'referringphysician',
                                                                    displayField: 'referringphysician',
                                                                    emptyText:'Referring Physician',
                                                                    listeners:{
                                                                        'specialkey': function(field, e)
                                                                        {
                                                                            if (e.getKey() == e.ENTER) 
                                                                            {
//                                                                            if (field.valueModels && field.valueModels.length >= 1)
//                                                                                alert ('selected');  
//                                                                            else
//                                                                                alert ('not selected');  
                                                                            }
                                                                        }
                                                                    }
                                                        },
                                                        {fieldLabel: 'Modality', 
                                                        store: modalityStore,
                                                        queryMode: 'local',
                                                        displayField: 'modality',
                                                        valueField: 'modality',
                                                        name: 'modality', 
                                                        xtype: 'combo'//,
//                                                        id: 'ModalityID',
//                                                        queryMode: 'local'//,
//                                                        autoSelect: false,
//                                                        valueField: 'modality',
//                                                        displayField: 'modality',
//                                                        emptyText:'Modality'
                                                        }
                                                    ]
                                                },
                                                { xtype:'fieldset', 
                                                    title: 'Patient Information', 
                                                    autoHeight:true, 
                                                    autoWidth:true, 
                                                    defaults: {width: 180}, 
                                                    labelWidth:105, 
                                                    defaultType: 'textfield',
                                                    items :[
                                                                {fieldLabel: 'Patient', 
                                                                name: 'patientid', 
                                                                xtype: 'combo',
                                                                store: patientStore,
                                                                id: 'cmbPatientID',
                                                                tpl: new Ext.XTemplate(
                                                                    '<tpl for="."><div class="x-boundlist-item">',
                                                                        '{patientname} ({birthdate}/ID: {origid})',
                                                                    '</div></tpl>'
                                                                ),
                                                                minChars:2,
                                                                width:400,
                                                                autoSelect: false,
                                                                valueField: 'origid',
                                                                displayField: 'patientname',
                                                                emptyText:'Patient Name',
                                                                listeners:{
                                                                        'specialkey': function(field, e)
                                                                        {
                                                                            if (e.getKey() == e.ENTER) 
                                                                            {
//                                                                            if (field.valueModels && field.valueModels.length >= 1)
//                                                                                alert ('selected');  
//                                                                            else
//                                                                                alert ('not selected');  
                                                                            }
                                                                        }
                                                                    }
                                                                },
                                                                {fieldLabel: 'New patient', id: '_field_new_patient', checked: false, name: 'new_patient', xtype:"checkboxfield",
                                                                listeners: 
                                                                {
                                                                    'change': function(self, newVal, oldVal, options)
                                                                    {
                                                                        var fullName = Ext.getCmp('cmbPatientID').getRawValue();
                                                                        var delimeterPos = fullName.indexOf(' ');
                                                                        Ext.getCmp('_field_patname').setValue(fullName.substr(0, delimeterPos));
                                                                        Ext.getCmp('_field_lastname').setValue(fullName.substr(delimeterPos + 1));
                                                                        
                                                                        if (newVal)
                                                                        {
                                                                            Ext.getCmp('cmbPatientID').setDisabled(true);
                                                                            Ext.getCmp('_field_patname').setDisabled(false);
                                                                            Ext.getCmp('_field_lastname').setDisabled(false);
                                                                            Ext.getCmp('_field_middlename').setDisabled(false);
                                                                            Ext.getCmp('_field_birthday').setDisabled(false);
                                                                            Ext.getCmp('_field_sex').setDisabled(false);
                                                                            Ext.getCmp('_field_neworigid').setDisabled(false);
                                                                        }
                                                                        else
                                                                        {
                                                                            Ext.getCmp('cmbPatientID').setDisabled(false);
                                                                            Ext.getCmp('_field_patname').setDisabled(true);
                                                                            Ext.getCmp('_field_lastname').setDisabled(true);
                                                                            Ext.getCmp('_field_middlename').setDisabled(true);
                                                                            Ext.getCmp('_field_birthday').setDisabled(true);
                                                                            Ext.getCmp('_field_sex').setDisabled(true);
                                                                            Ext.getCmp('_field_neworigid').setDisabled(true);
                                                                        }
                                                                    }

                                                                }
                                                                    
                                                                },
                                                                {fieldLabel: 'Patient id', id: '_field_neworigid', disabled: true, name: 'neworigid', allowBlank:false, xtype:"textfield", width: 200},
                                                                {fieldLabel: 'Name', id: '_field_patname', disabled: true, name: 'patname', allowBlank:false, xtype:"textfield", width: 200},
                                                                {fieldLabel: 'Lastname', id: '_field_lastname', disabled: true, name: 'lastname', allowBlank:false, xtype:"textfield", width: 200},
                                                                {fieldLabel: 'Middlename', id: '_field_middlename', disabled: true, name: 'middlename', xtype:"textfield", width: 200},
                                                                {fieldLabel: 'Birthday', id: '_field_birthday', disabled: true, name: 'birthday', allowBlank:false, xtype:"datefield", format: 'm/d/Y', width: 200, value: new Date()},
                                                                {fieldLabel: 'Sex', id: '_field_sex', disabled: true, name: 'sex', allowBlank:false, xtype:"combo", store: patientSex, value: "M", autoSelect: true, valueField: 'sex', displayField: 'sex', queryMode: 'local'}
                                                            ]
                                                        }
                                                ]
                                  })
                              ]

  
//                                new Ext.form.Label(
//                                   {
//                                      cls: 'x-form-item myBold',
//                                      text: 'Please select start and stop dates and press "Print pdfs":'
//                                   }),
//                                {
//                                    id: 'batchprint_date_start',
//                                    xtype: 'datefield',
//                                    name: 'datestart',
//                                    fieldLabel: 'Date Start',
////                                    value: startDateEmptyText,
//                                    labelWidth: 62,
//                                    width: 159//,
////                                                listeners:{'enter': buttonBatchPrintGoHandler}
//                                },
//                                {
//                                    id: 'batchprint_date_stop',
//                                    xtype: 'datefield',
//                                    name: 'datestop',
//                                    fieldLabel: 'Date Stop',
////                                    value: stopDateEmptyText,
//                                    labelWidth: 62,
//                                    width: 159//,
////                                                listeners:{'enter': buttonBatchPrintGoHandler}
//                                }
//                            ]

                        }); 
                        win.show();
                    },
                    markAsCritical:function()
                    {
                            if(mdi.study.hasSelection()){
                                    mdi.study.performAjaxAction("Mark Study as Critical");
                            }else{
                                    Ext.Msg.warn("Study  does not marked as reviewed");
                            }
                    },
                    markAsUnCritical:function()
                    {
                            if(mdi.study.hasSelection()){
                                    mdi.study.performAjaxAction("Mark Study as Uncritical");
                            }else{
                                    Ext.Msg.warn("Study  does not marked as reviewed");
                            }
                    },
                    showNoteWindow:function()
                    {
                            var noteTextOld = '';
                            htmlContent = '<table style="width:100%">'+
                            '<tr><td> Patient Name: </td><td> <b>%name%</b> </td><td></td>'+
                                    '<td> Note added by:</td><td> <b>%added_user%</b> </td>'+
                            '</tr>'+
                            '<tr><td> Study Date: </td><td><b> <b>%date%</b> </td><td></td>'+
                                    '<td> Note added on:</td><td> <b>%added_date%</b></td>'+
                            '</tr>'+
                            '<tr><td> Referring Physician: </td><td> <b>%physician%</b> </td><td></td>'+
                                    '<td> Modality: </td><td> <b>%modality%</b> </td>'+
                            '</tr>'+
                            '<tr><td> Study Description: </td><td> <b>%descr%</b> </td><td></td>'+
                                    '<td></td>'+
                            '</tr>'+
                            '</table>';
                            if(mdi.study.hasSelection())
                            {
                                    var selectedStudies = mdi.study.getSelections();
                                    var curStudy = selectedStudies[0];
                                    noteTextOld = curStudy.data.note_text;
                                    htmlContent = htmlContent.replace('%name%', curStudy.data.lastname + ' ' + curStudy.data.firstname)
                                    htmlContent = htmlContent.replace('%added_user%', curStudy.data.note_user)
                                    htmlContent = htmlContent.replace('%date%', curStudy.data.datetime)
                                    htmlContent = htmlContent.replace('%added_date%', curStudy.data.note_date)
                                    htmlContent = htmlContent.replace('%physician%', curStudy.data.referringphysician)
                                    htmlContent = htmlContent.replace('%descr%', curStudy.data.description)
                                    htmlContent = htmlContent.replace('%modality%', curStudy.data.modality)
                                    if (Ext.getCmp('window-add-note'))
                                            Ext.getCmp('window-add-note').hide();
                            }

                            var tb = new Ext.Toolbar({
                                    id: 'note-tb',
                                    items:[
                                                {text:'Print the Note', iconCls: 'ico-batch-print', handler:function(){window.open('/system/dispatch.php?control=UserControl&method=printStudyNote&studyID=' + curStudy.data.uid);}},
                                                {text:'Remove the Note', id: 'remove-note-btn', hidden:(mdi.isAdmin == 0), iconCls: 'ico-un-review', handler:function()
                                                    {
                                                        var resultHandler = function(param)
                                                        {
                                                            if (param == "yes")
                                                            {
                                                                mdi.study.addNote("");
                                                                Ext.getCmp('window-add-note').hide();
                                                            }
                                                        }
                                                        Ext.MessageBox.confirm('Confirm', 'Are you really want to remove the Note?', resultHandler);
                                                    }
                                                }
                                          ]
                            });
                            var win = new Ext.Window({
                                    title:'Note',
                                    iconCls: 'ico-note',
                                    bodyStyle:'padding:5px; background: #6F839A;',
                                    id:'window-add-note',
                                    height:270,
                                    width:600,
                                    modal:true,
                                    resizable:false,
                                    tbar:tb,						
                                    items:[
                                            {
                                                    id:'study-note-header',
                                                    xtype: 'panel',
                                                    bodyStyle  : '{background: #6F839A; float:left;width:100%;padding:10px 0; color:white;}',
                                                    html: htmlContent
                                              },
                                              new Ext.form.FormPanel({
                                              width      : 588,
                                              bodyPadding: 2,
                                              items: [{
                                                          xtype     : 'textareafield',
                                                          //grow      : true,
                                                          name      : 'message',
                                                          anchor    : '100%',
                                                          id        : 'note_textarea',
                                                          value     : noteTextOld
                                                        }]
                                                }),
                                                {
                                                    id:'study-note-footer',
                                                    xtype: 'panel',
                                                    bodyStyle  : '{background: #6F839A; float:left;width:100%;padding:10px 0; color:white;}',
                                                    items:[{id: 'study-note-footer-text', xtype:'label', html:' '}]
                                                }
                                    ],
                                    buttons:[
                                            {text:"Cancel", id:'note-btn-cancel', handler:function()
                                                    {
                                                        Ext.getCmp('window-add-note').close()}
                                                    },
                                            {text:"Save", id:'note-btn-save', handler: function()
                                                    {
                                                        mdi.study.addNote(Ext.getCmp('note_textarea').getValue()); 
                                                        Ext.getCmp('window-add-note').close()
                                                    }
                                            }
                                    ]
                            });
                            win.show();
                            var noteTextArea=Ext.get('note_textarea');
                                    noteTextArea.on('keypress',function(e)
                                                    {
                                                        var ti = Ext.getCmp('study-note-footer-text');
                                                        var a = noteTextArea.getValue();
                                                        // does not works in IE
                                                        if (a)
                                                            ti.getEl().update((a.length) + ' / 400 Chars');
                                                    }); 	
                            if (curStudy.data.note_text)
                                if (Ext.getCmp('note-btn-save'))
                                        Ext.getCmp('note-btn-save').hide();
                    },
                    legend:function()
                    {
                            var win = new Ext.Window({
                                    title:'The legend',
                                    iconCls: 'ico-legend',
                                    bodyStyle:'padding:5px',
                                    id:'window-batch-print-pdf',
                                    width:385,
                                    modal:true,
                                    resizable:false,
                                    html:'<table style="width:100%"><tr><td id="td2">'+
                                            '<div class="study-unread" ><div>&mdash; new studies </div></div>'+
                                            '<div class="study-read" ><div>&mdash; read studies </div></div>'+
                                            '</td></tr></tale>'
                            });
                            win.show();
                    },
                    admin_remove_item:function(itemType)
                    {
                        var self = this;
                        var resultHandler = function(param)
                        {
                            if (param == "yes")
                            {
//                                alert('aaaaaaaaaaa');
//                                5555555555555
                                if(self.hasSelection())
                                {
                                    self.performAjaxAction("remove the " + itemType);
                                }
                                else
                                {
                                    Ext.Msg.warn("Operation filed");
                                }
//                                mdi.study.addNote("");
//                                Ext.getCmp('window-add-note').hide();
                            }
                        }
                        Ext.MessageBox.confirm('Confirm', 'Are you really want to remove the ' + itemType + '?', resultHandler);
                    },
                    logs:function(itemID)
                    {
                        var startDateEmptyText = new Date();
                        startDateEmptyText.setDate(startDateEmptyText.getDate() /*- 1*/);
                        startDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');

                        var stopDateEmptyText = Ext.Date.format(new Date(), 'm/d/Y');

                        var storeCombo = Ext.create('Ext.data.Store', {
                        fields: ['descr', 'val'],
                        data : [
                            {"descr":"All", "val":"All"},
                            {"descr":"Critical study", "val":"Critical study"},
                            {"descr":"Email deleted", "val":"Email deleted"},
                            {"descr":"Study reviewed", "val":"Study reviewed"},
                            {"descr":"Email updated", "val":"Email updated"},
                            {"descr":"Emailed By Report", "val":"Emailed By Report"},
                            {"descr":"User added", "val":"User added"},
                            {"descr":"User deleted", "val":"User deleted"},
                            {"descr":"User updated" , "val":"User updated"},
                            {"descr":"Structured Report", "val":"Structured Report"}
                            //{"descr":"View Study", "val":"View Study"}
                        ]
                        });

                        var dorequest = function(field, e)
                        {
                            var tstore = Ext.getCmp('logs-grid-id').getStore();
                            var tproxy = tstore.getProxy();
                            tproxy.extraParams['study_id'] = itemID;

                            var el = comboElement
                            if (el)
                                tproxy.extraParams['logType'] = el.getValue();
                            else
                                tproxy.extraParams['logType'] = 'All';
                            el = Ext.getCmp('logsdatestart');
                            if (el)
                                tproxy.extraParams['dstart'] = el.getValue();
                            else
                                tproxy.extraParams['dstart'] = startDateEmptyText;
                            el = Ext.getCmp('logsdatestop');
                            if (el)
                                tproxy.extraParams['dstop'] = el.getValue();
                            else
                                tproxy.extraParams['dstop'] = stopDateEmptyText;
                            tstore.load();
                        };

                        var comboElement = Ext.create('Ext.form.ComboBox', {
                            fieldLabel: 'Log type',
                            store: storeCombo,
                            queryMode: 'local',
                            displayField: 'descr',
                            valueField: 'val',
                            value: 'All',
                            listeners:{'change': dorequest}
                        });

                        var logsMenuBar = {
                                xtype: 'toolbar',
                                id:'logs-grid-menu',
                                dock: 'top',
                                items:[
                                        {
                                            id: 'logsdatestart',
                                            xtype: 'datefield',
                                            name: 'datestart',
                                            fieldLabel: 'Start Date',
                                            value: startDateEmptyText,
                                            labelWidth: 62,
                                            width: 159,
                                            listeners:{'change': dorequest}
                                        },
                                        {
                                            id: 'logsdatestop',
                                            xtype: 'datefield',
                                            name: 'datestop',
                                            fieldLabel: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stop Date',
                                            bodyStyle: 'padding-left:20px',
                                            value: stopDateEmptyText,
                                            labelWidth: 82,
                                            width: 179,
                                            listeners:{'change': dorequest}
                                        },
                                        comboElement,
                                        {
                                            id:'btnLogsPrint',
                                            text:'Print',
                                            listeners:{'click': function()
                                                {
                                                    var childHtml = '<html>'+
                                                                    '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">'+
                                                                    '<style type="text/css">td {border: 1px black solid} table {border-collapse:collapse;width:100%;}</style>'+
                                                                    '<title>Event log</title>'+
                                                                    '</head>'+
                                                                    '<body>';
                                                    var st = Ext.getCmp('logs-grid-id').getStore();
                                                    var sdata = st.data.getRange(0, st.getCount());

                                                    childHtml += '<h4>';
                                                    var tstore = Ext.getCmp('logs-grid-id').getStore();
                                                    var tproxy = tstore.getProxy();

                                                    var el = Ext.getCmp('logsdatestart');
                                                    if (tproxy.extraParams['dstart'])
                                                        childHtml += 'Start date: ' + Ext.Date.format(el.getValue(), 'm/d/Y') + ';<br>';

                                                    el = Ext.getCmp('logsdatestop');
                                                    if (tproxy.extraParams['dstop'])
                                                        childHtml += 'Stop date: ' + Ext.Date.format(el.getValue(), 'm/d/Y') + ';<br>';

                                                    el = comboElement;
                                                    if (tproxy.extraParams['logType'])
                                                        childHtml += 'Log type: ' + el.getValue() + ';<br>';

                                                    el = Ext.getCmp('logs-grid-id');
                                                    childHtml += '</h4><table><theader><tr>';

                                                    var cols = el.columns;
                                                    var cj = cols.length;
                                                    var j = 0;
                                                    for (j = 0; j < cj; j++)
                                                    {
                                                        childHtml += '<th>' + cols[j].text + '</th>';
                                                    }

                                                    childHtml += '</tr></theader><tbody>';
                                                    var c = sdata.length;
                                                    var idx = '';
                                                    for (var i = 0; i < c; i++)
                                                    {
                                                        var e = sdata[i];
                                                        //console.log(e);
                                                        childHtml += '<tr>';
                                                        j = 0;
                                                        for (j = 0; j < cj; j++)
                                                        {
                                                            idx = cols[j].dataIndex
                                                            childHtml += '<td>' + e.raw[idx] + '</td>';
                                                        }

                                                        childHtml += '</tr>';
                                                    }

                                                    childHtml += '</tbody></table>';
                                                    var w = window.open('');
                                                    w.document.html = '';
                                                    w.document.write(childHtml);
                                                    w.print();
                                                }}
                                        }
                                ]
                            };
                                
                            var getlogs = function()
                            {
                                var logsGridStore =  Ext.create('Ext.data.Store', 
                                {
                                    model: 'modelAdminLogs',
                                    //remoteSort:true,
                                    totalProperty: 'recordcount',
                                    root:'data',
                                    autoLoad:false,
                                    proxy:
                                    {
                                        type: 'ajax',
                                        url: config.logs,
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

                                var win = new Ext.Window({
                                        title:'Logs',
                                        iconCls: 'ico-admin-logs',
                                        bodyStyle:'padding:5px;',
                                        id:'window-admin-logs',
                                        width:1000,
                                        modal:true,
                                        //resizable:true,
                                        height: 580,
                                        buttons: [{text:"Close", id:'buttonHistoryClose', listeners : {'click': function()
                                                                                                                    {
                                                                                                                        win.close();
                                                                                                                    }}}],
                                        items:
                                        [
                                            new Ext.grid.Panel({
                                                                iconCls:'ico-admin-logs',
                                                                store: logsGridStore,
                                                                //autoHeight: true,
                                                                height: 510,
                                                                remoteSort:true,
                                                                id:'logs-grid-id',
                                                                tbar:logsMenuBar,
                                                                columns: [
                                                                            {header: "Event date", width:117, dataIndex: 'event_date'},
                                                                            {header: "Event type", dataIndex: 'event_type'},
                                                                            {header: "ID", dataIndex: 'event_table_id'},
                                                                            {header: "User", dataIndex: 'user_name'},
                                                                            {header: "studydate", dataIndex: 'studydate'},
                                                                            {header: "patient_name", dataIndex: 'patient_name'},
                                                                            {header: "Event text", dataIndex: 'additional_text', flex: true}
                                                                         ]
                                                                })
                                        ]
                                });
                                
                                win.show();
                                dorequest();
                            }
                            getlogs();
                    },
                    batchPrintDialogShow:function(){
                        
                            var startDateEmptyText = new Date();
                            
                            var stopDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');
                            
                            startDateEmptyText.setDate(startDateEmptyText.getDate() - 1);
                            startDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');

                            var buttonBatchPrintGoHandler = function(btn)
                            {
                                var params = {
                                                control: 'BatchPrintControl',
                                                method: 'getProcessID'
                                             }
                                Ext.Ajax.request({
                                    url: 'system/dispatch.php',
                                    disableCaching: true,
                                    params: params,
                                    success: function(response)
                                    {
                                        var res = Ext.decode(response.responseText);
                                        if (res.success && !res.error_msg)
                                        {
                                            doRequest(res.process_id);
                                            window.open('/system/viewer/batchPrintView2.php?processID=' + res.process_id);
    //								win.close();
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
                        
                            var win = new Ext.Window({
                                    title:'Batch printing of the PDFs',
                                    iconCls: 'ico-batch-print',
                                    bodyStyle:'padding:5px',
                                    id:'window-batch-print-pdf',
                                    height:170, 
                                    width:200,
                                    modal:true,
//                                    resizable:false,
                                    buttons: [{text:"Print pdfs", id:'buttonBatchPrintGo', listeners : {'click': function()
                                                                                                                    {
                                                                                                                        buttonBatchPrintGoHandler();
                                                                                                                    }}}],
                                    items:[
                                            new Ext.form.Label(
                                               {
                                                  cls: 'x-form-item myBold',
                                                  text: 'Please select start and stop dates and press "Print pdfs":'
                                               }),
                                            {
                                                id: 'batchprint_date_start',
                                                xtype: 'datefield',
                                                name: 'datestart',
                                                fieldLabel: 'Date Start',
                                                value: startDateEmptyText,
                                                labelWidth: 62,
                                                width: 159//,
//                                                listeners:{'enter': buttonBatchPrintGoHandler}
                                            },
                                            {
                                                id: 'batchprint_date_stop',
                                                xtype: 'datefield',
                                                name: 'datestop',
                                                fieldLabel: 'Date Stop',
                                                value: stopDateEmptyText,
                                                labelWidth: 62,
                                                width: 159//,
//                                                listeners:{'enter': buttonBatchPrintGoHandler}
                                            }
                                        ]

                            });
                            var doRequest = function(progressID)
                            {
                                var params = {dstart:Ext.Date.format(Ext.getCmp('batchprint_date_start').getValue(), 'Y-m-d'), 
                                dstop: Ext.Date.format(Ext.getCmp('batchprint_date_stop').getValue(), 'Y-m-d'),
                                control: 'BatchPrintControl',
                                method: 'process',
                                progress_id : progressID
                                }
                                Ext.Ajax.request({
                                    url: 'system/dispatch.php',
                                    disableCaching: true,
                                    params: params,
                                    success: function(response)
                                    {
                                    },
                                    failure: function()
                                    {
                                    }
                                });
                            }

                            win.show();
                    },
                    viewReport:function(grid)
                    {
                        if(this.hasSelection(grid)){
                                var id = mdi.study.getSelections(grid)[0].data.uid;
                                this.performAjaxAction({params:{actions:"ViewReports"}, 
                                    success:function(){
                                        window.open('../transcriptions/'+id+'.pdf');
                                    },
                                    failure:function(){
                                        Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
                                    }
                                }, grid);
                        }else{
                            Ext.Msg.warn("Select a study to view the report");
                        }
                    },
                    mailReport:function(grid)
                    {
                        if(this.hasSelection()){
                            this.performAjaxAction({params:{actions:"ViewReports"}, 
                                    success:function(){
                                        var aa = mdi.study.getSelections();
                                        var id = aa[0].data.uid;
                                        var nw = window.open('/sendmail.php?study_id='+id);
                                        checkChildWindow(nw, function(){mdi.study.refresh()});
                                    },
                                    failure:function(){
                                        Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
                                    }
                            }, grid);
                        }else{
                            Ext.Msg.warn("Please select the study");
                        }
                    },
					mailReportAttach:function(grid)
                    {
                        if(this.hasSelection()){
                            this.performAjaxAction({params:{actions:"ViewReports"}, 
                                    success:function(){
                                        var aa = mdi.study.getSelections();
                                        var id = aa[0].data.uid;
                                        var nw = window.open('/automail.php?study_id='+id);
                                        checkChildWindow(nw, function(){mdi.study.refresh()});
                                    },
                                    failure:function(){
                                        Ext.Msg.info('A signed report is not yet available for this study, please check back soon.');
                                    }
                            }, grid);
                        }else{
                            Ext.Msg.warn("Please select the study");
                        }
                    },
                    toggleShowAll:function()
                    {
                        var store = this.getGrid().getStore();
                        if(store.getProxy().extraParams.showAll == undefined || store.getProxy().extraParams.showAll == false)
                            store.getProxy().extraParams.showAll = true;
                        else store.getProxy().extraParams.showAll = null;
                        store.load();
                    }
            },
            init:function()
            {
                var studyForm = new Ext.form.FormPanel({
                        id:'study-form',
                        renderTo:'study-form',
                        //standardSubmit:true,//this will allow us to submit the form as normal
                        url:config.action,
                        defaultType:'hidden',
                        items:[
                                {id:'actions', name:'actions'},
                                {id:'option', name:'option', value:'study'},
                                {id:'entry', name:'entry'}
                        ]
                })
                var fields = function(fieldArray){
                        var list = [];
                        for(var i = 0; i < fieldArray.length;i++)
                            list.push({name:fieldArray[i]});
                        return list;
                }

                Ext.define('Studies', {
                extend: 'Ext.data.Model',
                fields: ['uid','patientid','modality', 'datetime', 'reviewed', /*'patientname',*/, 'lastname', 'firstname', 'referringphysician', 'description', 'reviewed_text', 'is_critical', 'critical_date', 'mailed_date', 'images_cnt', 'has_attached_orders', 'note_date', 'note_text', 'note_user', 'dob', 'has_tech_notes']
                });

                Ext.define('PatienStudies', {
                extend: 'Ext.data.Model',
                fields: ['uid','id','patientid','modality','images', 'datetime', 'reviewed', 'patientname', 'lastname', 'firstname', 'referringphysician', 'description', 'reviewed_text', 'is_critical', 'critical_date', 'mailed_date', 'images_cnt']
                });
                
                Ext.define('modelAdminLogs', {
                extend: 'Ext.data.Model',
                fields: ['event_date', 'user_id', 'user_name', 'event_type','event_table_id', 'additional_text', 'studydate', 'patient_name']
                });
                
                Ext.define('modalityModel', 
                {
                    extend: 'Ext.data.Model',
                    fields: ['modality']
                });
                
                var mainGridStore = Ext.create('Ext.data.Store', {
                    autoLoad:false,
                    model: 'Studies',
                    remoteSort:true,
                    proxy:
                    {
                        type: 'ajax',
                        url: config.db,
                        actionMethods: {
                            read: 'POST'
                        },
                        reader: new Ext.data.JsonReader(
                        {
                            remoteSort:true,
                            totalProperty: 'recordcount',
                            root:'data'
                        })
                    },
                    pageSize: 20
                });
                
                var mainStudyGridHeight = index_php_mainGridHeight_ - 141;
                mainGridStore.autoLoad = true;
                mainGridStore.load();
                
                var patientStore = Ext.create('Ext.data.Store', {
                    model: 'PatienStudies',
                    remoteSort:true,
                    totalProperty: 'recordcount',
                    autoLoad: false,
                    proxy: 
                    {
                        type: 'ajax',
                        url: config.db,
                        reader: 
                        {
                            type: 'json',
                            root: 'data'
                        }
                    },
                    listeners:
                    {
                        'load':function(self, records, success)
                        {
                            if (records && records[0] && records[0].data)
                                Ext.getCmp('patient-grid').setTitle(records[0].data.patientname);
                        }
                    }
                });

                var pagingBar = new Ext.PagingToolbar({
//                    pageSize: 15,
                    store: mainGridStore,
                    displayInfo: true,
                    displayMsg: 'Displaying studies {0} - {1} of {2}',
                    emptyMsg: "No Studies to display"
                });
                var patientPagingBar = new Ext.PagingToolbar({
                        pageSize: 15,
                        store: patientStore,
                        displayInfo: true,
                        displayMsg: 'Displaying studies {0} - {1} of {2}',
                        emptyMsg: "No Studies to display"
                });

                var filterMenu = new Ext.SplitButton({
                        text:'Filter',
                        menu:{
                            items:
                            [
                                {text:'From', iconCls:'ico-date', menu:new Ext.picker.Date({
                                    listeners:{
                                        'select':function(self, date)                                                        {
                                            // maybe have to change to Ext.Date.format(
                                            var d = date.format('Y-m-d');
                                            if (d != mainGridStore.getProxy().extraParams.fromDate)
                                            {
                                                mainGridStore.getProxy().extraParams.fromDate = d;
                                                mainGridStore.getProxy().extraParams.showAll = null;
                                                mainGridStore.load();
                                            }
                                        }
                                    }
                                })},
                                {text:'To', iconCls:'ico-date', menu:new Ext.picker.Date({
                                        listeners:{
                                            'select':function(self, date){
                                                // maybe have to change to Ext.Date.format(
                                                var d = date.format('Y-m-d');
                                                if (d != mainGridStore.getProxy().extraParams.toDate)
                                                {
                                                    mainGridStore.getProxy().extraParams.toDate = d;
                                                    mainGridStore.getProxy().extraParams.showAll = null;
                                                    mainGridStore.load();
                                                }
                                            }
                                        }
                                    })
                                }
                            ]
                        }
                    });
                    
                    Ext.define('searchTypes',
                    {
                        extend: 'Ext.data.Model',
                        fields: ['type', 'search_column']
                    });
                    
                    var searchTypeStore = Ext.create('Ext.data.Store', 
                    {
                        model: 'searchTypes',
                        remoteSort:true,
                        totalProperty: 'recordcount',
                        root:'data',
                        autoLoad:false,
                        proxy: 
                        {
                        type: 'ajax',
                        url: 'system/dispatch.php?control=SearchControl&method=ViewSearchTypes',
                        reader: 
                            {
                                type: 'json',
                                root: 'data'
                            }
                        }
                    });
                    
                    var gridMenuBar = {
                            xtype: 'toolbar',
                            id:'grid-menu',
                            dock: 'top',
                            items:[
//                                    {text:'Download', iconCls:'ico-study-download', handler:function(){mdi.study.download();}},
//                                    {text:'Notes/Attachments', iconCls:'ico-study-note-attachment', handler:function(){mdi.study.viewNotesAndAttachments(Ext.getCmp('study-grid'));}},
                                    {text:'View', iconCls:'ico-study-viewer', handler: function(){mdi.study.viewDicom();}},
                                    {text:'Report', iconCls:'ico-study-view-report', handler:function(){mdi.study.viewReport();}},
									{text:'Email', iconCls:'ico-mail', handler:function(){mdi.study.mailReport();}},
                                    {text:'Priors', iconCls:'ico-history', handler:function(){mdi.study.viewPriors();}},
                                    {text:'Batch print', iconCls:'ico-batch-print', id:"menu-batch-print_button", handler:function(){mdi.study.batchPrintDialogShow();}},
                                    {text:'Burn CD', iconCls:'ico-burncd', id:'menu-burn-cd-button', handler:mdi.study.burnCDShow},
                                    {text:'New Study', iconCls:'ico-newstudy', hidden:false, id:'menu-create-new-study', handler:mdi.study.createStudy},
                                    '->',
                                    {text:'Advanced Search', id:'filter-advanced-search', handler: function()
                                        {
                                            var h = Ext.getCmp('maincont').height;
                                            if (h == 0)
                                                {
                                                    Ext.getCmp('maincont').hide();
                                                    Ext.getCmp('maincont').height = 27;
                                                    Ext.getCmp('maincont').show();
                                                }
                                            else
                                                {
                                                    Ext.getCmp('maincont').height = 0;
                                                    Ext.getCmp('maincont').hide();
                                                    for (var i = 1; i < 9; i++)
                                                    {
                                                        key = 'search' + i;
                                                        mainGridStore.getProxy().extraParams[key] = null;
                                                        key = 'searchColumn' + i;
                                                        mainGridStore.getProxy().extraParams[key] = null;
                                                        //key = 'searchmain' + i;
                                                        //Ext.getCmp(key).setValue('');
                                                    }
                                                    
                                                    mainGridStore.load();
                                                }
                                        }    
                                    },
                                    {text:'Show All', id:'filter-show-all', handler: function()
                                    { 
                                        mainGridStore.getProxy().extraParams.fromDate = null;
                                        mainGridStore.getProxy().extraParams.toDate = null;
                                        mainGridStore.getProxy().extraParams.search = null;
                                        for (var i = 1; i < 9; i++)
                                        {
                                            key = 'search' + i;
                                            mainGridStore.getProxy().extraParams[key] = null;
                                            key = 'searchColumn' + i;
                                            mainGridStore.getProxy().extraParams[key] = null;
                                        }
                                        mdi.study.toggleShowAll();
                                    }},
                                    //filterMenu,
                                    {
                                        xtype: 'combo',
                                        id: 'cmbSearchType',
                                        store: searchTypeStore,
                                        triggerAction: 'all',
                                        valueField: 'search_column',
                                        displayField: 'type',
                                        emptyText:'Patient Name',
                                        width: 100
                                    },
                                    new Ext.ux.SearchField({
                                        store: mainGridStore,
                                        width:210
                                    })
                            ]
                    };
                    var patientsMenuBar = new Ext.toolbar.Toolbar({
                            id:'patients-menu',
                            items:[
                                    {text:'Notes/Attachments', iconCls:'ico-study-note-attachment', handler:function(){mdi.study.viewNotesAndAttachments('patient-grid');}},					
                                    //{text:'View', iconCls:'ico-study-viewer', handler: function(){ mdi.study.viewDicom();}},
                                    {text:'Report', iconCls:'ico-study-view-report', handler:function(){mdi.study.viewReport('patient-grid');}},
                                    {text:'Batch print', iconCls:'ico-batch-print' 
                                        ,handler:function()
                                        {
                                            var selection = Ext.getCmp('patient-grid').getSelectionModel().getSelection();
                                            var entry = [];
                                            for(var i = 0; i < selection.length;i++)entry.push(selection[i].data.uid);
                                            var studiesID = entry.join(",");

                                            if (!studiesID)
                                            {
                                                Ext.Msg.error("Please select one or more studies");
                                                return 1;
                                            }
                                            var doRequest = function(progressID)
                                            {
                                                var params = {studies: studiesID,
                                                                control: 'BatchPrintControl',
                                                                method: 'processPriors',
                                                                progress_id : progressID};
                                                Ext.Ajax.request({
                                                        url: 'system/dispatch.php',
                                                        disableCaching: true,
                                                        params: params,
                                                        success: function(response)
                                                        {
                                                        },
                                                        failure: function()
                                                        {
                                                        }
                                                });
                                            };	
                                            var params={control: 'BatchPrintControl',
                                                                    method: 'getProcessID'};
                                            Ext.Ajax.request({
                                                                url: 'system/dispatch.php',
                                                                disableCaching: true,
                                                                params: params,
                                                                success: function(response)
                                                                {
                                                                    var res = Ext.decode(response.responseText);
                                                                    if (res.success && !res.error_msg)
                                                                    {

                                                                        doRequest(res.process_id);
                                                                        window.open('/system/viewer/batchPrintView2.php?processID=' + res.process_id);
        //                                                                                  win.close();
                                                                    }
                                                                    else
                                                                        Ext.Msg.error(res.error_msg);
                                                                },
                                                                failure: function()
                                                                {
                                                                    Ext.Msg.error("Failed to request");
                                                                }
                                                            });
                                            return 1;
                                        }
                                    },
                                    {text:'Burn CD', iconCls:'ico-burncd', id:'menu-burn-cd-button2', handler:function(){mdi.study.burnCDShow()}}
                            ]
                  })
    var patientGridView = {
    getRowClass : function (row, index, rowParams, store) {
    var cls = 'study-read';
    if(row.data.reviewed == "") cls = 'study-unread';
      return cls;
    }
    };
    var gridView = {
    getRowClass : function (row, index, rowParams, store) 
    {
    var cls = 'study-read';
    if(row.data.reviewed == "") 
        cls = 'study-unread';
    if(row.data.reviewed_user_id && row.data.reviewed_user_id.length > 0)
    {
        cls = 'study-reviewed';
    }
    return cls;
    }
    };
        var patientGridContextMenu = function()
        {
            var grid;
            this.init = function(g){
                grid = g;
                grid.on('itemcontextmenu', onContextMenu);
            }
            function onContextMenu(view, record, item, index, e)
            {
                        view.getSelectionModel().select(index);
                        var menu = Ext.getCmp('mmainMenu');
                        if (menu)
                            menu.destroy();

                        menu = new Ext.menu.Menu({
                        id:'mmainMenu',
                        items:[
                                {
                                  text: 'Priors',
                                  iconCls: 'ico-history',
                                  handler: function(){
                                    mdi.study.viewPriors();
                                  }
                                },
                                {
                                        id:'context-menu-attach-pdf',
                                        text: 'Attach report',
                                        hidden:(mdi.isAdmin == 0),
                                        iconCls: 'ico-file-pdf',
                                        handler: function(){
                                                var win = new Ext.Window({
                                                        title:'Attach report',
                                                        iconCls: 'ico-file-pdf',
                                                        bodyStyle:'padding:5px',
                                                        id:'window-attach-pdf',
                                                        height:115,
                                                        width:330,
                                                        modal:true,
                                                        resizable:false,
                                                        items:[
                                                                new Ext.form.FormPanel({
                                                                        id:'form-attach-pdf',
                                                                        frame:true,
                                                                        border:false,
                                                                        fileUpload:true,
                                                                        items:[
//                                                                                    {id: 'file-attachment',xtype:'fileuploadfield',name: 'file-attachment'}
                                                                                {
                                                                                    xtype: 'filefield',
                                                                                    name: 'file-attachment',
                                                                                    fieldLabel: '*.pdf file',
                                                                                    labelWidth: 100,
                                                                                    msgTarget: 'side',
                                                                                    allowBlank: false,
                                                                                    anchor: '100%',
                                                                                    buttonText: 'Select'
                                                                                }
                                                                        ]
                                                                })
                                                        ],
                                                        buttons:[
                                                                {text:"Cancel", handler:function(){Ext.getCmp('window-attach-pdf').close()}},
                                                                {text:"Attach", handler: function(){mdi.study.attachPDF('PDF')}}
                                                                ]
                                                });
                                                win.show();
                                        }
                                }
                              ]
          });

                  if (mdi.isCanMailPDF)
                        menu.add(
                        {
                          text: 'Email the report',
                          iconCls: 'ico-mail',
                          handler: function(){
                                mdi.study.mailReport();
                          }
                        });
                        var menuItem = {};
                        if (mdi.isCanMarkAsReviewed)
                        {
                            menuItem = new Ext.menu.Item({
                                text: 'Mark as reviewed',
                                id:'menu-item-mark-reviewd',
                                iconCls: 'ico-review',
                                handler: function(){
                                    mdi.study.markAsReviewd();
                                }
                            });
                            menu.add(menuItem);
                        }
                        if (mdi.isCanMarkCritical)
                        {
                            menuItem = new Ext.menu.Item({
                                    text: 'Mark as critical',
                                    id:'menu-item-mark-critical',
                                    iconCls: 'ico-critical',
                                    handler: function(){
                                        mdi.study.markAsCritical();
                                    }
                            });
                            menu.add(menuItem);

                            menuItem = new Ext.menu.Item({
                                    text: 'Remove critical status',
                                    id:'menu-item-mark-uncritical',
                                    iconCls: 'ico-uncritical',
                                    handler: function(){
                                        mdi.study.markAsUnCritical();
                                    }
                            });
                            menu.add(menuItem);
                        }

                        if (mdi.isCanAddNote)
                        {
                            menuItem = new Ext.menu.Item({
                                text: 'Add Note',
                                id:'menu-item-add-note',
                                iconCls: 'ico-note',
                                handler: function(){
                                        mdi.study.showNoteWindow();
                                }

                            });
                            menu.add(menuItem);
                        }

                        if (mdi.isCanAttachOrder)
                        {
                            menuItem = new Ext.menu.Item({
                                text: 'Attach an Order',
                                id:'menu-item-attach-order',
                                iconCls: 'ico-order',
                                handler: function()
                                {
                                    mdi.fileUpload('ORDER');
                                }
                            });
                            menu.add(menuItem);
                        }

                        if (mdi.isCanAttachOrder)
                        {
                            menuItem = new Ext.menu.Item({
                                text: 'Attach tech notes',
                                id:'menu-item-attach-tech_notes',
                                iconCls: 'ico-tech_notes',
                                handler: function()
                                {
                                    mdi.fileUpload('TECH_NOTES');
                                }
                            });
                            menu.add(menuItem);
                        }

                  if (mdi.isAdmin)
                        menu.add(
                        {
                            text: 'Mark as UNReviewed',
                            iconCls: 'ico-un-review',
                            handler: function()
                            {
                                mdi.study.markAsUnReviewd();
                            }
                        });
                  
                  if (mdi.isAdmin)
                  {
                        menu.add(
                        {
                            text: 'Show history',
                            iconCls: 'ico-admin-logs',
                            handler: function()
                            {
                                var aID = mdi.getSelectedRows();
                                aID = aID[0].data.uid;
                                mdi.study.logs(aID);
//                                console.log();
                            }
                        });

                        menu.add(
                        {
                            text: 'Remove ...',
                            iconCls: 'ico-admin_remove_report',
                            menu : [
                                {
                                    text: 'report',
                                    iconCls: 'ico-admin_remove_report',
                                    handler: function()
                                    {
                                        mdi.study.admin_remove_item('report');
                                    }
                                },
                                {
                                    text: 'order',
                                    iconCls: 'ico-admin_remove_order',
                                    handler: function()
                                    {
                                        mdi.study.admin_remove_item('order');
                                    }
                                },
                                {
                                    text: 'note',
                                    iconCls: 'ico-admin_remove_order',
                                    handler: function()
                                    {
                                        mdi.study.addNote("");
                                    }
                                },
                                {
                                    text: 'tech note',
                                    iconCls: 'ico-admin_remove_order',
                                    handler: function()
                                    {
                                        mdi.study.admin_remove_item('TECH_NOTES');
                                    }
                                }
                            ]
                        });
                  }
                  e.stopEvent();
                  menu.showAt(e.getXY());
            }
    };
mainStudyGridHeight = index_php_mainGridHeight_ - 141;
              var mainStudyGrid = new Ext.grid.Panel({
                            iconCls:'ico-study',
                            store: mainGridStore,
                            selModel:{mode: 'MULTI'},
                            multiselect:'true',
                            height:mainStudyGridHeight,
                            viewConfig:gridView,
                            plugins:new patientGridContextMenu(),
                            id:'study-grid',
                            listeners:{
                                    'itemdblclick': function(self,rowIndex,e)
                                    {
                                        mdi.study.viewDicom();
                                    }
                            },
                            tbar:gridMenuBar,
                            bbar:pagingBar,
                            columns: [
                                    {header: "Date", width:130, dataIndex: 'datetime'},
                                    {header: "Patient Id", dataIndex: 'patientid'},
                                    {header: '<span class="redtext" title="Marked as critical">&nbsp;!&nbsp;</span>', width:23, dataIndex: 'is_critical', sortable:false, renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value == '!')
                                            {
                                                var title = 'No email defined';
                                                if (record.data.critical_date)
                                                        title = 'Marked as critical and emailed on ' + record.data.critical_date;
                                                return '<div class="redtext" style="cursor:pointer;cursor:hand;" title="' + title + '">&nbsp;!&nbsp;</div>';
                                            }
                                            return '';
                                        }
                                    },
//                                  {header: "Patient Name", width:150, dataIndex: 'patientname'},
                                    {header: "Firstname", width:95, dataIndex: 'firstname'},
                                    {header: "Lastname", width:95, dataIndex: 'lastname'},
                                    {header: "DOB", width:85, dataIndex: 'dob'},
//                                  {header: "Study Id", width:150, dataIndex: 'id'},
                                    {header: "Study Description", width:150,  dataIndex: 'description'},
//                                  {header: "Images", dataIndex: 'images'},
                                    {header: "Referring Physician", width:130, dataIndex:'referringphysician'},
                                    {header: "Modality", dataIndex: 'modality'},
                                    {header: "<span title=\"Images\">#</span>", width:20,  dataIndex:'images_cnt'},
                                    {header: '<img src="system/ico/order.png" height="16px" width="14px" title="Order"/>', width:30, dataIndex: 'has_attached_orders', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value >= '1')
                                            {
                                                return '<a href="/orders/'+record.data.uid+'.pdf" target="_blank"><img src="system/ico/order.png" title="View Order" height="16px" width="14px"/></a>';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/emailed.png" height="16px" width="16px" title="Is emailed"/>', width:30, dataIndex: 'mailed_date', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value != '')
                                            {
                                                return '<img src="system/ico/emailed.png" height="16px" width="16px" title="Emailed to facility on ' + record.data.mailed_date + '" style="cursor:pointer;cursor:hand;" />';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/eye_preview.png" title="Is reviewed" height="16px" width="16px"/>', width:30, dataIndex: 'reviewed_text', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value != '')
                                            {
                                                return '<img style="height:16px;width:16px;cursor:pointer;cursor:hand;" src="system/ico/eye_preview.png" title="' + value + '" />';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/note.png" title="Notes" height="16px" width="16px"/>', width:30, dataIndex: 'note_text', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value != '')
                                            {
                                                return '<div style="height:16px;width:16px;cursor:pointer;cursor:hand;" class="ico-note" onclick="mdi.study.showNoteWindow()" title="' + record.data.note_user + ' (' + record.data.note_date + ') :   ' + record.data.note_text + '"/>';
                                            }
                                            return '';
                                        }},
                                    {header: '<img src="system/ico/tech_notes.png" title="Tech notes" height="16px" width="16px"/>', width:30, dataIndex: 'has_tech_notes', renderer: 
                                        function (value, metaData, record, rowIndex, colIndex, store) 
                                        {
                                            if (value == '1')
                                            {
                                                return '<a href="' + mdi.admin.dispatch + '?control=StudyControl&method=getTechNote&study_id=' + record.data.uid + '"><img style="height:16px;width:16px;" src="/system/ico/tech_notes.png"/></a>';
                                            }
                                            return '';
                                        }},
                                    {id:'reviewed_text',header: "Reviewed Text", dataIndex: 'reviewed_text', width:200}
                    ]
                });

              var patientGrid = new Ext.grid.Panel({
                            iconCls:'ico-study',
                            store: patientStore,
                            columns: [
                                    {id:'datetime',header: "Date", width:100, sortable: true, dataIndex: 'datetime'},
                                    {id:'id',header: "Study Id", width:100, sortable: true, dataIndex: 'uid'},
                                    {id:'modality',header: "Modality", sortable: true, dataIndex: 'modality'},
                                    {id:'description',header: "Study Description", width:100,  sortable: true, dataIndex: 'description', flex: true}
                            ],
                            stripeRows: true,
                            height:380,
                            viewConfig:patientGridView,
                            id:'patient-grid',
                            title:'Patient History',
                            tbar:patientsMenuBar,
                            listeners:{
                                    'itemdblclick' : function(self,rowIndex,e){
                                        mdi.study.viewDicom('patient-grid');
                                    }
                            },
                            selModel:{mode: 'MULTI'},
                            multiselect:'true',
                            bbar:patientPagingBar
              });
                var patientHistoryWindow = new Ext.Window({
                    id:'patient-history-window',
                    title:'Priors',
                    closeAction:'hide',
                    height:410,
                    width:800,
                    resizable:false,
                    items:[patientGrid]
                });

                var mainMenuBar = new Ext.toolbar.Toolbar(
                {
                    items:[
                        {text:'Home', iconCls:'ico-home', handler:function(){window.location = config.view}},
                        {text:'User Administration', iconCls:'ico-user-admin', id:'toolbar-button-user-admin', handler:mdi.admin.show},
                        //{text:'Profile', handler: function(){ window.location = 'profile.php?username=' + mdi.study.userName}},
                        {text:'Settings', iconCls:'ico-preferences', id:'toolbar-button-preferences', handler:mdi.preferences.show},
                        {text:'Legend', iconCls:'ico-legend', id:'toolbar-button-legend', handler:mdi.study.legend},
                        {text:'Logs', iconCls:'ico-admin-logs', id:'toolbar-button-logs', handler:function(){mdi.study.logs('');}},
                        '->',
                        {text:'Change Password', iconCls:'ico-password', handler:function(){mdi.passwordManager.show();}},
                        {text:'Logout', iconCls:'ico-login', handler:function(){mdi.authenticate.logout();}},
                        '-',
                        '<div id="login-icon" class="ico-user-login"><span id="login-status">test</span></div>',
                        {text:'Manual', iconCls:'ico-help', handler:function(){window.open('/manual.pdf')}}
                    ]
                });
                
                var curYear = new Date();
                curYear = curYear.getFullYear();
                var startDateEmptyText = '01/01/' + curYear;
                var stopDateEmptyText = '01/01/' + (curYear + 1);
                
                var advancedSearch = function()
                {
                    var arr = [];
                    var curVal = {};
                    var keyNum = 0;
                    var key = '';
                    var control = null;
                    var val = '';
                    for (var i = 1; i < 9; i++)
                    {
                        curVal = {};
                        key = 'searchmain' + i;
                        control = Ext.getCmp(key);
                        key = 'search' + i;
                        curVal[key] = control.getValue();
                        if (i == 4)
                        curVal[key] = curVal[key].replace('\*', '');
                        key = 'searchColumn' + i;

                        curVal[key] = control.getName();
                        arr[(i - 1)] = curVal;
                    }
                    var c = arr.length;
                    for (i = 0; i < c; i++)
                    {
                        for (key in arr[i])
                        {
                            mainGridStore.getProxy().extraParams[key] = arr[i][key];
                        }
                    }
                    mainGridStore.loadPage(1);
                }
                
                var advancedControlSearchClick = function(field, e)
                {
                    if (e.getKey() == e.ENTER)
                        advancedSearch();
                }
                
                Ext.define('institutionDescrStore',
                {
                    extend: 'Ext.data.Model',
                    fields: ['institution']
                });

                var institutionStore = Ext.create('Ext.data.Store', {
                    id:'institutionStore',
                    model: 'institutionDescrStore',
                    remoteSort:true,
                    autoLoad:false,
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
                    }
                });

                var view  = new Ext.Viewport(
                {
                    items: 
                    [
                        {
                            tbar:mainMenuBar,
                            region: 'north',
                            border:false,
                            autoHeight:true,
                            height:111,
                            contentEl:'study-header'
                        },
                        {id:'maingridcont', xtype: 'panel', region:'center', items:
                            [
                                {id: 'maincont', xtype: 'panel', layout: 'column', height: 0, border:false, items:
                                    [
                                        {
                                            id: 'searchmain1',
                                            xtype: 'textfield',
                                            labelWidth: 62,
                                            name: 'patient.firstname',
                                            fieldLabel: 'First Name',
                                            emptyText: 'First Name',
                                            width: 150,
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain2',
                                            xtype: 'textfield',
                                            labelWidth: 61,
                                            name: 'patient.lastname',
                                            fieldLabel: 'Last Name',
                                            emptyText: 'Last Name',
                                            width: 150,
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain3',
                                            store: institutionStore,
                                            xtype: 'combo',
                                            labelWidth: 41,
                                            name: 'patient.institution',
                                            fieldLabel: 'Facility',
                                            emptyText: 'Institution Name',
                                            width: 150,
                                            valueField: 'institution',
                                            displayField: 'institution',
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain4',
                                            xtype: 'textfield',
                                            labelWidth: 45,
                                            name: 'study.modality',
                                            fieldLabel: 'Modality',
                                            emptyText: '__',
                                            width: 75,
                                            listeners:{'specialkey': advancedControlSearchClick}

                                        },
                                        {
                                            id: 'searchmain5',
                                            xtype: 'textfield',
                                            labelWidth: 20,
                                            name: 'patient.origid ',
                                            fieldLabel: 'PID',
                                            emptyText: 'PID',
                                            width: 80,
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain6',
                                            xtype: 'datefield',
                                            name: 'patient.birthdate',
                                            fieldLabel: 'DoB',
                                            emptyText: 'Birthday',
                                            labelWidth: 23,
                                            width: 120,
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain7',
                                            xtype: 'datefield',
                                            name: 'study.studydate >',
                                            fieldLabel: 'Date range',
                                            emptyText: startDateEmptyText,
                                            labelWidth: 69,
                                            width: 166,
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain8',
                                            xtype: 'datefield',
                                            name: 'study.studydate <',
                                            fieldLabel: ':',
                                            emptyText: stopDateEmptyText,
                                            labelWidth: 7,
                                            width: 99,
                                            listeners:{'specialkey': advancedControlSearchClick}
                                        },
                                        {
                                            id: 'searchmain9',
                                            xtype: 'button',
                                            name: 'search',
                                            text: 'Search!',
                                            width: 60,
                                            listeners:{'click': advancedSearch}
                                        },
                                        {
                                            id: 'clearSearchBtn',
                                            xtype: 'button',
                                            name: 'clearSearchBtn',
                                            title: 'Clear filters',
                                            text: 'Clear',
                                            width: 40,
                                            listeners:{'click': function()
                                            {
                                                for (var i = 1; i < 9; i++)
                                                {
                                                    key = 'searchmain' + i;
                                                    control = Ext.getCmp(key);
                                                    control.setValue('');
                                                }
                                            }}
                                        }
                                    ]
                                }
                            ]
                        },
                        mainStudyGrid
                    ],
                    renderTo: 'study-view'
            });

            this.autoRefeshId = setInterval(function(){mdi.study.refresh();}, mdi.study.autoRefreshInterval*1000);
            Ext.getCmp('maincont').hide();
        },
        fileUpload:function(formType)
        {
            var winTitle;
            var iconCls;
            var fieldLabel;
            if (formType == 'ORDER')
            {
                winTitle = 'Attach an Order';
                iconCls = 'ico-file-pdf';
                fieldLabel = 'Order';
            }
            if (formType == 'TECH_NOTES')
            {
                winTitle = 'Attach file with Tech Notes';
                iconCls = 'ico-file-pdf';
                fieldLabel = 'Tech notes';
            }
            var win = new Ext.Window({
                title:winTitle,
                iconCls: iconCls,
                bodyStyle:'padding:5px',
                id:'window-attach-pdf-order',
                height:115,
                width:330,
                modal:true,
                resizable:false,
                items:[
                        new Ext.form.FormPanel({
                            id:'form-attach-pdf',
                            frame:true,
                            border:false,
                            fileUpload:true,
                            items:[
                                    {
                                        xtype: 'filefield',
                                        name: 'file-attachment',
                                        fieldLabel: fieldLabel,
                                        labelWidth: 100,
                                        msgTarget: 'side',
                                        allowBlank: false,
                                        anchor: '100%',
                                        buttonText: 'Select'
                                    }
                            ]
                        })
                ],
                buttons:[
                        {text:"Cancel", handler:function(){Ext.getCmp('window-attach-pdf-order').close()}},
                        {text:"Attach", handler: function(){mdi.study.attachPDF(formType)}}
                ]
            });
            win.show();
        }
    };
}();


function checkChildWindow(win, onclose) {
    var w = win;
    var cb = onclose;
    var t = setTimeout(function() { checkChildWindow(w, cb); }, 500);
    var closing = false;
    try {
        if (win.closed || win.top == null) //happens when window is closed in FF/Chrome/Safari
        closing = true;
    } catch (e) { //happens when window is closed in IE        
        closing = true;
    }
    if (closing) {
        clearTimeout(t);
        onclose();
    }
}
