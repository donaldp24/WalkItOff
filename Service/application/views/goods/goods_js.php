<script type="text/javascript">
//<![CDATA[
	jQuery(function($) {
		$("#btn_add").click(function(){
			window.location.href = rootUri + "goods_add";
			//$("form").attr("action", rootUri + "user_add");
			//$("form").submit();
			return false;
		})

		var oTable1 = $('#sample-table-2').dataTable( {
            "oLanguage": {
                "sUrl": rootUri + "www/i18n/dataTables.chinese.txt"
            },
			"aoColumnDefs" : [
				{
					aTargets: [0],    // Column number which needs to be modified
					fnRender: function (o, v) {   // o, v contains the object and value for the column
						return '<label>' +
								'<input type="checkbox" name="goodscheck" class="ace" value="' + o.aData[0] + '" />' + 
								'<span class="lbl"></span>' + 
								'</label>';
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell
				},
				{
					aTargets: [4],    // Column number which needs to be modified
					fnRender: function (o, v) {   // o, v contains the object and value for the column
						return o.aData[4] == 0 ? "新款" : "经典";
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell
				},
				{
					aTargets: [6],    // Column number which needs to be modified
					fnRender: function (o, v) {   // o, v contains the object and value for the column
						return '￥' +o.aData[6];
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell
				},
				{
					aTargets: [7],    // Column number which needs to be modified
					fnRender: function (o, v) {   // o, v contains the object and value for the column
						return '￥' +o.aData[7];
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell
				}
			],
			"aoColumns": [
			  { "bSortable": false, "sClass": "center" },
			  null, null,null, null, null, null, null,null
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "goods/retrievegoodslist"
		});
		
		
		$('table th input:checkbox').on('click' , function(){
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
			.each(function(){
				this.checked = that.checked;
				$(this).closest('tr').toggleClass('selected');
			});
				
		});
	})

	function onCloseDlg()
	{
	}

	function del_data()
	{
		selected_id = "";
		$(':checkbox:checked').each(function() {
			if ($(this).attr('name') == 'goodscheck')
				selected_id += $(this).attr('value') + ",";
		});
		if(selected_id != "")
		{
			$("#del_ids").val(selected_id);
			$("form").submit();
		}
		else
		{
			//
		}
		return false;
	}

//]]>
</script>