<script type="text/javascript">
//<![CDATA[
var set_flag = false;

$(function() {	
	$("#cancel_btn").click(function(){
		window.location.href = rootUri + "goods";
		return false;
    })

	
	$("#firstlevel").change(function(e) {
        e.stopPropagation();
		$.ajax({
            type: 'POST',
            url: rootUri+"goods_add/getSecondLevel",
			data: {
				id: this.value
			},
            success: function(resp) {
				var data = ""
                if( resp != "" ) {
					var records = resp.split("@@@");
					for( var i=0; i<records.length; i++ ) {
						var values = records[i].split("|||");
						data += "<option value='"+values[0]+"'>"+values[1]+"</option>";
					}
				}
				$("#secondlevel").html( data );
            }
        });
    });
	/*
	$("#notPair").click(function(){
        $(".relativeGoods_block").dialog('close');
    });
	*/	
	
	$("#r_firstlevel").change(function(e) {
        e.stopPropagation();
		$.ajax({
            type: 'POST',
            url: rootUri+"goods_add/getSecondLevel",
			data: {
				id: this.value
			},
            success: function(resp) {
				var data = ""
                if( resp != "" ) {
					var records = resp.split("@@@");
					for( var i=0; i<records.length; i++ ) {
						var values = records[i].split("|||");
						data += "<option value='"+values[0]+"'>"+values[1]+"</option>";
					}
				}
				$("#r_secondlevel").html( data );
            }
        });
    });
	
	$('#r_search_btn').click(function(e) {
        $.ajax({
            type: 'POST',
            url: rootUri+"goods_add/getRelativeGoodsSearchResult",
			data: {
				firstlevel: $('#r_firstlevel').val(),
				secondlevel: $('#r_secondlevel').val(),
				search_name: $.trim( $('#r_search_name').val() )
			},
            success: function(resp) {
                var data = "";
                if( resp != "" ) {
					arr_data = resp.split("@@@");
                    for( i = 0; i < arr_data.length; i++)
                    {
                        value = arr_data[i].split("|||");
                        set_flag = false;
                        $("#r_name option").each(function()
                        {
                            if($(this).val() == value[0])
                                set_flag = true;
                        });
                        if(!set_flag)
                            data += "<option value='"+value[0]+"' name='"+value[2]+"'>"+value[1]+"</option>";
                    }
				}
				$("#l_name").html( data );
            }
        });
    });
    $("#insert_pro").click(function(){
       m_size = $("#txt_size").val();
       m_color = $("#txt_color").val();
       m_count = $("#txt_count").val();
       if(m_size!="" && m_color != "" && !isNaN(m_count) && m_count != "")
       {
           content = "<tr><td>"+m_size+"</td>";
           content += "<td>"+m_color+"</td>";
           content += "<td>"+m_count+"</td>";
           content += "<td><a href='javascript:void(0);' onclick='remove_td(this)'>删除</a></td></tr>";
           $("#property_tbl").html($("#property_tbl").html()+content);
           $("#txt_size").val("");
           $("#txt_color").val("");
           $("#txt_count").val("");
           setKind();
       }
    });	
    $("#send_right").click(function()
    {
        $('#l_name option:selected').each(function() {
			$("#r_name").append($(this));
			$("#l_name").remove($(this));
        });
    });
    $("#send_right_all").click(function()
    {
        if($("#l_name").html()!="")
        {
            $("#r_name").html($("#l_name").html());
            $("#l_name").html("");
        }
    });
    $("#send_left").click(function()
    {
        $('#r_name option:selected').each(function() {
			$("#l_name").append($(this));
			$("#r_name").remove($(this));
        });
    });
    $("#send_left_all").click(function()
    {
        if($("#r_name").html()!="")
        {
            $("#l_name").html($("#r_name").html());
            $("#r_name").html("");
        }
    });
    $(".ui-icon-zoomin").click(function(){
        var id = $(this).attr("name");
        var title = $(this).next().html();
        $.ajax({
            type: 'POST',
            url: rootUri+"goods_add/get_detail",
            data: {
                ID:id
            },
            success: function(resp) {
                if(resp != "")
                {
                    arr_img = resp.split(",");
                    str_detail_img = "";
                    for(i = 0; i < arr_img.length; i++)
                    {
                        str_detail_img += '<li class="ui-widget-content ui-corner-tr" style="height:120px">';
                        str_detail_img += "<img src='"+rootUri+"www/images/uploads/products/picture/"+arr_img[i]+"' width='100' height='120'>";
                        str_detail_img += '</li>';
                    }
                    $("#detail_gallery").html(str_detail_img);
                    $("#detail_gallery").dialog({
                        autoOpen: true,
                        resizable: true,
                        title: "产品 "+title+" 的 详情图片",
                        width:400,
                        height:500,
                        position:['center','center'],
                        modal: true});
                }
            }
        });
    });

	$('#showorder').ace_spinner({value:<?php if(isset($showorder)) echo $showorder; else echo '1'; ?>,min:1,max:999999999,step:1, icon_up:'icon-caret-up', icon_down:'icon-caret-down'});

    var button1 = $('#upload_btn1'), interval;
    new AjaxUpload('#upload_btn1', {
		action: rootUri + 'uploadimage/upload',
		onSubmit : function(file , ext){
			$('#loading_photo1').show();
			if (! (ext && /^(JPG|PNG|JPEG|GIF)$/.test( ext.toUpperCase() ))){
				// extensiones permitidas
				alert('Error: Only image files please','');        
				$('#loading_photo1').hide();
				return false;
			} 
		},
		onComplete: function(file, response){
			var resize_url = rootUri + 'uploadimage/resize';
			var f_name = response;
			$.ajax({
				url: resize_url,
				data: {
					"photo": f_name,
					"kinds" : "<?php echo GOODS_THUMB_PREFIX; ?>"
				},
				type: "post",
				success: function(message){
					$('#loading_photo1').hide();
						  
					var str_html = "<img src='" + rootUri + "www/images/uploads/products/image/" + f_name+ "' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
					str_html +=  "<img src='" + rootUri + "www/images/image_close.png' class='close_btn' onclick='removeMe1(\""+f_name+"\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
					$('#img1').html(str_html);    
					$('#img_name1').val(f_name);
				}
			}); 
		}  
    });
	
	var button2 = $('#upload_btn2'), interval;
    new AjaxUpload('#upload_btn2', {
		action: rootUri + 'uploadimage/upload',
		onSubmit : function(file , ext){
			$('#loading_photo2').show();
			if (! (ext && /^(JPG|PNG|JPEG|GIF)$/.test( ext.toUpperCase() ))){
				// extensiones permitidas
				alert('Error: Only image files please','');        
				$('#loading_photo1').hide();
				return false;
			} 
		},
		onComplete: function(file, response){
			var resize_url = rootUri + 'uploadimage/resize';        
			var f_name = response;
			$.ajax({
				url: resize_url,
				data: {
					"photo": f_name,
					"kinds" : "<?php echo implode(',', array(GOODS_THUMB_PREFIX, GOODS_EXHIBITION_PREFIX)); ?>"
				},
				type: "post",
				success: function(message){                
					$('#loading_photo2').hide();
					var pic_num = $("#img2").find('div').size();
					var pic_data = "<div style='float:left; padding:5px;'>";
                    pic_data += "<img src='" + rootUri + "www/images/uploads/products/image/" + f_name+ "' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
                    pic_data +=  "<img src='" + rootUri + "www/images/image_close.png' class='close_btn' onclick='removeMe2(this, \""+f_name+"\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
                    pic_data += "</div>";
					$('#img2').html( $('#img2').html()+pic_data );
					if( $('#img_name2').val() == "" )
						$('#img_name2').val( f_name );
					else
						$('#img_name2').val( $('#img_name2').val() + "," + f_name);
					
				}
			}); 
		}  
    });
    
    var button3 = $('#upload_btn3'), interval;
    new AjaxUpload('#upload_btn3', {
		action: rootUri + 'uploadimage/upload',
        onSubmit : function(file , ext){
            $('#loading_photo3').show();
            if (! (ext && /^(JPG|PNG|JPEG|GIF)$/.test( ext.toUpperCase() ))){
                // extensiones permitidas
                alert('Error: Only image files please','');        
                $('#loading_photo1').hide();
                return false;
            } 
        },
        onComplete: function(file, response){
			var resize_url = rootUri + 'uploadimage/resize';        
            var f_name = response;
            $.ajax({
                url: resize_url,
				data: {
					"photo": f_name,
					"kinds" : "<?php echo implode(',', array(GOODS_DETAIL_PREFIX)); ?>"
				},
                type: "post",
                success: function(message){                
                    $('#loading_photo3').hide();
                    var pic_num = $("#img2").find('div').size();
                    var pic_data = "<div style='float:left; padding:5px;'>";
                    pic_data += "<img src='" + rootUri + "www/images/uploads/products/image/" + f_name+ "' width='104px' height='104px' onmouseover='over_img(this)' onmouseout='out_img(this)' >";
                    pic_data +=  "<img src='" + rootUri + "www/images/image_close.png' class='close_btn' onclick='removeMe3(this, \""+f_name+"\")' onmouseover='over_close(this)' onmouseout='out_close(this)'>";
                    pic_data += "</div>";
                    $('#img3').html( $('#img3').html()+pic_data );
                    if( $('#img_name3').val() == "" )
                        $('#img_name3').val( f_name );
                    else
                        $('#img_name3').val( $('#img_name3').val() + "," + f_name);
                    
                }
            }); 
        }  
    });
	
	jQuery.validator.addMethod("order_price", function (value, element) {
		return this.optional(element) || isValidDecimal(value);	
	}, "请输入正确的价格");
	jQuery.validator.addMethod("price", function (value, element) {
		return this.optional(element) || isValidDecimal(value);
	}, "请输入正确的价格");

	$.validator.messages.required = "必须要填写";

	$('#validation-form').validate({
		errorElement: 'span',
		errorClass: 'help-inline',
		focusInvalid: false,
		rules: {
			name: {
				required: true
			},
			goodsno: {
				required: true,
				minlength: 5
			},
			order_price: {
				required: true,
				order_price: 'required'
			},
			price: {
				required: true,
				price: 'required'
			},
			pattern: 'required',
			showorder : 'required'
		},

		messages: {
			goodsno: {
				minlength: "请输入至少5个数字"
			},
			pattern: "请选择款式"
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

			alert("WWW");
			$('#check_save').attr("value", "submit");

			form.submit();
		},
		invalidHandler: function (form) {
		}
	});
});

