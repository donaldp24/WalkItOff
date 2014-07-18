<script type="text/javascript">
//<![CDATA[
	jQuery(function($) {
		var oTable1 = $('#sample-table-2').dataTable( {
            "oLanguage": {
                "sUrl": rootUri + "www/i18n/dataTables.chinese.txt"
            },
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
            "aaSorting": [[2, "desc"]],
			"aoColumns": [
			  { "bSortable": false, "sClass": "center" },
			  null, null//, null
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "message/retrievemessagelist"
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
 * 공개 혹은 비공개조작을 눌렀을때의 처리
 */
function onCommand(uid, val) {

    // fire off the request to /form.php
    request = $.ajax({
        url: rootUri + "message/changeStatus",
        type: "post",
        data: {"uid":uid, "val":val },
        success: function(){
            $("#sample-table-2").dataTable().fnDraw();
            $("#result").html('Submitted successfully');
        },
        error:function(xhml){
            alert(xhml);
            alert("failure");
            $("#result").html('There is error while submit');
        }
    });
}

function onPrivate(uid)
{
    onCommand(uid, 0);
    return false;
}

function onPublic(uid)
{
    onCommand(uid, 1);
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
        window.location.href = rootUri + "message_add";
        //$("form").attr("action", rootUri + "user_add");
        //$("form").submit();
        return false;
    })
});

//]]>
</script>