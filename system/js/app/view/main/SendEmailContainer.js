Ext.define('MdiApp.view.main.SendEmailContainer',{
	extend : 'Ext.form.Panel',
	show : function(id){
		this.callParent(arguments)
	},
	defineItems : function(config){
		var self=this
		config=config||{}
		config.defaults={
			//frame : true
		}
		config.scroll=true
			
		config.frame=true
		//this.callParent(arguments);
	},
	initComponent : function(){
		var self=this
		var buttons=[];
		var sendBtn={
			xtype : 'button',
			text : 'Send',
			handler : function(){
				var values=self.getForm().getValues()
				self.getForm().submit({
					url:'system/dispatch.php',
					waitMsg : 'Sending the Mail',
					waitTitle : 'Mailing the pdf',
					params : {pdf_file_name:iframe_cmp.el.dom.src},
					success : function(form,action){
						Ext.Msg.info('Email Send')
						var parent=self.findParentByType('studymainview')
						parent.setActiveItem(0)
					},
					failure:function(form,action){
						if (action.failureType === Ext.form.action.Action.CONNECT_FAILURE) {
                        Ext.Msg.error(
                            'Status:'+action.response.status+': '+
                            action.response.statusText);
						}
						if (action.failureType === Ext.form.action.Action.SERVER_INVALID){
							// server responded with success = false
							Ext.Msg.error('Email was NOT sent. Please try again<br />'+action.result.message);
						}
					}
				});
			}
		}
		var cancelBtn={
			xtype : 'button',
			text : 'Cancel',
			handler : function(){
				var parent=self.findParentByType('studymainview')
				parent.setActiveItem(0)
			}
		}
		var searchTypeStore =  Ext.create('Ext.data.Store', 
			{
				fields:['id', 'mail'],
				//remoteSort:true,
				totalProperty: 'recordcount',
				rootProperty:'data',
				autoLoad:false,
				proxy:
				{
					type: 'ajax',
					//url: 'system/dispatch.php?control=MailControl&method=ViewMails&studyID={studyID}',
					actionMethods: {
						read: 'POST'
					},
					reader:
					{
						type: 'json',
						rootProperty: 'data'
					}
				}
			});
		var to=Ext.create('Ext.form.field.ComboBox',{
			xtype : 'combo',
			fieldLabel : 'To',
			store: searchTypeStore,
			standardSubmit:true,
            valueField: 'mail',
            displayField: 'mail',
            triggerAction: 'all',
			itemId : 'email_to_combo',
			name : 'mailTo',
			vtype: 'email',
			allowBlank : false,
			//emptyText:'{default_email}'
		})
		var subject={
			xtype : 'textfield',
			fieldLabel : 'Subject',
			name : 'mailSubject',
			value : 'Study report'
		}
		var text={
			xtype : 'textarea',
			fieldLabel : 'Text',
			name : 'mailText',
			value : 'Report attached'
		}
		var check={
			xtype : 'checkbox',
			fieldLabel : 'Include HIPAA Cover Sheet',
			inputValue : 1,
			uncheckedValue : 0,
			name : 'includeHippa'
		}
		var file_name={
			xtype :'hidden',
			name : 'pdf_file_name'
		}
		var study_id={
			xtype :'hidden',
			name : 'studyID'
		}
		var default_email={
			xtype :'hidden',
			name : 'default_email'
		}
		/*buttons.push({
			xtype: 'displayfield',
			value : 'Root'
		})*/
		
		
		buttons.push(sendBtn)
		//buttons.push({xtype: 'spacer'})
		buttons.push(cancelBtn)
		var fields=[]
		fields.push({
			xtype : 'hidden',
			name : 'control',
			value:'MailControl'
		})
		fields.push({
			xtype : 'hidden',
			name : 'method',
			value : 'SendMail'
		})
		fields.push(default_email)
		fields.push(to)
		
		fields.push(subject)
		fields.push(text)
		fields.push(check)
		fields.push(file_name)
		fields.push(study_id)
		var iframe_cmp=Ext.create('Ext.Component',{
			autoEl : {
				tag: 'iframe',
				src : 'about:blank',
				style: 'height: 400px; width: 100%; border: none',
			}
		})
		
		fields.push(iframe_cmp)
		self.items=[{
			xtype : 'panel',
			//liquidLayout: true,
			items : [
			{
				xtype: 'displayfield',
				value : 'Root'
			}
			],
			buttons : buttons
		},{
			xtype : 'fieldset',
			defaults : {
				width : '100%'
			},
			items : fields
		}];	
		this.callParent(arguments);
		this.on('show',function(){			
            var id = Ext.getCmp('studyGrid').getSelection()[0].data.uid;			
			searchTypeStore.getProxy().setUrl('system/dispatch.php?control=MailControl&method=ViewMails&studyID='+id)
			var url='/sendmail2.php?study_id='+id
			self.getForm().load({
				waitMsg: 'Loading data',
				waitTitle : 'Loading...',
				url :url,
				success : function(form,action){
					iframe_cmp.el.dom.src=action.result.data.pdf_file_name
					to.setValue(action.result.data.default_email)
				},
				failure:function(){
					Ext.Msg.error('Form not able to load')
				}
			})
		})
	}
});