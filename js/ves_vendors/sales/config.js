Event.observe(window,'load',function(){
	if($('vendors_config_mode')){
		$('vendors_config_mode').observe('change',function(){
			vendorsSalesToggleMarketplaceMode(this.value);
		});
		vendorsSalesToggleMarketplaceMode($('vendors_config_mode').value);
	}
});
function vendorsSalesToggleMarketplaceMode(value){
	if(value == 'advanced' || value== 'advanced_x'){
		$('vendors_sales_view_order_comments').up(1).show();
		$('vendors_sales_send_order_comments').up(1).show();
		$('vendors_sales_view_creditmemo').up(1).show();
		$('vendors_sales_view_invoices').up(1).show();
		$('vendors_sales_view_payment_info').up(1).show();
		
		$('vendors_sales_view_addresses').up(1).hide();
	}else{
		$('vendors_sales_view_order_comments').up(1).hide();
		$('vendors_sales_send_order_comments').up(1).hide();
		$('vendors_sales_view_creditmemo').up(1).hide();
		$('vendors_sales_view_invoices').up(1).hide();
		$('vendors_sales_view_payment_info').up(1).hide();
		
		$('vendors_sales_view_addresses').up(1).show();
	}
}