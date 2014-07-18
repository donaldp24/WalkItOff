<script type="text/javascript">
//<![CDATA[
	jQuery(function($) {
		$.datepicker.regional[""].dateFormat = 'yy-mm-dd';
		$.datepicker.setDefaults($.datepicker.regional['']);

		var oTable1 = $('#sample-table-2').dataTable( {
            "oLanguage": {
                "sUrl": rootUri + "www/i18n/dataTables.chinese.txt"
            },
            "aaSorting": [[5, "desc"]],
			"aoColumns": [
//			  { "bSortable": false, bFilterable: false, "sClass": "center" },
			  null, null, 
			  null,null, null, null
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "goodsstatistic/retrievestatisticlist"
		})
		.columnFilter({ 	
			sPlaceHolder: "head:before",
			aoColumns: [ 	
//				null,
				{ sSelector: "#goodsname", type: "text" },
				null,
				null,
				null,
				{ sSelector: "#membername", type: "text" },
				{ sSelector: "#ordertime", type: "date-range" }
			]
		});


        //$("#sample-table-2_rang_from_5").style.width = "20px";

			/*
			.columnFilter({aoColumns:[
				{ type: "text" },
				{ type: "text" },
				{ type: "text" },
				{ type: "text" },
				{ type: "text" },
				{ type: "text" }
				]}
			);
		*/
	
		$('table th input:checkbox').on('click' , function(){
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
			.each(function(){
				this.checked = that.checked;
				$(this).closest('tr').toggleClass('selected');
			});
		});


		$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
			_title: function(title) {
				var $title = this.options.title || '&nbsp;'
				if( ("title_html" in this.options) && this.options.title_html == true )
					title.html($title);
				else title.text($title);
			}
		}));

	});

	function onClickChart(uid)
	{
		$.ajax({
			type: "POST",
			url: rootUri + "goodsstatistic/getchartdata/" + uid,
			dataType: "json",
/*	
			data: {
				orderid: targetid,
				smscontent: smscontent,
				receiver: smsreceiver,
				phonenum: phonenum
			},
*/
			success: onSendSuccess,
			error: onSendError
		});
	}

	function onSendSuccess(data) {
		send_dialog = $( "#modal-send-deliver" ).dialog({
			modal: true,
			width: "500px",
			title: "<div class='widget-header widget-header-small'><h4 class='smaller'>区域分布</h4></div>",
			title_html: true,
			buttons: [ 
				{
					text: "确定",
					"class" : "btn btn-primary btn-mini",
					click: function() {
						$( this ).dialog( "close" ); 
					} 
				}
			]
		});
		showchart();
	}

	function onSendError(xhr) {
		bootbox.alert("发送失败， 请再试一下！");
	}

	function drawPieChart(placeholder, data, position) {
	  $.plot(placeholder, data, {
		series: {
			pie: {
				show: true,
				tilt:0.8,
				highlight: {
					opacity: 0.25
				},
				stroke: {
					color: '#fff',
					width: 2
				},
				startAngle: 2
			}
		},
		legend: {
			show: true,
			position: position || "ne", 
			labelBoxBorderColor: null,
			margin:[-30,15]
		}
		,
		grid: {
			hoverable: true,
			clickable: true
		}
	 })
	}

	function showchart()
	{
		
		  var placeholder = $('#piechart-placeholder').css({'width':'90%' , 'min-height':'150px'});
		  var data = [
			{ label: "social networks",  data: 38.7, color: "#68BC31"},
			{ label: "search engines",  data: 24.5, color: "#2091CF"},
			{ label: "ad campaings",  data: 8.2, color: "#AF4E96"},
			{ label: "direct traffic",  data: 18.6, color: "#DA5430"},
			{ label: "other",  data: 10, color: "#FEE074"}
		  ]
		 drawPieChart(placeholder, data);
		
		 /**
		 we saved the drawing function and the data to redraw with different position later when switching to RTL mode dynamically
		 so that's not needed actually.
		 */
		 placeholder.data('chart', data);
		 placeholder.data('draw', drawPieChart);

		  var $tooltip = $("<div style='z-index:20000;' class='tooltip top in hide'><div class='tooltip-inner'></div></div>").appendTo('body');
		  var previousPoint = null;
		
		  placeholder.on('plothover', function (event, pos, item) {	
			if(item) {
				if (previousPoint != item.seriesIndex) {
					previousPoint = item.seriesIndex;
					var tip = item.series['label'] + " : " + item.series['percent']+'%';
					$tooltip.show().children(0).text(tip);
				}
				$tooltip.css({top:pos.pageY + 10, left:pos.pageX + 10});
			} else {
				$tooltip.hide();
				previousPoint = null;
			}
		 });
	}
//]]>
</script>