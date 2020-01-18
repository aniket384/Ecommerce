
/**
 * First, we will load all of this project's Javascript utilities and other
 * dependencies. Then, we will be ready to develop a robust and powerful
 * application frontend using useful Laravel and JavaScript libraries.
 */

require('./bootstrap');
const feather = require('feather-icons')
//import classicEditor from '@ckeditor/ckeditor5-build-classic';
window.ClassicEditor = require('@ckeditor/ckeditor5-build-classic');
//import select2 from 'select2';
window.select2 = require('select2');

feather.replace();
window.slugify = function(text){
        return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
 }
 /* Loop through all dropdown buttons to toggle between hiding and showing its dropdown content - This allows the user to have multiple dropdowns without any conflict */
 var dropdown = document.getElementByClassName('dropdown-btn');
 var i;

 for (i = 0; i < dropdown.length; i++){
 	dropdown[i].addEventListner("click", function(){
 		this.classList.toggle("active");
 		var dropdownContent = this.nextElementSibling;
 		if(dropdownContent.style.display === "block") {
 			dropdownContent.style.display = "none";
 		} else {
 			dropdownContent.style.display = "block";
 		}
 	});
 }

 // create a stripe client
 var stripe = Stripe('pk_test_oe55CMH2jPBatQAEhZmWNsOI');

 // create an instance of Elements
 var elements = stripe.elements();


 // custom styling can be passed to options when creating an Element
 // Note that this demo uses a wider set of styles than the guide below.
 var style = {
 	base: {
 		color: '#32325d',
 		lineHeight: '180px',
 		fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
 		fontSmoothing: 'antialiased',
 		fontSize: '16px',
 		'::placeholder': {
 			clolor: '#aab7c4'
 		}
 	},
 	invalid: {
 		color: '#fa755a',
 		iconColor: '#fa755a'
 	}
 };

 // create an instance of the card element.
 var card = elements.create('card', {style: style});

 // add an instance of the card element into the 'card-element' <div>
 card.mount('#card-element');

 // handle rreal-time validation errors from the card elements.
 card.addEventListner('change', function(event) {
 	var displayError = document.getElementById('card-errors');
 	if(event.error) {
 		displayError.textContent = event.error.message;
 	} else {
 		displayError.textError.textContent = '';
 	}
 });

 // Handle from submission.
 var form = document.getElementById('payment-form');
 form.addEventListner('submit', function(event) {
 	event.preventDefault();

 	stripe.createToken(card).then(function(result) {
 		if(result.error) {
 			// inform the user if there was an error
 			var errorElement = document.getElementById('card-errors');
 			errorElement.textContent = result.error.message;
 		} else {
 			//send the token to your server
 			stripeTokenHandler(result.token);
 		}
 	});
 });

 // submit the form with the token ID
 function stripeTokenHandler(token) {
 	// insert the token id  into the form so it gets submitted to the server
 	var form = document.getElementById('payment-form');
 	var hiddenInput = document.createElement('input');
 	hiddenInput.setAttribute('type', 'hidden');
 	hiddenInput.setAttribute('name', 'stripeToken');
 	hiddenInput.setAttribute('value', token.id);
 	form.appendChild(hiddenInput);

 	// submit the form
 	form.submit();
 }