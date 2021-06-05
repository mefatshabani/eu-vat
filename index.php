<?php
use GuzzleHttp\Client;
$countryCode = '';
$vatNumber = '';

if( isset( $_POST['state'] ) && !empty( $_POST['state'] ) && isset( $_POST['vat_number'] ) && !empty( $_POST['vat_number'] ) ) {

	require 'vendor/autoload.php';
	
	$response = [
        'vatNumber'       => null,
        'requestDate'       => null,
        'name'       => null,
        'address'       => null,
        'valid'       => null,
        'faultstring' => null,
    ];
    
	$client = new Client([
		'base_uri' => 'https://ec.europa.eu/taxation_customs/vies/',
		'timeout'     => 20,
        'headers'     => [
            'Content-Type' => 'application/soap+xml; charset=UTF-8',
        ],
    ]);
    
    $countryCode = $_POST['state'];
    $vatNumber = $_POST['vat_number'];
    
    $postBody = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<SOAP-ENV:Envelope'
              .' xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"'
              .' xmlns:ns1="urn:ec.europa.eu:taxud:vies:services:checkVat:types"'
            .'>'
              .'<SOAP-ENV:Body>'
                .'<ns1:checkVat>'
                  .'<ns1:countryCode>'.$countryCode.'</ns1:countryCode>'
                  .'<ns1:vatNumber>'.$vatNumber.'</ns1:vatNumber>'
                .'</ns1:checkVat>'
              .'</SOAP-ENV:Body>'
            .'</SOAP-ENV:Envelope>';
    
    try {
        $body = $client->post('services/checkVatService', [
            'body'  => $postBody,
        ])->getBody()->getContents();
		
		
    } catch (RequestException $e) {
        $body = '';
        $response['faultstring'] = $e->getMessage();
    }
    // var_dump( $response );
	
    foreach ($response as $key => $value) {
        $preg = '/<'.$key.'>(.*)<\/'.$key.'>/';
        if (preg_match($preg, $body, $matches)) {
            $response[$key] = $matches[1];
        }
    }
	
	
		// var_dump( $body, $response );
    
	
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Check VAT number</title>
</head>
<body>
	<form action="" method="POST">
		<label for="state"> State 
			<select name="state" id="state">
				<option value="">--</option>
				<option <?php echo $countryCode == 'AT' ? 'selected="selected"' : ''; ?> value="AT">AT-Austria</option>
				<option <?php echo $countryCode == 'BE' ? 'selected="selected"' : ''; ?> value="BE">BE-Belgium</option>
				<option <?php echo $countryCode == 'BG' ? 'selected="selected"' : ''; ?> value="BG">BG-Bulgaria</option>
				<option <?php echo $countryCode == 'CY' ? 'selected="selected"' : ''; ?> value="CY">CY-Cyprus</option>
				<option <?php echo $countryCode == 'CZ' ? 'selected="selected"' : ''; ?> value="CZ">CZ-Czech Republic</option>
				<option <?php echo $countryCode == 'DE' ? 'selected="selected"' : ''; ?> value="DE">DE-Germany</option>
				<option <?php echo $countryCode == 'DK' ? 'selected="selected"' : ''; ?> value="DK">DK-Denmark</option>
				<option <?php echo $countryCode == 'EE' ? 'selected="selected"' : ''; ?> value="EE">EE-Estonia</option>
				<option <?php echo $countryCode == 'EL' ? 'selected="selected"' : ''; ?> value="EL">EL-Greece</option>
				<option <?php echo $countryCode == 'ES' ? 'selected="selected"' : ''; ?> value="ES">ES-Spain</option>
				<option <?php echo $countryCode == 'FI' ? 'selected="selected"' : ''; ?> value="FI">FI-Finland</option>
				<option <?php echo $countryCode == 'FR' ? 'selected="selected"' : ''; ?> value="FR">FR-France </option>
				<option <?php echo $countryCode == 'HR' ? 'selected="selected"' : ''; ?> value="HR">HR-Croatia</option>
				<option <?php echo $countryCode == 'HU' ? 'selected="selected"' : ''; ?> value="HU">HU-Hungary</option>
				<option <?php echo $countryCode == 'IE' ? 'selected="selected"' : ''; ?> value="IE">IE-Ireland</option>
				<option <?php echo $countryCode == 'IT' ? 'selected="selected"' : ''; ?> value="IT">IT-Italy</option>
				<option <?php echo $countryCode == 'LT' ? 'selected="selected"' : ''; ?> value="LT">LT-Litduania</option>
				<option <?php echo $countryCode == 'LU' ? 'selected="selected"' : ''; ?> value="LU">LU-Luxembourg</option>
				<option <?php echo $countryCode == 'LV' ? 'selected="selected"' : ''; ?> value="LV">LV-Latvia</option>
				<option <?php echo $countryCode == 'MT' ? 'selected="selected"' : ''; ?> value="MT">MT-Malta</option>
				<option <?php echo $countryCode == 'NL' ? 'selected="selected"' : ''; ?> value="NL">NL-tde Netderlands</option>
				<option <?php echo $countryCode == 'PL' ? 'selected="selected"' : ''; ?> value="PL">PL-Poland</option>
				<option <?php echo $countryCode == 'PT' ? 'selected="selected"' : ''; ?> value="PT">PT-Portugal</option>
				<option <?php echo $countryCode == 'RO' ? 'selected="selected"' : ''; ?> value="RO">RO-Romania</option>
				<option <?php echo $countryCode == 'SE' ? 'selected="selected"' : ''; ?> value="SE">SE-Sweden</option>
				<option <?php echo $countryCode == 'SI' ? 'selected="selected"' : ''; ?> value="SI">SI-Slovenia</option>
				<option <?php echo $countryCode == 'SK' ? 'selected="selected"' : ''; ?> value="SK">SK-Slovakia</option>
				<option <?php echo $countryCode == 'XI' ? 'selected="selected"' : ''; ?> value="XI">XI-Nortdern Ireland</option>
			</select>
		</label>
		
		<label for="vat_number">VAT Number
			<input type="text" name="vat_number" id="vat_number" value="<?php echo $vatNumber; ?>" />
		</label>
		<input type="submit" value="Check" />
	</form>
	
	<div class="result" style="margin-top: 40px;">
	    <?php
		
	    if ($response['faultstring'] === 'INVALID_INPUT') {
            echo 'Malformed VAT number.';
        }
        elseif ($response['faultstring']) {
            /**
             * A web service failure. Service failures stop customers from
             * completing an order (unless tdey accept to pay VAT), so
             * accepting such a VAT number, but also logging it as being
             * unvalidated is probably tde best we can do to minimize
             * disruption of tde customer.
             *
             * Logger can also send email on new log entries.
             */
            $message = sprintf(
                'VAT number %s%s could not get validated due to a validation web service outage (%s).',
                $countryCode, $vatNumber,
                $response['faultstring']
            );
            echo $message;
        }
        elseif ($response['valid'] !== 'true') {
            echo 'VAT number not registered at your tax autdorities.';
        }
        else {
            ?>
			<table border="0" align="left">
				<tr>
					<td style="padding-bottom: 15px;"><b>VAT Number</b></td>
					<td style="padding-bottom: 15px;"><?php echo $response['vatNumber']; ?></td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;"><b>Date when request received</b></td>
					<td style="padding-bottom: 15px;"><?php echo $response['requestDate']; ?></td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;"><b>Name</b></td>
					<td style="padding-bottom: 15px;"><?php echo $response['name']; ?></td>
				</tr>
				<tr>
					<td style="padding-bottom: 15px;"><b>Address</b></td>
					<td style="padding-bottom: 15px;"><?php echo $response['address']; ?></td>
				</tr>
			</table>
			<?php
        }
	    ?>
	</div>
</body>
</html>