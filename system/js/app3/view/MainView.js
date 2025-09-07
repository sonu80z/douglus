/**
 * This class is the main view for the application. It is specified in app.js as the
 * "mainView" property. That setting causes an instance of this class to be created and
 * added to the Viewport container.
 *
 * TODO - Replace the content of this view to suit the needs of your application.
 */
Ext.define('MdiApp.view.MainView', {
    extend: 'Ext.Container',
    
    layout : 'border',
	requires: [
        'MdiApp.view.main.MainController'
    ],
	//html : 'hello',
	controller: 'main',
    items: [
		Ext.create('MdiApp.view.main.TopContainer',{
			region : 'north',
			contentEl:'study-header',
			items : Ext.create('MdiApp.view.main.MainToolbar',{})
		}),
		Ext.create('MdiApp.view.main.Grid',{region:'center'})   
    ]
});
