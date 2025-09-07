Ext.ux.SearchField = Ext.extend(Ext.form.TwinTriggerField, {
    initComponent : function()
    {
        Ext.ux.SearchField.superclass.initComponent.call(this);
        this.on('specialkey', function(f, e){
            if(e.getKey() == e.ENTER){
                this.onTrigger2Click();
            }
        }, this);
    },
	afterRender: function() {
        Ext.form.TwinTriggerField.superclass.afterRender.call(this);
        if (Ext.isIE && !this.hideTrigger) {
            this.el.position();  
            this.el.applyStyles("top: 1px;");
        } 
    },
    validationEvent:false,
    validateOnBlur:false,
    trigger1Class:'x-form-clear-trigger',
    trigger2Class:'x-form-search-trigger',
    hideTrigger1:true,
    width:180,
    hasSearch : false,
    paramName : 'imageName',

    onTrigger1Click : function(){
        if(this.hasSearch){
            this.el.dom.value = '';
            this.store.getProxy().extraParams['search'] = null;
            this.store.getProxy().extraParams['searchColumn'] = null;
            this.store.load();
            if (this.triggers && this.triggers[0])
                this.triggers[0].hide();
            this.hasSearch = false;
        }
    },

    onTrigger2Click : function()
    {
        var v = this.getRawValue();
        if(v.length < 1){
            this.onTrigger1Click();
            return;
        }
        
        this.store.getProxy().extraParams = this.store.getProxy().extraParams || {};
        this.store.getProxy().extraParams['search'] = v;
        var searchColumn = null;
        var searchTypeCombo = Ext.getCmp('cmbSearchType');
		if(searchTypeCombo){
        	searchColumn = searchTypeCombo.getValue();
        
        
		}
		this.store.getProxy().extraParams['searchColumn'] = searchColumn; 
        this.store.loadPage(1);
        this.hasSearch = true;
//		this.triggers[0].show();
    }
});