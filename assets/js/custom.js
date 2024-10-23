/* This is your custom Javascript */
jQuery(document).ready(function($) {
    setTimeout(function(){
        jQuery(".login.login-action-lostpassword.bb-login #login>p.message>div").wrapInner('<h2></h2>');
    }, 0);
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="signup_email"]').attr('placeholder', 'Email Address');
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="signup_email_confirm"]').attr('placeholder', 'Confirm Email Address');
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="signup_password"]').attr('placeholder', 'Password');
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="signup_password_confirm"]').attr('placeholder', 'Confirm Password');
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="field_1"]').attr('placeholder', 'First Name');
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="field_2"]').attr('placeholder', 'Last Name');
    $('.bs-bp-container-reg #buddypress #signup-form.standard-form input[name="field_3"]').attr('placeholder', 'Nickname');
});