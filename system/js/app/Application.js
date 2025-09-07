/**
 * The main application class. An instance of this class is created by app.js when it
 * calls Ext.application(). This is the ideal place to handle application launch and
 * initialization details.
 */
Ext.define('MdiApp.Application', {
    extend: 'Ext.app.Application',
    
    name: 'MdiApp',
	
	controllers: ['StudyGridController'],

    stores: [
        // TODO: add global / shared stores here
    ],
    
    launch: function () {
		//MdiApp.authenticate.userSession();
        // TODO - Launch the application
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
Ext.define('Ext.grid.Panel',{
	override: 'Ext.grid.Panel',
	getEntries:function(){
		var selection = this.getSelection();
                        var entry = [];
                        for(var i = 0; i < selection.length;i++)entry.push(selection[i].data.uid);
                        return entry.join(",");
	}
})
    },

    onAppUpdate: function () {
        Ext.Msg.confirm('Application Update', 'This application has an update, reload?',
            function (choice) {
                if (choice === 'yes') {
                    window.location.reload();
                }
            }
        );
    }
});
