Ext.define('MdiApp.view.main.win.ForwardWindow',{
	extend : 'Ext.Window',
	title:'Forward',
	modal : true,
	layout : 'fit',
	constructor : function(config){
		var self=this;
		let grid=config.grid
		let uid = grid.getSelection()[0].data.uid;
		let applentityStore = Ext.create('Ext.data.Store', {
		    fields: ['title'],
		    rootProperty:'data',
			autoLoad:true,
			proxy: 
			{
				type: 'ajax',
				url: '/system/actionItem.php?actions=getApplentity',
				actionMethods: {
					read: 'POST'
				},
				reader: 
				{
					type: 'json',
					rootProperty: 'data',
					totalProperty: 'recordcount'
				}
			}
		});
		let applentityCombo=Ext.create('Ext.form.ComboBox', {
		    fieldLabel: 'Choose applentity',
		    store: applentityStore,
		    queryMode: 'remote',
		    displayField: 'title',
		    valueField: 'title',
		    name : 'aetitle',
		    allowBlank : false
		});
		let form=Ext.create('Ext.form.Panel',{
			items : [
				applentityCombo,
				{
					xtype : 'hidden',
					name : 'uid',
					value : uid
				}
			]
		})
		self.buttons=[
			{
				text : 'Close',
				handler : function(){
					self.close()
				}
			},
			{
				text : 'Submit',
				waitMsg : 'Submitting data...',
				waitTitle : 'Please wait',
				handler : function(btn){
					if(form.getForm().isValid()){
						btn.disable()
						form.getForm().submit({
							url : '/system/actionItem.php?actions=forward',
							method : 'POST',
							success: function(form, action) {
	       						Ext.Msg.alert('Success', action.result.msg);
	       						btn.enable()
	    					},
	    					failure : function(form, action){
	    						btn.enable()
	    						Ext.Msg.alert('Error', 'Some error occur');
	    					}
						})
					}else{
						Ext.Msg.alert('Check the form','Some fields are not valid');
					}
				}
			}
		]
		self.items=[form]
		this.callParent(arguments);
	}
})
