# EasyGeo
A PHP Class that does all the heavywork and lets you geocode/reverse geocode data effortlessly. It uses the Google Maps API to retrieve and validate the address/latitude/longitude values.

## Features
- [x] Lightweight
- [x] Multi-channel - Checks for `curl` first, if not enabled checks for `file_get_contents`
- [x] Handles multiple addresses returned by the server
- [x] Simple to use
- [x] Error handling when something goes wrong with the API call

## Usage
```php
// include the class file
require_once("EasyGeo.php");

// create the object/instance of the class
$geo = new EasyGeo();

// call the geocoding function and pass the textual address as the parameter
$data = $geo->geocodeTextAddress("200 E Berry St");

// observe the output
echo '<pre>';
print_r($data);
echo '</pre>';

/*  RETURNED DATA 
- Full textual address
- Latitude
- Longitude
- Zip Code
- Zip Code Suffix (wherever applicable)
- Place ID (pertaining to Google Map)
*/
```

## To Do
Complete the reverse geocoding module; i.e., convert a latitude and longitude pair into a human-readable address.

## License
Code licensed under [GPL-3.0](https://github.com/noobards/EasyGeo/blob/master/LICENSE)
