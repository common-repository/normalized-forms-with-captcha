=== Normalized Forms with Captcha ===
Contributors: Trigve Hagen
Donate link: http://www.globalwebmethods.com/
Tags: contact form, registration form, login form, mail fix, captcha
Requires at least: 4.3.1
Tested up to: 4.3.1
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Responsive Contact, Login & Register Forms with Captcha. Redirection of Register and Login links to a theme based Register page.

== Description ==

This plugin creates a Responsive Login, Register and Contact us Form with captcha. These forms are Transparent and can be placed via shortcode anywhere on any theme you like. It corrects the Contact Form not sending emails on finicky servers, robot spam sign ups via the registration and contact forms, and redirecting of the Login and Register links to complete a "Normalizing" of the Wordpress Installation. To use create a Wordpress page with the slug register. Then place these codes where you want the forms. Login = [gwb_login_form] Register = [gwb_register_form] and Contact = [gwb_contact_form]. The plugin defaults to the administrators email but you can fill in the $to_args array with more emails for multiple mail recipients. You can also change the name of the page which at the moment the slug is /register . All options must be hard coded in php.

== Installation ==

= Manual Installation =

1. Download the plugin and extract the plugin zip file.
2. Upload 'normalized-forms-with-captcha' folder to '/wp-content/plugins/' directory of your website.
3. Go to 'Plugins > Installed Plugins' page inside your WP admin dashboard.
4. Find 'Normalized Forms with Captcha' and click 'activate'.
5. Go to the Pages link on you dashboard and create a page called Register and make sure that the slug in the url says register. http://www.globalwebmethods.com/testsite/**register**/
6. Go to the Register page you made and enter the shotcodes to place a Login and A Register form on the page. Login = [gwb_login_form] Register = [gwb_register_form].
7. Go to your Contact page and enter the shotcodes to place a Contact form on the page. Contact = [gwb_contact_form].

= Automatic Installation =

1. Go to 'Plugins > Add New' page inside your WP admin dashboard.
2. Enter 'Normalized Forms with Captcha' to the search box and press enter.
3. Install and activate the plugin.
5. Go to the Pages link on you dashboard and create a page called Register and make sure that the slug in the url says register. http://www.globalwebmethods.com/testsite/**register**/
6. Go to the Register page you made and enter the shotcodes to place a Login and A Register form on the page. Login = [gwb_login_form] Register = [gwb_register_form].
7. Go to your Contact page and enter the shotcodes to place a Contact form on the page. Contact = [gwb_contact_form].

== Frequently Asked Questions ==

- Is there a premium version and what added functionality comes with it? -

There will be a premium version that uses picture captcha for people that do not speak english, panels in the back to allow people who do not code to update the plugin functionality through forms, and the ability to assign a Login page and a Registration page. There will also be the ability to assign different slugs to the url so you can name the page anything you like.

- How much will you charge for the premium version? -
The premium version will be $49.98.

== Screenshots ==

1. This picture is the what the register page looks like in a fresh wordpress install with no other plugins. You can adjust the placement and styling of the header and other elements on the page. Its good to start with this plugin to stop the robots from finding your registration page and filling your database with spam sign ups.
2. This picture is the what the register page looks like in a wordpress install that uses the fitnesszone theme. I made a few css modifications to the forgot password link. This plugin should work on most installations with most themes and most plugins. I honestly only tested it on two sites at the moment and so the comments Requires at least: 4.3.1 and Tested up to: 4.3.1 reflects this.
3. This picture is the what the contact page looks like in a fresh wordpress install with no other plugins. You can add an infinite number of recipients to the wp_mail function.
4. This picture is the what the contact page looks like in a wordpress install that uses the fitnesszone theme.
5. This picture is a picture of the code you need to adjust to change Captcha colors. It uses three numbers representing the strength of Red Green and Blue 0, 0, 0 being Black and 255, 255, 255 being white. Any color you want can be found by mixing the right numbers.
6. This picture is a picture of the code you need to adjust to change the number of recipients that will receive an email when someone uses your contact form. If you have multiple sales people who answer contact form submissions you can send them each an email.

== Changelog ==

= 1.0 =
* Initial release.

== Upgrade Notice ==

None yet.