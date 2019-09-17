<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/hakstocks/core/init.php';
include 'includes/head.php';

// The custom hidden field (user id) sent along with the button is retrieved here. 
if($_GET['cm']) $user=$_GET['cm']; 
// The unique transaction id. 
if($_GET['tx']) $tx= $_GET['tx'];
 $identity = 'Your Identity'; 
// Init curl
 $ch = curl_init(); 
// Set request options 
curl_setopt_array($ch, array ( CURLOPT_URL => 'https://www.sandbox.paypal.com/cgi-bin/webscr',
  CURLOPT_POST => TRUE,
  CURLOPT_POSTFIELDS => http_build_query(array
    (
      'cmd' => '_notify-synch',
      'tx' => $tx,
      'at' => $identity,
    )),
  CURLOPT_RETURNTRANSFER => TRUE,
  CURLOPT_HEADER => FALSE,
  // CURLOPT_SSL_VERIFYPEER => TRUE,
  // CURLOPT_CAINFO => 'cacert.pem',
));
// Execute request and get response and status code
$response = curl_exec($ch);
$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// Close connection
curl_close($ch);
if($status == 200 AND strpos($response, 'SUCCESS') === 0)
{
    // Save the Record into the database
    echo $tx;
}

if (isset($_GET['add'])) {
	$name = ((isset($_POST['full_name']))?checkInput($_POST['full_name']):'');
	$email = ((isset($_POST['email']))?checkInput($_POST['email']):'');
	$street1 = ((isset($_POST['street1']))?checkInput($_POST['street1']):'');
	$street2 = ((isset($_POST['street2']))?checkInput($_POST['street2']):'');
	$city = ((isset($_POST['city']))?checkInput($_POST['city']):'');
	$state = ((isset($_POST['state']))?checkInput($_POST['state']):'');
	$zip = ((isset($_POST['zip']))?checkInput($_POST['zip']):'');
	$country = ((isset($_POST['country']))?checkInput($_POST['country']):'');
	$error = array();
	$required = array(
		'full_name' => 'Full Name',
		'email' 	=> 'Email',
		'street1' 	=> 'Street Address',
		'city' 		=> 'City',
		'state' 	=> 'State',
		'zip' 		=> 'Zip Code',
		'country' 	=> 'Country',
	);

	if ($_POST) {
			$email_sql = $db->query("SELECT * FROM customers WHERE email = '$email'");
			$email_num = mysqli_num_rows($email_sql);

			if ($email_num != 0) {
				$error[] = 'Email already exists.';
			}


			if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
				$error[] = "Invalid email.";
			}

		// Check if all required fields are filled.
		foreach ($required as $f => $d) {
			if (empty($_POST[$f]) || $_POST[$f] = '') {
				$error[] = $d. ' is required.';
			}
		}

		if (!empty($error)) {
			echo show_error($error);
		}
		else {
		// Add customer to database.
			$adduser_sql = $db->query("INSERT INTO customer (full_name, email, street1, street2, city, state, zip, country) VALUES ('$name','$email','$street1','$street2','city','state','zip','country')");
			$_SESSION['success_flash'] = 'You have successfully added a user.';
		}
	}
}