function over_img(obj)
{
	clearTimeout(timeoutID);
	timer_flag = false;
	if(img_parent_div)
		$(img_parent_div).find(".close_btn").css('visibility', 'hidden');
	var obj_parent = $(obj).parent();    
	//$(obj_parent).find(".close_btn").show(); 
	$(obj_parent).find(".close_btn").css('visibility', 'visible');
}
var img_parent_div = null;
var timeoutID;
function out_img(obj)
{   
    img_parent_div = $(obj).parent(); 
    timeoutID = setTimeout("timerProc( )", 1000);
    timer_flag = true;
}
var timer_flag = false;
var close_flag = false;
function timerProc()
{
    if(!close_flag)
    {
        $(img_parent_div).find(".close_btn").css('visibility', 'hidden');
    }
    timer_flag = false;
}
function over_close(obj)
{
   close_flag = true; 
}
function out_close(obj)
{
    close_flag = false;
    
    if(!timer_flag)
        $(obj).css('visibility', 'hidden');
}
function removeMe1(f_name)
{
    var url;
    url = rootUri + "goods_add/remove_photo1";    
    $.ajax({
        url: url,
        data: "file_name=" + f_name,
        type: "post",
        success: function(message) {
            $('#img1').html("");
			$('#img_name1').val('');
        }
    });        
}
function removeMe2(obj, f_name)
{
	var pic_div = obj.parentNode;
    var url;
    url = rootUri + "goods_add/remove_photo2";    
    $.ajax({
        url: url,
        data: "file_name=" + f_name,
        type: "post",
        success: function(message) { 
        	$(pic_div).remove();
			var img_name_ary = $('#img_name2').val().split(',');
			var img_name_data = "";
			for( var i=0; i<img_name_ary.length; i++ ) {
				if( img_name_ary[i] == f_name )
					continue;
				else
					img_name_data += img_name_ary[i]+",";
			}
			img_name_data = img_name_data.substr(0, img_name_data.length-1);
			$('#img_name2').val( img_name_data );
        }
    });        
}
function removeMe3(obj, f_name)
{
    var pic_div = obj.parentNode;
    var url;
    url = rootUri + "goods_add/remove_photo3";    
    $.ajax({
        url: url,
        data: "file_name=" + f_name,
        type: "post",
        success: function(message) { 
            $(pic_div).remove();
            var img_name_ary = $('#img_name3').val().split(',');
            var img_name_data = "";
            for( var i=0; i<img_name_ary.length; i++ ) {
                if( img_name_ary[i] == f_name )
                    continue;
                else
                    img_name_data += img_name_ary[i]+",";
            }
            img_name_data = img_name_data.substr(0, img_name_data.length-1);
            $('#img_name3').val( img_name_data );
        }
    });        
}

