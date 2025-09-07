Ext.define('MdiApp.view.admin.LogWindow',{
	extend : 'Ext.Window',
	title:'Logs',
	iconCls: 'ico-admin-logs',
	bodyStyle:'padding:5px;',
	id:'window-admin-logs',
	width:1000,
	modal:true,
	//resizable:true,
	height: 580,
	
	constructor : function(config){
		var self=this;
		config=config||{}
		var startDateEmptyText = new Date();
		startDateEmptyText.setDate(startDateEmptyText.getDate() /*- 1*/);
		startDateEmptyText = Ext.Date.format(startDateEmptyText, 'm/d/Y');

		var stopDateEmptyText = Ext.Date.format(new Date(), 'm/d/Y');
		var printWindow=function(){
			var tpl=new Ext.XTemplate(
				'<html>',
				'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">',
				'<style type="text/css">td {border: 1px black solid} table {border-collapse:collapse;width:100%;}</style>',
				'<title>Event log</title>',
				'</head>',
				'<body>',
				'<h4>',
				'Start date: {start_date};<br />',
				'Stop date: {stop_date};<br >',
				'Log type: {log_type};<br />',
				'<table><theader><tr>',
				'<theader><tr>',
				'<tpl for="cols">',
				'<th>{text}</th>',
				'</tpl>',
				'</tr></theader><tbody>',
				'<tpl for="rows">',
				'<tr>',
				'<tpl foreach=".">',
				'<td>{.}</td>',
				'</tpl>',
				'</tr>',
				'</tpl>',
				'</tbody></table>',
				'</body></html>',
				{strict:true}
				)
				var tpl_data={};
				var st = logsGridStore//Ext.getCmp('logs-grid-id').getStore();
				var sdata = st.data.getRange(0, st.getCount());

				var tstore = logsGridStore//Ext.getCmp('logs-grid-id').getStore();
				var tproxy = tstore.getProxy();
				if(config.study_id)
					tproxy.extraParams['study_id'] = study_id;

				var el = Ext.getCmp('logsdatestart');
				if (tproxy.extraParams['dstart']){
					tpl_data['start_date']=Ext.Date.format(el.getValue(), 'm/d/Y')
				}

				el = Ext.getCmp('logsdatestop');
				if (tproxy.extraParams['dstop']){
					tpl_data['stop_date']=Ext.Date.format(el.getValue(), 'm/d/Y')
				}
				

				el = comboElement;
				if (tproxy.extraParams['logType']){
					tpl_data['log_type']=el.getValue()
				}

				el = grid//Ext.getCmp('logs-grid-id');

				var cols = el.columns;
				tpl_data.cols=cols
				
				var cj = cols.length;
				var c = sdata.length;
				var idx = '';
				tpl_data.rows=[]
				for (var i = 0; i < c; i++)
				{
					var e = sdata[i];
					row={}
					for (var j = 0; j < cj; j++)
					{
						idx = cols[j].dataIndex
						row[idx]=e.data[idx]
					}
					tpl_data.rows.push(row)
				}
				tplstr=tpl.apply(tpl_data)
				var w = window.open('');
				w.document.html = '';
				w.document.write(tplstr);
				w.print();
		}
		var storeCombo = Ext.create('Ext.data.Store', {
                        fields: ['descr', 'val'],
                        data : [
                            {"descr":"All", "val":"All"},
                            {"descr":"Critical study", "val":"Critical study"},
                            {"descr":"Email deleted", "val":"Email deleted"},
                            {"descr":"Study reviewed", "val":"Study reviewed"},
                            {"descr":"Email updated", "val":"Email updated"},
                            {"descr":"Emailed By Report", "val":"Emailed By Report"},
                            {"descr":"User added", "val":"User added"},
                            {"descr":"User deleted", "val":"User deleted"},
                            {"descr":"User updated" , "val":"User updated"},
                            {"descr":"Structured Report", "val":"Structured Report"}
                            //{"descr":"View Study", "val":"View Study"}
                        ]
                        });
		
		var dorequest = function(field, e)
                        {
                            //var tstore = Ext.getCmp('logs-grid-id').getStore();
                            var tproxy = logsGridStore.getProxy();
							
							if(config.study_id)
								tproxy.extraParams['study_id'] = config.study_id;

                            var el = comboElement
                            if (el)
                                tproxy.extraParams['logType'] = el.getValue();
                            else
                                tproxy.extraParams['logType'] = 'All';
                            el = Ext.getCmp('logsdatestart');
                            if (el)
                                tproxy.extraParams['dstart'] = el.getValue();
                            else
                                tproxy.extraParams['dstart'] = startDateEmptyText;
                            el = Ext.getCmp('logsdatestop');
                            if (el)
                                tproxy.extraParams['dstop'] = el.getValue();
                            else
                                tproxy.extraParams['dstop'] = stopDateEmptyText;
                            logsGridStore.load();
                        };
		var comboElement = Ext.create('Ext.form.field.ComboBox', {
                            fieldLabel: 'Log type',
                            store: storeCombo,
                            queryMode: 'local',
                            displayField: 'descr',
                            valueField: 'val',
                            value: 'All',
                            listeners:{'change': dorequest}
                        });
		var logsMenuBar = {
                                xtype: 'toolbar',
                               // id:'logs-grid-menu',
                              //  dock: 'top',
                                items:[
                                        {
                                            id: 'logsdatestart',
                                            xtype: 'datefield',
                                            name: 'datestart',
                                            fieldLabel: 'Start Date',
                                            value: startDateEmptyText,
                                            labelWidth: 62,
                                            width: 159,
                                            listeners:{'change': dorequest}
                                        },
                                        {
                                            id: 'logsdatestop',
                                            xtype: 'datefield',
                                            name: 'datestop',
                                            fieldLabel: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Stop Date',
                                            bodyStyle: 'padding-left:20px',
                                            value: stopDateEmptyText,
                                            labelWidth: 82,
                                            width: 179,
                                            listeners:{'change': dorequest}
                                        },
										comboElement,
										{
											id:'btnLogsPrint',
											text:'Print',
											listeners:{'click': printWindow}
										}
                                ]
                            };
		
		var logsGridStore =  Ext.create('Ext.data.Store', 
                                {
                                    //model: 'modelAdminLogs',
                                    //remoteSort:true,
									fields: ['event_date', 'user_id', 'user_name', 'event_type','event_table_id', 'additional_text', 'studydate', 'patient_name'],
                                    totalProperty: 'recordcount',
                                    rootProperty:'data',
                                   // autoLoad:true,
                                    proxy:
                                    {
                                        type: 'ajax',
                                        url: MdiApp.config.logs,
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
		var buttons={
					text:"Close", 
					id:'buttonHistoryClose', 
					listeners : {
						'click': function()
						{
							self.close();
						}
					}
				};
		var grid=Ext.create('Ext.grid.Panel',{
					//iconCls:'ico-admin-logs',
					store: logsGridStore,
					//autoHeight: true,
					height: 510,
					remoteSort:true,
					//id:'logs-grid-id',
					//tbar:logsMenuBar,
					columns: [
								{header: "Event date", width:150, dataIndex: 'event_date'},
								{header: "Event type",width:150, dataIndex: 'event_type'},
								{header: "ID", dataIndex: 'event_table_id'},
								{header: "User", dataIndex: 'user_name'},
								{header: "studydate", dataIndex: 'studydate'},
								{header: "patient_name", dataIndex: 'patient_name'},
								{header: "Event text", dataIndex: 'additional_text', flex: true}
							 ]
					})
		config.items=grid;
		config.buttons=buttons
		config.tbar=logsMenuBar
		config.listeners={
			show : dorequest
		}
		this.callParent(arguments);
	}
})