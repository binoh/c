<div id='statistics_main'>
</div>
<script type="text/javascript">
function createChart(data,navigatordata) {
    Highcharts.setOptions({
	lang: {
    	rangeSelectorFrom: '从',
    	rangeSelectorTo: ' 至',
    	rangeSelectorZoom: '时间跨度' 
    },
    colors: ['#50B432',
             '#ED561B',
             '#DDDF00',
             '#24CBE5', 
             '#64E572', 
             '#FF9655', 
             '#FFF263',
             '#6AF9C4',
             '#4572A7', 
             //以下是默认的9种颜色，一个tab下最多包括29个指标(含目标值)
         	 '#AA4643', 
        	 '#89A54E',
        	 '#80699B', 
        	 '#3D96AE', 
        	 '#DB843D', 
        	 '#92A8CD', 
        	 '#A47D7C', 
        	 '#B5CA92']
	});
   // console.log(data)
    chart = new Highcharts.StockChart({
        chart: {
            renderTo: 'sys',
            zoomType: 'x',
            shadow: true,
            events: {
                addSeries: function() {

                },
                //单击空白区域时目标值线隐藏
    			click:function(){
    				if(showTarget.name){
						var len = chart.series.length-1
                       	chart.series[len].remove();
                        //一次只能查看一条目标值
                        showTarget = [];
                    }
        		},
        		load:function(){
            	}
            },
            height:400+seriesOptions.length*2.6,
            marginBottom:60+seriesOptions.length*2.6
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
        navigator: {
            series: {
                data: navigatordata    
            },
            top:300,
            xAxis: {  
                tickPixelInterval: 200,//x轴上的间隔  

                type: 'datetime', //定义x轴上日期的显示格式  
                labels: {  
                    formatter: function() {  
                        var vDate=new Date(this.value);  
                        return vDate.getFullYear()+"-"+(vDate.getMonth()+1)+"-"+vDate.getDate();  
                    },  
                    align: 'center'  
                }  
            }
        },
        plotOptions: {
            series: {
                events: {
                    click: function(event) {
                        if(showTarget.name){//如果图上有目标值的线
							if(showTarget.target == this.options.__index){
								var len = chart.series.length-1
                               	chart.series[len].remove();//一次只能查看一条目标值
                                showTarget = [];
							}else{
								var len = chart.series.length-1
                               	chart.series[len].remove();//一次只能查看一条目标值
                                showTarget = [];
								//如果当前单击的线存在目标值
                                targetColor = this.color;
                            	//console.log(this);
                            	var __key = this.options.__index;
                            	$.each(targetOptions,function(i,n){
                                    if(__key == n.target){
										n.color=targetColor;   
										n.dashStyle='ShortDash';                                 		
                                        showTarget = n;
                                        chart.addSeries(showTarget);
                                    }
                                });
							}
                        }else{
                        	//如果当前单击的线存在目标值
                        	targetColor = this.color;
                        	var __key = this.options.__index;
                        	$.each(targetOptions,function(i,n){
                                if(__key == n.target){
									n.color=targetColor;   
									n.dashStyle='ShortDash';                                 		
                                    showTarget = n;
                                    chart.addSeries(showTarget);
                                }
                            });
                        }
                    }
                },
                //compare: 'percent',
                marker: {
					enabled: true,
					symbol:'circle',
					radius:0
                }
            },
            //显示/隐藏异常原因
            line:{
				events:{
					legendItemClick: function(event){
						return false;//取消图例单击事件
					}
				}
            }
        },
        legend: {
            enabled: true,
            align:'center',
            verticalAlign: 'bottom',
            layout: 'horizontal',
            x:5,
            itemStyle: {
                cursor: 'hand'
            },
            itemMarginBottom:2
        },

        credits:{
            enabled:false
        },
        xAxis: {  
            tickPixelInterval: 160,//x轴上的间隔  
            startOfWeek: 5, 
            type: 'datetime', //定义x轴上日期的显示格式  
            labels: {  
                formatter: function() {  
                    var vDate=new Date(this.value);  
                    var year = vDate.getFullYear().toString().substr(2,2);
                    var month = (vDate.getMonth()+1);
                    //return 'Y'+year+"M"+month;  //当前日期是第几月
                    return vDate.getFullYear()+"-"+(vDate.getMonth()+1)+"-"+vDate.getDate();  
                },  
                align: 'center'  
            }  
        },  

        yAxis: [{
        		//linkedTo: 1,
                lineWidth: 1
            }, {
            	//linkedTo: 0,
                lineWidth: 1,
                opposite: true,
                tickPixelInterval:250 
            }],
     /*   yAxis: [{
	    		lineWidth: 1,
	            startOnTick:false,
	            //tickPixelInterval:200
	        }, {
	            lineWidth: 1,
	            opposite: true,
	            startOnTick:false,
	            gridLineWidth:0,
	            tickPixelInterval:250 
	        }],*/

        tooltip: {
            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b><br/>',
            valueDecimals: 0,
            xDateFormat: '%Y-%m-%d'
            /*
            useHTML: true,
            headerFormat: '<small>{point.key}</small><BR>',
            pointFormat: '<table><tr><td style="color: {series.color}">{series.name}: </td>' +
            '<td style="text-align: left"><b>{point.y}</b>({point.change}%)</td></tr>',
            footerFormat: '</table>',
            */
        },
        exporting: {
            buttons: {
                exportButton: {
                    menuItems: [{
                        text: 'Export to PNG (small)',
                        onclick: function() {
                            this.exportChart({
                                width: 250
                            });
                        }
                    }, {
                        text: 'Export to PNG (large)',
                        onclick: function() {
                            this.exportChart(); // 800px by default
                        }
                    },
                    null,
                    null
                    ]
                }
            }
        }, 
        series: data 
    });
}
createChart(seriesOptions,navigatordata);        
chart.showLoading();


		</script>