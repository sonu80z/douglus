 Ext.define('MdiApp.controller.StudyGridController', function(){
	 
	 function performAjaxAction(){
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
	 }
	 
	return {
		extend: 'Ext.app.Controller',

		init: function() {
			console.log('Initialized Users! This happens before ' +
			'the Application launch() function is called11');
		},
		refs: [{
			ref: 'studyGrid',
			selector: 'study\\.grid'
		}],
		control: {
			'study\\.grid': {
				render: 'onPanelRendered',
				rowcontextmenu:'rowcontextmenu'
			},
				'study\\.grid button[studyAction] , menuitem[studyAction]' : {
				click : 'studyAction'
			}
		},
		onPanelRendered: function() {
			console.log('The panel was rendered');
		},
		studyAction : function(action){
			var grid=this.getStudyGrid();
			var studyAction=action.studyAction
			if(Ext.isObject(studyAction)){
			}
			console.log(arguments)
			console.log(this.getStudyGrid())
		},
		performAjaxAction : function(){
			
		},
		rowcontextmenu:function( grid, record, tr, rowIndex, e, eOpts){
			var grid=this.getStudyGrid()
			e.preventDefault();
			e.stopPropagation();
			e.stopEvent();
			var self=this
			try{
				var menu=Ext.create('MdiApp.view.main.StudyGridContextMenu',{
					record:record,
					grid:grid
				});
				//grid.mask()
				menu.showAt(e.getXY())
			}catch(ex){
				console.log(ex)
			}

		}
	}
 });