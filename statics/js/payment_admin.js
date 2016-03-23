// JavaScript Document
;(function(app, $) {
	app.payment_list = {
		/* 支付方式编辑form提交 */
		editFormSubmit : function() {
			var $form = $('form[name="editForm"]');
			/* 给表单加入submit事件 */
			var option = {
				rules:{
					pay_name : {required:true,minlength:3},
					pay_desc : {required:true,minlength:6},
				},
				messages:{
					pay_name : {
						required : "请输入支付名称",
						minlength : "支付名称长度不能小于3"
					},
					pay_desc : {
						required : "请输入支付描述",
						minlength : "支付描述长度不能小于6"
					}
				},	
				submitHandler : function() {
					$form.ajaxSubmit({
						dataType : "json",
						success : function(data) {
							if(data.state == "success") {
								if(data.refresh_url!=undefined) {
									var pjaxurl = data.refresh_url;
									ecjia.pjax(pjaxurl, function(){
										ecjia.admin.showmessage(data);
									});
								}else{
									ecjia.admin.showmessage(data);
								}
							} else {
								ecjia.admin.showmessage(data); 
							}	
						}
					});
				}
			}
			var options = $.extend(ecjia.admin.defaultOptions.validate, option);
			$form.validate(options);
		},
		initList : function(){
			/* 配送方式关闭与启用 */
			$('.switch').on('click', function(e){
				$.ajax({
					type: "POST",
					url: $(this).attr('data-url'),
					data: '',
					dataType: "json",
					success: function(data){
						if(data.state == "success") {
							if(data.refresh_url!=undefined) {
								var pjaxurl = data.refresh_url;
								ecjia.pjax(pjaxurl, function(){
									ecjia.admin.showmessage(data);
								});
							}else{
								ecjia.admin.showmessage(data);
							}
						} else {
							ecjia.admin.showmessage(data); 
						}				            	 	
					}
				});
			});
		},
	};

})(ecjia.admin, jQuery);

//end