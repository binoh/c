<div id='statistics_main'>
</div>
<script type="text/javascript">
	//$.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=aapl-c.json&callback=?', function(data) {
	var data= [2,7,9,25,31,32,9]; 
		Highcharts.setOptions({
			lang: {
		    	rangeSelectorFrom: '从',
		    	rangeSelectorTo: ' 至',
		    	rangeSelectorZoom: '时间跨度' 
		    }
	    	});
		
		chart=new Highcharts.StockChart({
		    chart: {
			    renderTo:'statistics_main',
		    	 events:{
			    	 addSeries:function(){
				       // return confirm('are you sure');
			    		 
			        }
		    	 }
		    },
		    
		    tooltip: {
            },
            credits:{
                enabled:false
            },
		    rangeSelector: {
		        selected: 1
		    },
		    title: {
		        enabled:false
		    },
		    rangeSelector: {
                buttons: [{
                        type: 'day',
                        count: 7,
                        text: '1周'
                }, {
                        type: 'month',
                        count: 1,
                        text: '1月'
                }, {
                        type: 'month',
                        count: 3,
                        text: '1季'
                }, {
                        type: 'month',
                        count: 6,
                        text: '半年'
                },  {
                        type: 'all',
                        text: '全部'
                }],
                buttonSpacing:5,
                inputDateFormat:'%Y-%m-%d',
                inputBoxStyle: {
                    right: '80px'
                },
                label: '跨度',
                selected: 2
            },
            xAxis: {  
                //tickPixelInterval:160,//x轴上的间隔  
                //startOfWeek: 5, 
                //type: 'datetime', //定义x轴上日期的显示格式 
                categories:['1','2','3','4','5','6','7']
                /*labels: {  
                    formatter: function() {  
                        var vDate=new Date(this.value);  
                        var year = vDate.getFullYear().toString().substr(2,2);
                        var month = (vDate.getMonth()+1);
                        //return 'Y'+year+"M"+month;  //当前日期是第几月
                        return vDate.getFullYear()+"-"+(vDate.getMonth()+1)+"-"+vDate.getDate();  
                    },*/  
                  //  align: 'center'  
                //}  
            },
            tooltip: {
	        	 pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
	                valueDecimals: 0,
	                xDateFormat: '%Y-%m-%d'
	        },
		    series: [{
		        name: 'series',
		        data: data,
		        type: 'spline',
		        events:{
			        click:function(event){
				        //alert(event.point.pageX);
				        //console.log(event);
			        	//console.log(this);
			        	var aa=[7,16,17,20,25,26,4];
			        	//this.series.data.push(aa);
				        chart.addSeries();
				        //chart.height=800
			        }
		        	
		        }
			       
		       
		    }]
		});
		//chart.showLoading();
	//});

		</script>