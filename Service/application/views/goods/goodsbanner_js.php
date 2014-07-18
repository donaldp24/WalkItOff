<script type="text/javascript">
//<![CDATA[
	jQuery(function($) {

		$("#btn_add").click(function(){
			window.location.href = rootUri + "goodsbanner_add";
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
								'<input type="checkbox" name="bannercheck" class="ace" value="' + o.aData[0] + '" />' + 
								'<span class="lbl"></span>' + 
								'</label>';
					},
					sClass: 'tableCell'    // Optional - class to be applied to this table cell
				}
			],
            "aaSorting": [[3, "desc"]],
			"aoColumns": [
			  { "bSortable": false, "sClass": "center" },
			  null, null,null
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "goodsbanner/retrievegoodsbannerlist"
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
        if ($(this).attr('name') == 'bannercheck')
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