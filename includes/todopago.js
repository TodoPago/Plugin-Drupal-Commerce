$(document).ready(function(){
	$("label[for=edit-customer-profile-billing-commerce-customer-address-und-0-name-line]").parent("div").hide();
	$("label[for=edit-customer-profile-billing-commerce-customer-address-und-0-administrative-area]").parent("div").hide();

	$("#edit-continue").click(function(){
		$("#edit-customer-profile-billing-commerce-customer-address-und-0-name-line").val($("#edit-customer-profile-billing-todo-pago-nombre-und-0-value").val() + " "+ $("#edit-customer-profile-billing-todo-pago-apellido-und-0-value").val())
	  	//  alert($("#edit-customer-profile-billing-commerce-customer-address-und-0-name-line").val());
	   	// return false;
	});
});