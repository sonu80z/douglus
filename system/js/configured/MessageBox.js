/**
 * @author Jesse Chrestler
 */
//This allows us to provide a much easier way to popup alerts with out so much configuration.
Ext.applyIf(Ext.Msg, {
	checkOptions:function(options){
		if(typeof options == "undefined")options = {};
		if(typeof options == "string")options = {msg:options};
		return Ext.applyIf(options, {msg:'Message'});
	},
	error:function(options){
		this.show(Ext.applyIf(this.checkOptions(options), {title:'Error', buttons:Ext.Msg.OK, icon:Ext.Msg.ERROR}));
	},
	warn:function(options){
		this.show(Ext.applyIf(this.checkOptions(options), {title:'Warning', buttons:Ext.Msg.OK, icon:Ext.Msg.WARNING}));
	},
	info:function(options){
		this.show(Ext.applyIf(this.checkOptions(options), {title:'Message', icon:Ext.Msg.INFO}));
	},
	invalid:function(options){
		this.error(Ext.applyIf(this.checkOptions(options), {title:'Validation Error'}));
	}
});
