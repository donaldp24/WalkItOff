<script type="text/javascript">
//<![CDATA[
	jQuery(function($) {
		var oTable1 = $('#sample-table-2').dataTable( {
            "oLanguage": {
                "sUrl": rootUri + "www/i18n/dataTables.chinese.txt"
            },
            "aaSorting": [[3, "desc"]], // Sort by showorder
			"aoColumnDefs" : [
			{
				aTargets: [0],    // Column number which needs to be modified
				fnRender: function (o, v) {   // o, v contains the object and value for the column
					return '<label>' +
							'<input type="checkbox" name="usercheck" class="ace" value="' + o.aData[0] + '" />' +
							'<span class="lbl"></span>' + 
							'</label>';
				},
				sClass: 'tableCell'    // Optional - class to be applied to this table cell
			}],
			"aoColumns": [
			  { "bSortable": false, "sClass": "center" },
			  null, null, null
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "onlineshop/retrieveshoplist"
		});
		
		
		$('table th input:checkbox').on('click' , function(){
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
			.each(function(){
				this.checked = that.checked;
				$(this).closest('tr').toggleClass('selected');
			});
				
		});
	});

/**
 * 암호재설정단추가 눌리우는 경우 hidden 변수에 값설정
 * @param id
 * @returns {boolean}
 */
function onResetPasswordClicked(id)
{
    $('#sel_user').attr('value', id);
    return false;
}

function del_data()
{
    selected_id = "";
    $(':checkbox:checked').each(function() {
        if ($(this).attr('name') == 'usercheck')
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

$(document).ready(function(){
    $("#btn_add").click(function(){
        window.location.href = rootUri + "onlineshop_add";
        //$("form").attr("action", rootUri + "user_add");
        //$("form").submit();
        return false;
    })
});

//]]>
</script>