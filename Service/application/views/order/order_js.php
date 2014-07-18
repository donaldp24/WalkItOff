<script type="text/javascript">
//<![CDATA[
var staticcontent = "您订购的商品已送出，请您注意查收，需要时可直接与送货员联系。为了便于我们第一时间将货物送到您的手中，请尽量保持电话处于可接通状态。" + 
"配送员已经从站点出发，请您准备收货，咨询热线：0755-1234567";
	jQuery(function($) {
		var oTable1 = $('#sample-table-2').dataTable( {
            "oLanguage": {
                "sUrl": rootUri + "www/i18n/dataTables.chinese.txt"
            },
			"aoColumnDefs" : [
				{
					aTargets: [3],    // Column number which needs to be modified
					fnRender: function (o, v) {   // o, v contains the object and value for the column
						return '￥' +o.aData[3];
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell,
				},
				{
					aTargets: [6],    // Column number which needs to be modified
					fnRender: function (o, v) {   // o, v contains the object and value for the column
						if (o.aData[6] <= 0)
						{
							return "";
						} else {
							return '<a href="javascript:void(0);" onclick="opensms(' + 
								o.aData[6] + ', \''+ o.aData[2] +'\', \'' + o.aData[7] + '\');">发送短信</a>';
						}
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell,
				}
			],
            "aaSorting": [[1, "desc"]],
			"aoColumns": [
				{ sClass: "center", sWidth : '16%'},
				{ sClass: "center", sWidth : '16%' },
				{ sWidth : '25%'},
				{ bSortable: false, sClass: "center", sWidth : '10%' },
				{ sClass: "center", sWidth : '10%' },
				{ sClass: "center", sWidth : '8%' },
				{ bSortable: false, sWidth : '10%'}
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "order/retrieveorderlist"
		});
		
		
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

	})

	function opensms(targetid, smsreceiver, phonenum)
	{
		$("#receiver").html(smsreceiver);
		$("#smscontent").html(staticcontent);
		send_dialog = $( "#modal-send-deliver" ).dialog({
			modal: true,
			width: '500px',
			title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='icon-ok'></i> 发送短信</h4></div>",
			title_html: true,
			buttons: [ 
				{
					text: "确定",
					"class" : "btn btn-primary btn-mini",
					click: function() {
						var smscontent = $("#smscontent").val();

						if (smscontent == null || jQuery.trim(smscontent) == "")
						{
							$("#errorcancel").addClass("red");
							$("#errorcancel").css("display", "block");
							return false;
						} else {
							$("#errorcancel").css("display", "none");
						}
						
						sendsms(targetid, smsreceiver, smscontent, phonenum);							
					} 
				},
				{
					text: "取消",
					"class" : "btn btn-mini",
					click: function() {
						$( this ).dialog( "close" ); 
					} 
				}
			]
		});
	}

	function sendsms(targetid, smsreceiver, smscontent, phonenum)
	{
		$.ajax({
			type: "POST",
			url: rootUri + "order/sendsms",
			dataType: "json",
			data: {
				orderid: targetid,
				smscontent: smscontent,
				receiver: smsreceiver,
				phonenum: phonenum
			},
			success: onSendSuccess,
			error: onSendError
		});
	}

	function onSendSuccess(data) {
		if (data.indexOf("发送成功") >= 0) 
		{
			send_dialog.dialog( "close" ); 
			bootbox.alert("发送成功");
		} else {
			bootbox.alert(data);
		}
	}

	function onSendError(xhr) {
		bootbox.alert("发送失败， 请再试一下！");
	}

//]]>
</script>