function validation_registration()
{
	var first_name = jQuery("#first_name").val();
	var last_name = jQuery("#last_name").val();
	var address = jQuery("#address").val();
	var city = jQuery("#city").val();
	var state = jQuery("#state").val();
	var postcode = jQuery("#postcode").val();
	var match_postcode = jQuery("#match_postcode").val(); 
	var country = jQuery("#country").val();
	var phone = jQuery("#phone").val();
	var mobile_phone = jQuery("#mobile_phone").val();
	var fax = jQuery("#fax").val();
	var email = jQuery("#email").val();
	var password = jQuery("#password").val();
	var confirm_password = jQuery("#confirm_password").val();
	var message = jQuery("#message").val();
	
	
	if(first_name == "")
	{
		alert("First Name must be filled!");
		jQuery("#first_name").focus();
		return false;
	}
	else if(last_name == "")
	{
		alert("Last Name must be filled!");
		jQuery("#last_name").focus();
		return false;
	}
	else if(address == "")
	{
		alert("Address must be filled!");
		jQuery("#address").focus();
		return false;
	}
	else if(city == "")
	{
		alert("Suburb must be filled!");
		jQuery("#city").focus();
		return false;
	}
	else if(state == "")
	{
		alert("State must be filled!");
		jQuery("#state").focus();
		return false;
	}
	else if(postcode == "")
	{
		alert("Postcode must be filled!");
		jQuery("#postcode").focus();
		return false;
	}
	else if(country == "Australia" && match_postcode != "match")
	{
		alert("Invalid postcode!");
		jQuery("#postcode").focus();
		return false;
	}
	else if(phone == "")
	{
		alert("Phone must be filled!");
		jQuery("#phone").focus();
		return false;
	}
	else if(email == "")
	{
		alert("Email must be filled!");
		jQuery("#email").focus();
		return false;
	}
	else if(password == "")
	{
		alert("Password must be filled!");
		jQuery("#password").focus();
		return false;
	}
	else if(password != confirm_password)
	{
		alert("Confirm Password not match!");
		jQuery("#password").focus();
		return false;
	}
}

function validation_edit_profile()
{
	var first_name = jQuery("#first_name").val();
	var last_name = jQuery("#last_name").val();
	var mailing_address_line1 = jQuery("#mailing_address_line1").val();
	var mailing_city = jQuery("#mailing_city").val();
	var mailing_state = jQuery("#mailing_state").val();
	var mailing_postcode = jQuery("#mailing_postcode").val();
	var delivery_contact_name = jQuery("#delivery_contact_name").val();
	var delivery_address_line1 = jQuery("#delivery_address_line1").val();
	var delivery_city = jQuery("#delivery_city").val();
	var delivery_state = jQuery("#delivery_state").val();
	var delivery_postcode = jQuery("#delivery_postcode").val();
	var phone = jQuery("#phone").val();
	var mobile_phone = jQuery("#mobile_phone").val();
	var fax = jQuery("#fax").val();
	var email = jQuery("#email").val();
	var password = jQuery("#password").val();
	var confirm_password = jQuery("#confirm_password").val();
	var message = jQuery("#message").val();
	
	
	if(first_name == "")
	{
		alert("First Name must be filled!");
		jQuery("#first_name").focus();
		return false;
	}
	else if(last_name == "")
	{
		alert("Last Name must be filled!");
		jQuery("#last_name").focus();
		return false;
	}
	else if(phone == "")
	{
		alert("Phone must be filled!");
		jQuery("#phone").focus();
		return false;
	}
	else if(email == "")
	{
		alert("Email must be filled!");
		jQuery("#email").focus();
		return false;
	}
	else if(password != "" && password != confirm_password)
	{
		alert("Confirm Password not match!");
		jQuery("#password").focus();
		return false;
	}
	else if(mailing_address_line1 == "")
	{
		alert("Address must be filled!");
		jQuery("#mailing_address_line1").focus();
		return false;
	}
	else if(mailing_city == "")
	{
		alert("Suburb must be filled!");
		jQuery("#mailing_city").focus();
		return false;
	}
	else if(mailing_state == "")
	{
		alert("State must be filled!");
		jQuery("#mailing_state").focus();
		return false;
	}
	else if(mailing_postcode == "")
	{
		alert("Postcode must be filled!");
		jQuery("#mailing_postcode").focus();
		return false;
	}
	else if(delivery_contact_name == "")
	{
		alert("Contact Name must be filled!");
		jQuery("#delivery_contact_name").focus();
		return false;
	}
	else if(delivery_address_line1 == "")
	{
		alert("Delivery Address must be filled!");
		jQuery("#delivery_address_line1").focus();
		return false;
	}
	else if(delivery_city == "")
	{
		alert("Delivery Suburb must be filled!");
		jQuery("#delivery_city").focus();
		return false;
	}
	else if(delivery_state == "")
	{
		alert("Delivery State must be filled!");
		jQuery("#delivery_state").focus();
		return false;
	}
	else if(delivery_postcode == "")
	{
		alert("Delivery Postcode must be filled!");
		jQuery("#delivery_postcode").focus();
		return false;
	}
	else if (!validation_credit_card())
	{
		return false;
	}
}

function validation_credit_card()
{
	if(jQuery("#EWAY_CARDNUMBER").length)
	{
		var EWAY_CARDNAME = jQuery("#EWAY_CARDNAME").val();
		var EWAY_CARDNUMBER = jQuery("#EWAY_CARDNUMBER").val();
		//var EWAY_CARDCVN = jQuery("#EWAY_CARDCVN").val();
		
		if(EWAY_CARDNAME == "")
		{
			alert("Card Holder must be filled!");
			jQuery("#EWAY_CARDNAME").focus();
			return false;
		}
		else if(EWAY_CARDNUMBER == "")
		{
			alert("Card Number must be filled!");
			jQuery("#EWAY_CARDNUMBER").focus();
			return false;
		}
		else if(!checkLuhn(EWAY_CARDNUMBER))
		{
			alert("Invalid Card Number!");
			jQuery("#EWAY_CARDNUMBER").focus();
			return false;
		}
		/*
		else if(EWAY_CARDCVN == "")
		{
			alert("CVN must be filled!");
			jQuery("#EWAY_CARDCVN").focus();
			return false;
		}
		*/
	}

	return true
}

function validation_submit_transaction() {
	var delivery_type = jQuery("input[name='delivery_type']:checked").val();
	if (delivery_type == "default") {
		return true;
	} else if (delivery_type == "new") {
		if (jQuery("#new_contact_name").val() == "") {
			alert("Contact Name must be filled"); return false;			
		} else if (jQuery("#new_delivery_address_line1").val() == "") {
			alert("Address Line 1 must be filled"); return false;
		} else {
			return true;
		}
	}
}

function checkLuhn(input) 
{ 
	var sum = 0; 
	var numdigits = input.length; 
	var parity = numdigits % 2; 
	for(var i=0; i < numdigits; i++) 
	{ 
		var digit = parseInt(input.charAt(i)) 
		if(i % 2 == parity) 
		digit *= 2; 
		if(digit > 9) 
		digit -= 9; 
		sum += digit; 
	} 
	return (sum % 10) == 0; 
}