$country_list = array(
		"Afghanistan",
		"Albania",
		"Algeria",
		"Andorra",
		"Angola",
		"Antigua and Barbuda",
		"Argentina",
		"Armenia",
		"Australia",
		"Austria",
		"Azerbaijan",
		"Bahamas",
		"Bahrain",
		"Bangladesh",
		"Barbados",
		"Belarus",
		"Belgium",
		"Belize",
		"Benin",
		"Bhutan",
		"Bolivia",
		"Bosnia and Herzegovina",
		"Botswana",
		"Brazil",
		"Brunei",
		"Bulgaria",
		"Burkina Faso",
		"Burundi",
		"Cambodia",
		"Cameroon",
		"Canada",
		"Cape Verde",
		"Central African Republic",
		"Chad",
		"Chile",
		"China",
		"Colombi",
		"Comoros",
		"Congo (Brazzaville)",
		"Congo",
		"Costa Rica",
		"Cote d'Ivoire",
		"Croatia",
		"Cuba",
		"Cyprus",
		"Czech Republic",
		"Denmark",
		"Djibouti",
		"Dominica",
		"Dominican Republic",
		"East Timor (Timor Timur)",
		"Ecuador",
		"Egypt",
		"El Salvador",
		"Equatorial Guinea",
		"Eritrea",
		"Estonia",
		"Ethiopia",
		"Fiji",
		"Finland",
		"France",
		"Gabon",
		"Gambia, The",
		"Georgia",
		"Germany",
		"Ghana",
		"Greece",
		"Grenada",
		"Guatemala",
		"Guinea",
		"Guinea-Bissau",
		"Guyana",
		"Haiti",
		"Honduras",
		"Hungary",
		"Iceland",
		"India",
		"Indonesia",
		"Iran",
		"Iraq",
		"Ireland",
		"Israel",
		"Italy",
		"Jamaica",
		"Japan",
		"Jordan",
		"Kazakhstan",
		"Kenya",
		"Kiribati",
		"Korea, North",
		"Korea, South",
		"Kuwait",
		"Kyrgyzstan",
		"Laos",
		"Latvia",
		"Lebanon",
		"Lesotho",
		"Liberia",
		"Libya",
		"Liechtenstein",
		"Lithuania",
		"Luxembourg",
		"Macedonia",
		"Madagascar",
		"Malawi",
		"Malaysia",
		"Maldives",
		"Mali",
		"Malta",
		"Marshall Islands",
		"Mauritania",
		"Mauritius",
		"Mexico",
		"Micronesia",
		"Moldova",
		"Monaco",
		"Mongolia",
		"Morocco",
		"Mozambique",
		"Myanmar",
		"Namibia",
		"Nauru",
		"Nepal",
		"Netherlands",
		"New Zealand",
		"Nicaragua",
		"Niger",
		"Nigeria",
		"Norway",
		"Oman",
		"Pakistan",
		"Palau",
		"Panama",
		"Papua New Guinea",
		"Paraguay",
		"Peru",
		"Philippines",
		"Poland",
		"Portugal",
		"Qatar",
		"Romania",
		"Russia",
		"Rwanda",
		"Saint Kitts and Nevis",
		"Saint Lucia",
		"Saint Vincent",
		"Samoa",
		"San Marino",
		"Sao Tome and Principe",
		"Saudi Arabia",
		"Senegal",
		"Serbia and Montenegro",
		"Seychelles",
		"Sierra Leone",
		"Singapore",
		"Slovakia",
		"Slovenia",
		"Solomon Islands",
		"Somalia",
		"South Africa",
		"Spain",
		"Sri Lanka",
		"Sudan",
		"Suriname",
		"Swaziland",
		"Sweden",
		"Switzerland",
		"Syria",
		"Taiwan",
		"Tajikistan",
		"Tanzania",
		"Thailand",
		"Togo",
		"Tonga",
		"Trinidad and Tobago",
		"Tunisia",
		"Turkey",
		"Turkmenistan",
		"Tuvalu",
		"Uganda",
		"Ukraine",
		"United Arab Emirates",
		"United Kingdom",
		"United States",
		"Uruguay",
		"Uzbekistan",
		"Vanuatu",
		"Vatican City",
		"Venezuela",
		"Vietnam",
		"Yemen",
		"Zambia",
		"Zimbabwe",
	);

?>

<form action="paypal_success.php?add=1" method="post" id="payment-form">
	<span class="text-danger" id="payment-error"></span>
	<div class="col-md-5">
		<div class="form-group">
			<label for="full_name">Full Name:</label>
			<input type="text" class="form-control" id="full_name" name="full_name">
		</div>
		<div class="form-group">
			<label for="email">E-mail:</label>
			<input type="email" class="form-control" id="email" name="email">
		</div>
		<div class="form-group">
			<label for="street1">Street Address 1:</label>
			<input type="text" class="form-control" id="street1" name="street1">
		</div>
		<div class="form-group">
			<label for="street2">Street Address 2:</label>
			<input type="text" class="form-control" id="street2" name="street2">
		</div>
		<div class="form-group">
			<label for="city">City:</label>
			<input type="text" class="form-control" id="city" name="city">
		</div>
		<div class="form-group">
			<label for="state">State:</label>
			<input type="text" class="form-control" id="state" name="state">
		</div>
		<div class="form-group">
			<label for="zip">Zip:</label>
			<input type="text" class="form-control" id="zip" name="zip">
		</div>
		<div class="form-group">
			<label for="country">Country:</label>
			<select name="country" class="form-control" id="country">
				<option value=""></option>
				<?php foreach($country_list as $cr => $value): ?>
					<option value="<?php echo $cr; ?>"><?php echo $value; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<input type="submit" class="ghost-button" value="Add Customer">
		</div>
	</div>
</form>