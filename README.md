# LegitCar Verification API WordPress Plugin (LEGITCAR API Client)

Official repo for the 'LEGITCAR API Client' WordPress plugin, for WordPress users who need to consume the LegitCar verification API (https://legitcar.ng/api-docs).

## Getting Started

### Prerequisites

* A WordPress install (Obviously).
* Contact the LegitCar team for access to the API.

### Installation

* Copy the plugin to your WordPress plugins folder, Or install like any other WordPress plugin.
* Activate plugin.
* Open `wp-config.php`, add and edit the following lines just after  ```define('WP_DEBUG', false);```


```
define('LEGITCAR_EMAIL', "youremailaddress");
define('LEGITCAR_PASSWORD', "yourpassword");
define('LEGITCAR_CACHE_DURATION', 1800); //time in seconds you want your result for a       //particular VIN to be cached. We recommend 1800secs (30mins)
```

#### Shortcodes
##### Verification Form

To display the Verification form on a page, add the shortcode below, to that page.

```
[legitcar_verification_form]
```
The shortcode accepts the following parameter(s):

* ```form_action```  - is the page where you want to go to after the verification form is submitted (for example, the result page).
* ```form_classes```  -  are the classes you want to add to your form
* ```label```  -  the name of the label for the VIN input field
* ```placeholder```   -  placeholder for the VIN input field
* ```submit_text```  -  text on the submit button

Example usage:

```
[legitcar_verification_form form_action="verified" form_classes="my-class another-class" label="my label" placeholder="Enter VIN" submit_text="Verify"]
```

##### Verification Result

To display the Verification result on a page, add the shortcode below, to that page.

```
[legitcar_verification_result]
```

The shortcode accepts the following parameter(s):

* ```strict```  -  if this is set, and a certain line* in the plugin code is uncommented, the result page cannot be viewed directly, except VIN verification was made.

Example usage:

```
[legitcar_verification_result strict=true]
```

**To get the strict parameter working correctly, open the *class-legitcar-api-client-public.php* file, and uncomment ```//add_action('template_redirect', array($this, 'force404'));```

**This line was commented out, as it could cause performance issues for pages with large content.

## Extra Notes
This project is just a guide to get you started on consuming the LegitCar verification API from inside WordPress. You can modify the codes however you wish, to suit your own needs.


## Built using

* [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate)

## Authors

* [**Samuel Ogbujimma**](https://twitter.com/samuelik3chukwu)

## Acknowledgments

* Kudos to the WordPress Plugin Boilerplate team
