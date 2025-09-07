/**
 * This class is the main view for the application. It is specified in app.js as the
 * "mainView" property. That setting causes an instance of this class to be created and
 * added to the Viewport container.
 *
 * TODO - Replace the content of this view to suit the needs of your application.
 */
Ext.define('MdiApp.view.MainView', {
    extend: 'Ext.Container',
    
    layout : 'card',
	id : 'mainview',
	requires: [
        'MdiApp.view.main.MainController'
    ],
	//html : 'hello',
	controller: 'main',
	activeItem : 0,
	switchTab : function(item){
		this.setActiveItem(item)
	},
    items: [{
		xtype : 'container',
		layout : 'border',
		items : [Ext.create('MdiApp.view.main.TopContainer',{
			region : 'north',
			contentEl:'study-header',
			items : Ext.create('MdiApp.view.main.MainToolbar',{})
		}),
		Ext.create('MdiApp.view.main.StudyGrid',{region:'center',reference: 'studyGrid'})   ]
	},{
		xtype : 'container',
		layout:'fit',
		tbar : {items:{xtype:'button',handler:function(){Ext.getCmp('mainview').setActiveItem(0)},text:'Back'}},
		items:{	xtype : 'container',
			id : 'iframe-tag',
			autoEl : {
				tag: 'iframe',
				style: 'height: 100%; width: 100%; border: none',
				
				//src : 'system/html5viewer/index.php?entry=1.2.826.0.1.3680043.2.737.15923.2019.9.3.19.25.49&actions=DicomViewer'
			}
		}
		
	}
    ]
});
