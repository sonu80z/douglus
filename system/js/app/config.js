Ext.namespace('MdiApp');
Ext.apply(MdiApp,{
	isAdmin:0,
	isCanMailPDF:0,
	isCanBatchPrintPDFs:0,
	isCanMarkAsReviewed:0,
	isCanBurnCD:0,
	isCanMarkCritical:0,
	isCanAttachOrder:0,
	isStaffRole:0,
	isCanAddNote:0,
	isCanViewHTML5Viewer:1,
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
	setUsername:function(user)
            {
                username = user;
				console.log(user)
                var el = document.getElementById('login-status');
                if (el)
                    el.innerHTML = username;
            },
     setAdmin:function(isAdmin){
		MdiApp.isAdmin = isAdmin;
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
		    el = Ext.getCmp('menu-create-new-study');
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
		    el = Ext.getCmp('menu-create-new-study');
                    if (el)
                        el.show();
//                    Ext.getCmp('menu-create-new-study').show();

                }
            },
	setLoadingText:function(txt){
		//document.getElementById('loading-msg').innerHTML = txt;
	},
	setCanMarkAsReviewed:function(val){
		MdiApp.isCanMarkAsReviewed = val;
	},
	setCanAddNote:function(val){
		var r = false;
		if (val == '1')
				r = true;
		MdiApp.isCanAddNote = r;
	},
	setCanMailPDF:function(val){
		MdiApp.isCanMailPDF = val;
	},
	setCanBatchPrintPDFs:function(val){
		if (!val)
			document.getElementById('menu-batch-print_button').style.display="none";

		MdiApp.isCanBatchPrintPDFs = val;
	},
	setCanBurnCD:function(val){
		if (!val)
			document.getElementById('menu-burn-cd-button').style.display="none";
		MdiApp.CanBurnCD = val;
	},
	setCanMarkCritical:function(val){
		var r = false;
		if (val == '1')
			r = true;
		MdiApp.isCanMarkCritical = r;
	},
	setCanAttachOrder:function(val){
		var r = false;
		if (val == '1')
			r = true;
		MdiApp.isCanAttachOrder = r;
	},
	setStaffRole:function(val){
		var r = false;
		if (val == '1')
				r = true;
		MdiApp.isStaffRole = r;
		if (this.isStaffRole) 
		{
			Ext.getCmp('toolbar-button-preferences').show();
			Ext.getCmp('menu-create-new-study').show();
		}
	},
	setCanViewHTML5Viewer:function(val){
		var r = false;
		if(val == '1')
			r = true;
		MdiApp.isCanViewHTML5Viewer = r;
	},
	getCanViewHTML5Viewer:function(){
		var canvasEl = document.createElement('canvas'); //create the canvas object
			if(!canvasEl.getContext) //if the method is not supported, i.e canvas is not supported
				return false;
		return true;
	},
	performAjaxAction:function(action, grid, params)
			{
				//base options
				var o = {url:MdiApp.config.action, params:Ext.applyIf({entry:grid.getEntries(),actions:action,option:MdiApp.option}, params)};
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
							//mdi.study.refresh();
						}
				});
			},
	
})
Ext.namespace('MdiApp').config={
	db: "system/legacy/db/study.php",
	view:"index.php",
	action:"system/actionItem.php",
	logs:"system/dispatch.php?control=LogsControl&method=getLogs",
	
}
Ext.namespace('MdiApp').admin = {
		dispatch:"system/dispatch.php",
		request: function(options){
                Ext.Ajax.request({
                        url: options.url || MdiApp.admin.dispatch,
                        method: options.urlMethodType || "POST",
                        disableCaching: true,
                        params: options.params,
                        success: function(response){
                                if (typeof options.success == "function") {
                                options.success(Ext.decode(response.responseText));
                          }
                        }
                });
        }
	}

	
	
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
