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
							'<input type="checkbox" name="membercheck" class="ace" value="' + o.aData[0] + '" />' +
							'<span class="lbl"></span>' + 
							'</label>';
				},
				sClass: 'tableCell'    // Optional - class to be applied to this table cell
			}],
			"aoColumns": [
			  { "bSortable": false, "sClass": "center" },
			  null, null,null, null,
                {"bSortable":false, "bSearchable" : false}
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "member/retrievememberlist"
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
    $('#sel_member').attr('value', id);
    return false;
}

/**
 * 암호재설정확인창의 확인단추 누르는 사건
 */
function resetPassword() {
    var sel_member = $('#sel_member').attr('value');

    // fire off the request to /form.php
    request = $.ajax({
        url: rootUri + "member/resetpassword/" + sel_member,
        type: "post",
        success: function(){
            $("#result").html('Submitted successfully');
        },
        error:function(){
            alert("failure");
            $("#result").html('There is error while submit');
        }
    });
}

/**
 * 암호재설정창의 취소단추 누르는 사건
 */
function onCloseDlg()
{
    $("#r_name").html("");
    $("#l_name").html("");
}

function del_data()
{
    selected_id = "";
    $(':checkbox:checked').each(function() {
        if ($(this).attr('name') == 'membercheck')
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