function click_view(obj)
{
    var id = $(obj).attr("name");
    var title = $(obj).next().html();
    $.ajax({
        type: 'POST',
        url: rootUri+"goods_add/get_detail",
        data: {
            ID:id
        },
        success: function(resp) {
            if(resp != "")
            {
                arr_img = resp.split(",");
                str_detail_img = "";
                for(i = 0; i < arr_img.length; i++)
                {
                    str_detail_img += '<li class="ui-widget-content ui-corner-tr" style="height:120px">';
                    str_detail_img += "<img src='"+rootUri+"www/images/uploads/products/picture/"+arr_img[i]+"' width='100' height='120'>";
                    str_detail_img += '</li>';
                }
                $("#detail_gallery").html(str_detail_img);
                $("#detail_gallery").dialog({
                    autoOpen: true,
                    resizable: true,
                    title: "产品 "+title+" 的 详情图片",
                    width:400,
                    height:500,
                    position:['center','center'],
                    modal: true});
            }
        }
    });
}

function remove_td(obj)
{
    $(obj).parent().parent().remove();
    setKind();
}

function set_r_goods() {
	var sel_r_id = "";
    relative_img = "";
    $("#gallery").html("");
	$('#r_name option').each(function() {
        sel_r_id += $(this).val()+",";
        relative_img += '<div style="float:left; padding:5px;">';
        relative_img += "<img src='"+rootUri+"www/images/uploads/products/image/"+$(this).attr("name")+"' width='100' height='70'>";
        relative_img += '<h5 class="ui-widget-header"><span title="产品详情" class="ui-icon ui-icon-zoomin" name="'+$(this).val()+'" onclick="click_view(this)">&nbsp;</span><span>'+$(this).html()+'</span></h5>';
        relative_img += '</div>';
    });
	sel_r_id = sel_r_id.substr(0, sel_r_id.length-1);
	$('#sel_r_id').val( sel_r_id );
    $("#gallery").html(relative_img);
}


function setKind() {
	var tbody = $('#kind_tbl tbody').get(0);
	var kind = "";
	for( var i=0; i<tbody.rows.length; i++ ) {
		var item_data = "";
		for( var j=0; j<=2; j++ ) {
			if( $(tbody.rows[i].cells[j].childNodes[0]).is('input') ) {
				item_data += $.trim( tbody.rows[i].cells[j].childNodes[0].value ) + "|||";
			}else {
				item_data += $.trim( tbody.rows[i].cells[j].innerHTML ) + "|||";
			}
		}
		if( item_data == "|||||||||" ) continue;
		item_data = item_data.substr(0, item_data.length-3);
		kind += item_data + "@@@";
	}
	kind = kind.substr(0, kind.length-3);
	$('#kind').val( kind );
}

function onCloseDlg()
{
	$("#r_name").html("");
	$("#l_name").html("");
}

function isValidDecimal(value)
{
//	var regexTest = /^\d{0,8}(\.\d{0,2})?$/;
	var regexTest = /^\d+(?:\.\d\d?)?$/;
	var ok = regexTest.test(value);
	return ok;
}

//]]>
</script>