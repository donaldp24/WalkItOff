<script type="text/javascript">
//<![CDATA[
var set_flag = false;

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "member";
		return false;
    });

	$(".chosen-select").chosen(); 

	$.validator.messages.required = "必须要填写";

	$('#validation-form').validate({
		errorElement: 'span',
		errorClass: 'help-inline',
		focusInvalid: false,
		rules: {
			memberid: {
				required: true
			},
            membername: {
				required: true
			},
            sex: {
				required: true
			},
            phonenum: {
                required: true
            }
        },

		invalidHandler: function (event, validator) { //display error alert on form submit   
			$('.alert-error', $('.login-form')).show();
		},

		highlight: function (e) {
			$(e).closest('.control-group').removeClass('info').addClass('error');
		},

		success: function (e) {
			$(e).closest('.control-group').removeClass('error').addClass('info');
			$(e).remove();
		},

		errorPlacement: function (error, element) {
			if(element.is(':checkbox') || element.is(':radio')) {
				var controls = element.closest('.controls');
				if(controls.find(':checkbox,:radio').length > 1) controls.append(error);
				else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
			}
			else if(element.is('.select2')) {
				error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
			}
			else if(element.is('.chosen-select')) {
				error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
			}
			else error.insertAfter(element);
		},

		submitHandler: function (form) {
			if(!form.valid()) return false;

			form.submit();
		},
		invalidHandler: function (form) {
		}
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
//]]>
</script>