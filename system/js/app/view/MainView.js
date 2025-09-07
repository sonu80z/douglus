/**
 * This class is the main view for the application. It is specified in app.js as the
 * "mainView" property. That setting causes an instance of this class to be created and
 * added to the Viewport container.
 *
 * TODO - Replace the content of this view to suit the needs of your application.
 */
Ext.define('MdiApp.view.MainView', {
    extend: 'Ext.Container',
    xtype : 'studymainview',
    layout : 'card',
	requires: [
        'MdiApp.view.main.MainController'
    ],
	//html : 'hello',
	controller: 'main',
	activeItem : 0,
    items: [
	{
		xtype  : 'container',
		layout : 'border',
		itemId : 'container-study',
		items : [Ext.create('MdiApp.view.main.TopContainer',{
			region : 'north',
			contentEl:'study-header',
			items : Ext.create('MdiApp.view.main.MainToolbar',{})
		}),
		Ext.create('MdiApp.view.main.StudyGrid',{region:'center',reference: 'studyGrid',id:'studyGrid'})   ]
	},
	Ext.create('MdiApp.view.main.SendEmailContainer',{
		scroll : true,
		autoScroll: true,
		//frame : true,
		//region:'center'
	}),
	Ext.create('MdiApp.view.main.CreateStudyContainer',{
		scroll : true,
		autoScroll: true,
		itemId : 'create-study'
	})
    ]
});
