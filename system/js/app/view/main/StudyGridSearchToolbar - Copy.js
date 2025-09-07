Ext.define('MdiApp.view.main.StudyGridSearchToolbar',{
	extend : 'Ext.toolbar.Toolbar',
	
		constructor : function(config){
			var self=this;
			config=config||{}
			var mainGridStore=config.grid.getStore()
			var fields=[];
			var buttons=[];
			var institutionStore =Ext.create('MdiApp.store.InstitutionStore',{});
			var startDateEmptyText = new Date();
            startDateEmptyText.setDate(startDateEmptyText.getDate() /*- 1*/);
            startDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');

            var stopDateEmptyText = Ext.Date.format(new Date(), 'm/d/Y');
			var advancedControlSearchClick = function(field, e)
                {
                    if (e.getKey() == e.ENTER)
                        advancedSearch();
                }
			var advancedSearch = function()
                {
                    var arr = [];
                    var curVal = {};
                    var keyNum = 0;
                    var key = '';
                    var control = null;
                    var val = '';
					arr=Ext.Array.map(fields,function(item,index){
						var curVal={}
						key = 'search' + (index+1);
						curVal[key] = item.getValue();
						if (index == 3)
							curVal[key] = curVal[key].replace('\*', '');
						key = 'searchColumn' + (index+1);
						curVal[key] = item.getName();
						return curVal;
					});					
                    var c = arr.length;
					Ext.Array.forEach(arr,function(item){
						for (key in item)
                        {
                            mainGridStore.getProxy().extraParams[key] = item[key];
                        }
					})
                    mainGridStore.loadPage(1);
                }
			fields.push(Ext.create('Ext.form.field.Text',{
					//id: 'searchmain1',
					xtype: 'textfield',
					labelWidth: 70,
					name: 'patient.firstname',
					fieldLabel: 'Filter criteria',
					emptyText: 'First Name',
					width: 150,
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			fields.push(Ext.create('Ext.form.field.Text',{
					//id: 'searchmain2',
					xtype: 'textfield',
//                  labelWidth: 61,
					name: 'patient.lastname',
//                  fieldLabel: 'Last Name',
					emptyText: 'Last Name',
					width: 70,
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			fields.push(Ext.create('Ext.form.field.ComboBox',{
					//id: 'searchmain3',
					store: institutionStore,
					xtype: 'combo',
//                  labelWidth: 41,
					name: 'patient.institution',
//                  fieldLabel: 'Facility',
					emptyText: 'Institution',
					matchFieldWidth:false,
					width: 90,
					valueField: 'institution',
					displayField: 'institution',
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			fields.push(Ext.create('Ext.form.field.Text',{
					//id: 'searchmain4',
					xtype: 'textfield',
					labelWidth: 45,
					name: 'study.modality',
					fieldLabel: 'Modality',
					emptyText: '__',
					width: 75,
					listeners:{'specialkey': advancedControlSearchClick}

				}))
			fields.push(Ext.create('Ext.form.field.Text',{
					//id: 'searchmain5',
					xtype: 'textfield',
//              	labelWidth: 20,
					name: 'patient.origid ',
//                  fieldLabel: 'PID',
					emptyText: 'PID',
					width: 40,
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			fields.push(Ext.create('Ext.form.field.Date',{
					//id: 'searchmain6',
					xtype: 'datefield',
					name: 'patient.birthdate',
//                  fieldLabel: 'DoB',
					emptyText: 'Birthday',
//                  labelWidth: 23,
					width: 150,
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			fields.push(Ext.create('Ext.form.field.Date',{
					//id: 'searchmain7',
					xtype: 'datefield',
					name: 'study.studydate >',
					fieldLabel: 'Date range',
					emptyText: startDateEmptyText,
					labelWidth: 66,
					width: 150,
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			fields.push(Ext.create('Ext.form.field.Date',{
					//id: 'searchmain8',
					xtype: 'datefield',
					name: 'study.studydate <',
					fieldLabel: '',
					emptyText: stopDateEmptyText,
					labelWidth: 3,
					width: 150,
					listeners:{'specialkey': advancedControlSearchClick}
				}))
			buttons.push(Ext.create('Ext.button.Button',{
					//id: 'searchmain9',
					xtype: 'button',
					name: 'search',
					text: 'Search!',
					//width: 60,
					listeners:{'click': advancedSearch}
				}))
			buttons.push(Ext.create('Ext.button.Button',{
					//id: 'clearSearchBtn',
					xtype: 'button',
					name: 'clearSearchBtn',
					title: 'Clear filters',
					text: 'Clear',
				   // width: 40,
					listeners:{'click': function()
					{
						Ext.Array.forEach(fields,function(item){
							item.reset()
						});
					}}
				}))
			var items=Ext.Array.merge(fields,buttons)
			config.items=items;
			this.callParent(arguments);
		}
});