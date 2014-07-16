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
            "aaSorting": [[1, "desc"]],
			"aoColumns": [
			  { "bSortable": false, "sClass": "center" },
			  { }, null, 
			  { bSortable: false}
			],
			"bProcessing" : true,
			"bServerSide" : true,
			"sPaginationType": "bootstrap",
			"sAjaxSource" : rootUri + "invitecode/retrievecodelist"
		});
		
		$('table th input:checkbox').on('click' , function(){
			var that = this;
			$(this).closest('table').find('tr > td:first-child input:checkbox')
			.each(function(){
				this.checked = that.checked;
				$(this).closest('tr').toggleClass('selected');
			});
		});

		jQuery('#codecount').keyup(function () {  
			this.value = this.value.replace(/[^0-9\.]/g,''); 
		});
	});

function generate_code()
{

	if($('#codecount').val() == "")
	{
		alert("请输入数字");
	} else {
		$("form").submit();
	}
    return false;
}

//]]>
</script>