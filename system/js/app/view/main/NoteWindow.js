Ext.define('MdiApp.view.main.NoteWindow',{
	extend : 'Ext.Window',
	title:'Note',
	iconCls: 'ico-note',
	bodyStyle:'padding:5px; background: #6F839A;',
   // id:'window-add-note',
	closeAction : 'destroy',
	height:400,
	width:600,
	modal:true,
	resizable:false,
	_constructor : function(config){
		var self=this;
		config=config||{}
		this.callParent(arguments);
	},
	constructor : function(config){
		var self=this;
		config=config||{}
		var grid=config.grid
		
		var tbar_item=[];
		tbar_item.push({text:'Print the Note', iconCls: 'ico-batch-print', handler:function(){window.open('/system/dispatch.php?control=UserControl&method=printStudyNote&studyID=' + curStudy.data.uid);}})
		tbar_item.push({
			text:'Remove the Note', 
			/*id: 'remove-note-btn',*/ 
			hidden:(MdiApp.isAdmin == 0), 
			iconCls: 'ico-un-review', 
			handler:function()
			{
				Ext.MessageBox.confirm('Confirm', 'Are you really want to remove the Note?', function(btn)
				{
					if (btn == "yes")
					{
						MdiApp.study.addNote("",grid);
						self.close()
						//Ext.getCmp('window-add-note').hide();
					}
				});
			}
			})
        var tbar = Ext.create('Ext.Toolbar',{
                                    /*//id: 'note-tb',*/
                                    items:tbar_item
                            });
		if(grid.getSelectionModel().getCount())
			{
					var selectedStudies = grid.getSelection();
					var curStudy = selectedStudies[0];
					var noteTextOld = curStudy.data.note_text;
					var data={};
					data['name']=curStudy.data.lastname + ' ' + curStudy.data.firstname
					data['added_user']=curStudy.data.note_user
					data['date']=curStudy.data.datetime
					data['added_date']=curStudy.data.note_date
					data['physician']=curStudy.data.referringphysician
					data['descr']=curStudy.data.description
					data['modality']=curStudy.data.modality
					if (Ext.getCmp('window-add-note'))
							Ext.getCmp('window-add-note').hide();
			}
		var tpl=new Ext.XTemplate('<table style="width:100%">',
                            '<tr><td> Patient Name: </td><td> <b>{name}</b> </td><td></td>',
                                    '<td> Note added by:</td><td> <b>{added_user}</b> </td>',
                            '</tr>',
                            '<tr><td> Study Date: </td><td><b> <b>{date}</b> </td><td></td>',
                                    '<td> Note added on:</td><td> <b>{added_date}</b></td>',
                            '</tr>',
                            '<tr><td> Referring Physician: </td><td> <b>{physician}</b> </td><td></td>',
                                    '<td> Modality: </td><td> <b>{modality}</b> </td>',
                            '</tr>',
                            '<tr><td> Study Description: </td><td> <b>{descr}</b> </td><td></td>',
                                    '<td></td>',
                            '</tr>',
                            '</table>');
		var note_textarea=Ext.create('Ext.form.field.TextArea',{
								  xtype     : 'textareafield',
								  grow      : true,
								  name      : 'message',
								  anchor    : '100%',
							 /*//     id        : 'note_textarea',*/
								  value     : noteTextOld
								})
		note_textarea.on('keypress',function(e)
			{
				var a = note_textarea.getValue();
				// does not works in IE
				if (a)
					study_note_footer_text.getEl().update((a.length) + ' / 400 Chars');
			}); 
		var study_note_footer_text=Ext.create('Ext.form.Label',{html:' '})
		var items=[
					{
						/*//    id:'study-note-header',*/
						xtype: 'panel',
						bodyStyle  : '{background: #6F839A; float:left;width:100%;padding:10px 0; color:white;}',
						tpl: tpl,
						data : data
					},
					Ext.create('Ext.form.FormPanel',{
					//width      : 588,
						bodyPadding: 2,
						items: [note_textarea]
					}),
					{
						/*//id:'study-note-footer',*/
						xtype: 'panel',
						bodyStyle  : '{background: #6F839A; float:left;width:100%;padding:10px 0; color:white;}',
						items:[study_note_footer_text]
					}
				];
		config['tbar']=tbar
		config['items']=items
		config['buttons']=[]
		config.buttons.push(
						{text:"Cancel", id:'note-btn-cancel', handler:function()
								{
									self.close()}
								})
		if(!curStudy.data.note_text){
			config.buttons.push(
						{text:"Save", id:'note-btn-save', handler: function()
								{
									MdiApp.study.addNote(note_textarea.getValue(),grid); 
									self.close()
								}
						})
		}
		this.callParent(arguments);
	}
});