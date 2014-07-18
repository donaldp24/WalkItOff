<script type="text/javascript">
//<![CDATA[
var send_dialog = null;
var change_dialog = null;
	jQuery(function($) {

		//override dialog's title function to allow for HTML titles
		$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
			_title: function(title) {
				var $title = this.options.title || '&nbsp;'
				if( ("title_html" in this.options) && this.options.title_html == true )
					title.html($title);
				else title.text($title);
			}
		}));
	
	
		$( "#btn-send-deliver" ).on('click', function(e) {
			e.preventDefault();
	
			send_dialog = $( "#modal-send-deliver" ).dialog({
				modal: true,
				title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='icon-ok'></i> 设置发货单号</h4></div>",
				title_html: true,
				buttons: [ 
					{
						text: "确定",
						"class" : "btn btn-primary btn-mini",
						click: function() {
							var sendno = $("#sendno").val();
							if (sendno == null || jQuery.trim(sendno) == "")
							{
								$("#errorsendno").addClass("red");
								$("#errorsendno").css("display", "block");
								return false;
							} else {
								$("#errorsendno").css("display", "none");
							}
							
							send_deliver(sendno);							
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
		});

		$("#btn-complete-deliver").on(ace.click_event, function() {
			bootbox.confirm("您确定要完成订单吗？", "取消", "确定", function(result) {
				if(result) {
					complete_deliver();
				}
			});
		});

		$( "#btn-cancel-deliver" ).on('click', function(e) {
			e.preventDefault();
	
			cancel_dialog = $( "#modal-cancel-deliver" ).dialog({
				modal: true,
				title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='icon-ok'></i> 订单取消</h4></div>",
				title_html: true,
				buttons: [ 
					{
						text: "确定",
						"class" : "btn btn-primary btn-mini",
						click: function() {
							var cancelreason = $("#cancelreason").val();
							if (cancelreason == null || jQuery.trim(cancelreason) == "")
							{
								$("#errorcancel").addClass("red");
								$("#errorcancel").css("display", "block");
								return false;
							} else {
								$("#errorcancel").css("display", "none");
							}
							
							cancel_deliver(cancelreason);							
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
		});

		$( "#btn-change-receiver" ).on('click', function(e) {
			e.preventDefault();
	
			$("#edit_receiver").attr("value", $("#receiver").html());
			$("#edit_phonenum").attr("value", $("#phonenum").html());
			$("#edit_postaddr").attr("value", $("#postaddr").html());

			change_dialog = $( "#modal-change-receiver" ).dialog({
				modal: true,
				width: "800",
				height: "600",
				title: "<div class='widget-header widget-header-small'><h4 class='smaller'><i class='icon-ok'></i> 收货人信息</h4></div>",
				title_html: true,
				open : function() {
					//$('#addrprovince').trigger('chosen:updated');
					$(".chosen-select").chosen(); 

				},
				buttons: [ 
					{
						text: "确定",
						"class" : "btn btn-primary btn-mini",
						click: function() {
							var phonenum = $("#edit_phonenum").val();
							var postaddr = $("#edit_postaddr").val();
							var receiver = $("#edit_receiver").val();
							var addrprovince = $("#addrprovince").val();
							var addrcity = $("#addrcity").val();
							var addrarea = $("#addrarea").val();
							var addrstreet = $("#addrstreet").val();
							if (jQuery.trim(phonenum) == "" || jQuery.trim(postaddr) == "")
							{
								$("#errorreceiver").addClass("red");
								$("#errorreceiver").css("display", "block");
								return false;
							} else {
								$("#errorreceiver").css("display", "none");
							}
							
							change_receiver(receiver, phonenum, postaddr, addrprovince, addrcity, addrarea, addrstreet);
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
		});

		$("#addrprovince").change(function(e) {
			e.stopPropagation();
			$.ajax({
				type: 'POST',
				url: rootUri+"member_add/getcities",
				dataType: 'json',
				data: {
					province: this.value
				},
				success: function(resp) {
					var data = ""
					if( resp != null && resp.length > 0) {
						for( var i=0; i<resp.length; i++ ) {
							data += "<option value='"+resp[i]['regionname']+"'>"+resp[i]['regionname']+"</option>";
						}
					}
					$("#addrcity").html( data );
					$("#addrarea").html( "");
					if (resp.length == 1)
					{
						getareas($("#addrcity").val());
					}
					$('#addrcity').trigger('chosen:updated');
					$('#addrarea').trigger('chosen:updated');
				}
			});
		});

		$("#addrcity").change(function(e) {
			e.stopPropagation();
			getareas(this.value);
		});

		function getareas(city)
		{
			$.ajax({
				type: 'POST',
				url: rootUri+"member_add/getareas",
				dataType: 'json',
				data: {
					city: city
				},
				success: function(resp) {
					var data = ""
					if( resp != null && resp.length > 0) {
						for( var i=0; i<resp.length; i++ ) {
							data += "<option value='"+resp[i]['regionname']+"'>"+resp[i]['regionname']+"</option>";
						}
					}
					$("#addrarea").html( data );
					$('#addrarea').trigger('chosen:updated');
				}
			});
		}


	});


	function change_receiver(receiver, phonenum, postaddr, addrprovince, addrcity, addrarea, addrstreet)
	{
		$("#errorreceiver").html("提交中...");
		$("#errorreceiver").removeClass("red");
		$("#errorreceiver").css("display", "block");

		$.ajax({
			type: "POST",
			url: rootUri + "orderdetail/changereceiver",
			dataType: "json",
			data: {
				posid: $("#memposid").val(),
				receiver: receiver,
				phonenum: phonenum,
				postaddr: postaddr,
				addrprovince: addrprovince,
				addrcity: addrcity,
				addrarea: addrarea,
				addrstreet: addrstreet
			},
			success: onChangeReceiverSuccess,
			error: onChangeReceiverError
		});

		return false;
	}

	function onChangeReceiverSuccess(data) {
		if (data > 0) 
		{
			change_dialog.dialog( "close" ); 
			$("#receiver").html($("#edit_receiver").val());
			$("#phonenum").html($("#edit_phonenum").val());
			$("#postaddr").html($("#edit_postaddr").val());
			$("#show_addrprovince").html($("#addrprovince").val());
			$("#show_addrcity").html($("#addrcity").val());
			$("#show_addrarea").html($("#addrarea").val());
			$("#show_addrstreet").html($("#addrstreet").val());
			bootbox.alert("提交成功");
		}
	}

	function onChangeReceiverError(xhr) {
		$("#errorreceiver").html("操作失败...");
		$("#errorreceiver").addClass("red");
	}


	function cancel_deliver(cancelreason)
	{
		$("#errorcancel").html("提交中...");
		$("#errorcancel").removeClass("red");
		$("#errorcancel").css("display", "block");

		$.ajax({
			type: "POST",
			url: rootUri + "orderdetail/Cancelorder",
			dataType: "json",
			data: {
				orderid: $("#orderuid").val(),
				cancelreason: cancelreason
			},
			success: onCancelSubmitSuccess,
			error: onCancelSubmitError
		});

		return false;
	}

	function onCancelSubmitSuccess(data) {
		if (data > 0) 
		{
			cancel_dialog.dialog( "close" ); 
			$("#btn-send-deliver").css("display", "none");
			$("#btn-complete-deliver").css("display", "none");
			$("#btn-cancel-deliver").css("display", "none");
			$("#reason-div").css("display", "block");
			$("#reason").html($("#cancelreason").val());
			$("#order_status").html('<span class="label label-large arrowed"><s>已取消</s></span>');
			bootbox.alert("提交成功");
		}
	}

	function onCancelSubmitError(xhr) {
		$("#errorcancel").html("操作失败...");
		$("#errorcancel").addClass("red");
	}

	function send_deliver(sendno)
	{
		$("#errorsendno").html("提交中...");
		$("#errorsendno").removeClass("red");
		$("#errorsendno").css("display", "block");

		$.ajax({
			type: "POST",
			url: rootUri + "orderdetail/UpdateSendingNo",
			dataType: "json",
			data: {
				orderid: $("#orderuid").val(),
				sendno: sendno
			},
			success: onSendNoSubmitSuccess,
			error: onSubmitError
		});

		return false;
		//bootbox.alert("You are sure!");
	}

	function onSendNoSubmitSuccess(data) {
		if (data > 0) 
		{
			send_dialog.dialog( "close" ); 
			$("#sendingno").html($("#sendno").val());
			$("#btn-send-deliver").css("display", "none");
			$("#btn-complete-deliver").css("display", "block");
			$("#order_status").html('<span class="label label-large label-info arrowed-right arrowed-in">已发货</span>');
			bootbox.alert("提交成功");
		}
	}

	function onSubmitError(xhr) {
		$("#errorsendno").html("操作失败...");
		$("#errorsendno").addClass("red");
	}

	function complete_deliver()
	{
		$("#btn-complete-deliver").html("提交中...");
		$("#btn-complete-deliver").addClass("disabled");
		$.ajax({
			type: "POST",
			url: rootUri + "orderdetail/CompleteOrder",
			dataType: "json",
			data: {
				orderid: $("#orderuid").val()
			},
			success: onCompleteSubmitSuccess,
			error: onCompleteSubmitError
		});
	}

	function onCompleteSubmitSuccess(data) {
		if (data > 0) 
		{
			$("#order_status").html('<span class="label label-large label-success arrowed-in arrowed-in-right">已完成</span>');
			$("#btn-complete-deliver").html("操作成功");
			$("#btn-complete-deliver").off("click");
			$("#btn-cancel-deliver").css("display", "none");
		}
	}

	function onCompleteSubmitError(xhr) {
		$("#errorsendno").addClass("red");
		$("#btn-complete-deliver").html("操作失败,请再试一下->");
		$("#btn-complete-deliver").removeClass("disabled");
	}

//]]>
</script>