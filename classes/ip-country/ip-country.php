<?php

class BSK_GFBLCV_IP_Country {
    
    private $bsk_gfblcv_countries_code = array();
    private $bsk_gfblcv_server_api_list = array();
    
	public function __construct() {
		
        $this->bsk_gfblcv_countries_code = array(
                                                    'AF' => 'Afghanistan',
                                                    'AX' => 'Aland Islands',
                                                    'AL' => 'Albania',
                                                    'DZ' => 'Algeria',
                                                    'AS' => 'American Samoa',
                                                    'AD' => 'Andorra',
                                                    'AO' => 'Angola',
                                                    'AI' => 'Anguilla',
                                                    'AQ' => 'Antarctica',
                                                    'AG' => 'Antigua and Barbuda',
                                                    'AR' => 'Argentina',
                                                    'AM' => 'Armenia',
                                                    'AW' => 'Aruba',
                                                    'AU' => 'Australia',
                                                    'AT' => 'Austria',
                                                    'AZ' => 'Azerbaijan',
                                                    'BS' => 'Bahamas',
                                                    'BH' => 'Bahrain',
                                                    'BD' => 'Bangladesh',
                                                    'BB' => 'Barbados',
                                                    'BY' => 'Belarus',
                                                    'BE' => 'Belgium',
                                                    'BZ' => 'Belize',
                                                    'BJ' => 'Benin',
                                                    'BM' => 'Bermuda',
                                                    'BT' => 'Bhutan',
                                                    'BO' => 'Bolivia (Plurinational State of)',
                                                    'BQ' => 'Bonaire, Sint Eustatius and Saba',
                                                    'BA' => 'Bosnia and Herzegovina',
                                                    'BW' => 'Botswana',
                                                    'BR' => 'Brazil',
                                                    'IO' => 'British Indian Ocean Territory',
                                                    'BN' => 'Brunei Darussalam',
                                                    'BG' => 'Bulgaria',
                                                    'BF' => 'Burkina Faso',
                                                    'BI' => 'Burundi',
                                                    'CV' => 'Cabo Verde',
                                                    'KH' => 'Cambodia',
                                                    'CM' => 'Cameroon',
                                                    'CA' => 'Canada',
                                                    'KY' => 'Cayman Islands',
                                                    'CF' => 'Central African Republic',
                                                    'TD' => 'Chad',
                                                    'CL' => 'Chile',
                                                    'CN' => 'China',
                                                    'CO' => 'Colombia',
                                                    'KM' => 'Comoros',
                                                    'CG' => 'Congo',
                                                    'CD' => 'Congo (Democratic Republic of the)',
                                                    'CK' => 'Cook Islands',
                                                    'CR' => 'Costa Rica',
                                                    'CI' => 'Cote D\'ivoire',
                                                    'HR' => 'Croatia',
                                                    'CU' => 'Cuba',
                                                    'CW' => 'Curacao',
                                                    'CY' => 'Cyprus',
                                                    'CZ' => 'Czechia',
                                                    'DK' => 'Denmark',
                                                    'DJ' => 'Djibouti',
                                                    'DM' => 'Dominica',
                                                    'DO' => 'Dominican Republic',
                                                    'EC' => 'Ecuador',
                                                    'EG' => 'Egypt',
                                                    'SV' => 'El Salvador',
                                                    'GQ' => 'Equatorial Guinea',
                                                    'ER' => 'Eritrea',
                                                    'EE' => 'Estonia',
                                                    'SZ' => 'Eswatini',
                                                    'ET' => 'Ethiopia',
                                                    'FK' => 'Falkland Islands (Malvinas)',
                                                    'FO' => 'Faroe Islands',
                                                    'FJ' => 'Fiji',
                                                    'FI' => 'Finland',
                                                    'FR' => 'France',
                                                    'GF' => 'French Guiana',
                                                    'PF' => 'French Polynesia',
                                                    'GA' => 'Gabon',
                                                    'GM' => 'Gambia',
                                                    'GE' => 'Georgia',
                                                    'DE' => 'Germany',
                                                    'GH' => 'Ghana',
                                                    'GI' => 'Gibraltar',
                                                    'GR' => 'Greece',
                                                    'GL' => 'Greenland',
                                                    'GD' => 'Grenada',
                                                    'GP' => 'Guadeloupe',
                                                    'GU' => 'Guam',
                                                    'GT' => 'Guatemala',
                                                    'GG' => 'Guernsey',
                                                    'GN' => 'Guinea',
                                                    'GW' => 'Guinea-Bissau',
                                                    'GY' => 'Guyana',
                                                    'HT' => 'Haiti',
                                                    'VA' => 'Holy See',
                                                    'HN' => 'Honduras',
                                                    'HK' => 'Hong Kong',
                                                    'HU' => 'Hungary',
                                                    'IS' => 'Iceland',
                                                    'IN' => 'India',
                                                    'ID' => 'Indonesia',
                                                    'IR' => 'Iran (Islamic Republic of)',
                                                    'IQ' => 'Iraq',
                                                    'IE' => 'Ireland',
                                                    'IM' => 'Isle of Man',
                                                    'IL' => 'Israel',
                                                    'IT' => 'Italy',
                                                    'JM' => 'Jamaica',
                                                    'JP' => 'Japan',
                                                    'JE' => 'Jersey',
                                                    'JO' => 'Jordan',
                                                    'KZ' => 'Kazakhstan',
                                                    'KE' => 'Kenya',
                                                    'KI' => 'Kiribati',
                                                    'KP' => 'Korea (Democratic People\'s Republic of)',
                                                    'KR' => 'Korea (Republic of)',
                                                    'KW' => 'Kuwait',
                                                    'KG' => 'Kyrgyzstan',
                                                    'LA' => 'Lao People\'s Democratic Republic',
                                                    'LV' => 'Latvia',
                                                    'LB' => 'Lebanon',
                                                    'LS' => 'Lesotho',
                                                    'LR' => 'Liberia',
                                                    'LY' => 'Libya',
                                                    'LI' => 'Liechtenstein',
                                                    'LT' => 'Lithuania',
                                                    'LU' => 'Luxembourg',
                                                    'MO' => 'Macao',
                                                    'MG' => 'Madagascar',
                                                    'MW' => 'Malawi',
                                                    'MY' => 'Malaysia',
                                                    'MV' => 'Maldives',
                                                    'ML' => 'Mali',
                                                    'MT' => 'Malta',
                                                    'MH' => 'Marshall Islands',
                                                    'MQ' => 'Martinique',
                                                    'MR' => 'Mauritania',
                                                    'MU' => 'Mauritius',
                                                    'YT' => 'Mayotte',
                                                    'MX' => 'Mexico',
                                                    'FM' => 'Micronesia (Federated States of)',
                                                    'MD' => 'Moldova (Republic of)',
                                                    'MC' => 'Monaco',
                                                    'MN' => 'Mongolia',
                                                    'ME' => 'Montenegro',
                                                    'MS' => 'Montserrat',
                                                    'MA' => 'Morocco',
                                                    'MZ' => 'Mozambique',
                                                    'MM' => 'Myanmar',
                                                    'NA' => 'Namibia',
                                                    'NR' => 'Nauru',
                                                    'NP' => 'Nepal',
                                                    'NL' => 'Netherlands',
                                                    'NC' => 'New Caledonia',
                                                    'NZ' => 'New Zealand',
                                                    'NI' => 'Nicaragua',
                                                    'NE' => 'Niger',
                                                    'NG' => 'Nigeria',
                                                    'NU' => 'Niue',
                                                    'NF' => 'Norfolk Island',
                                                    'MK' => 'North Macedonia',
                                                    'MP' => 'Northern Mariana Islands',
                                                    'NO' => 'Norway',
                                                    'OM' => 'Oman',
                                                    'PK' => 'Pakistan',
                                                    'PW' => 'Palau',
                                                    'PS' => 'Palestine, State of',
                                                    'PA' => 'Panama',
                                                    'PG' => 'Papua New Guinea',
                                                    'PY' => 'Paraguay',
                                                    'PE' => 'Peru',
                                                    'PH' => 'Philippines',
                                                    'PL' => 'Poland',
                                                    'PT' => 'Portugal',
                                                    'PR' => 'Puerto Rico',
                                                    'QA' => 'Qatar',
                                                    'RE' => 'Reunion',
                                                    'RO' => 'Romania',
                                                    'RU' => 'Russian Federation',
                                                    'RW' => 'Rwanda',
                                                    'BL' => 'Saint Barthelemy',
                                                    'KN' => 'Saint Kitts and Nevis',
                                                    'LC' => 'Saint Lucia',
                                                    'MF' => 'Saint Martin (French Part)',
                                                    'PM' => 'Saint Pierre and Miquelon',
                                                    'VC' => 'Saint Vincent and The Grenadines',
                                                    'WS' => 'Samoa',
                                                    'SM' => 'San Marino',
                                                    'ST' => 'Sao Tome and Principe',
                                                    'SA' => 'Saudi Arabia',
                                                    'SN' => 'Senegal',
                                                    'RS' => 'Serbia',
                                                    'SC' => 'Seychelles',
                                                    'SL' => 'Sierra Leone',
                                                    'SG' => 'Singapore',
                                                    'SX' => 'Sint Maarten (Dutch Part)',
                                                    'SK' => 'Slovakia',
                                                    'SI' => 'Slovenia',
                                                    'SB' => 'Solomon Islands',
                                                    'SO' => 'Somalia',
                                                    'ZA' => 'South Africa',
                                                    'SS' => 'South Sudan',
                                                    'ES' => 'Spain',
                                                    'LK' => 'Sri Lanka',
                                                    'SD' => 'Sudan',
                                                    'SR' => 'Suriname',
                                                    'SJ' => 'Svalbard and Jan Mayen',
                                                    'SE' => 'Sweden',
                                                    'CH' => 'Switzerland',
                                                    'SY' => 'Syrian Arab Republic',
                                                    'TW' => 'Taiwan (Province of China)',
                                                    'TJ' => 'Tajikistan',
                                                    'TZ' => 'Tanzania, United Republic of',
                                                    'TH' => 'Thailand',
                                                    'TL' => 'Timor-Leste',
                                                    'TG' => 'Togo',
                                                    'TK' => 'Tokelau',
                                                    'TO' => 'Tonga',
                                                    'TT' => 'Trinidad and Tobago',
                                                    'TN' => 'Tunisia',
                                                    'TR' => 'Turkey',
                                                    'TM' => 'Turkmenistan',
                                                    'TC' => 'Turks and Caicos Islands',
                                                    'TV' => 'Tuvalu',
                                                    'UG' => 'Uganda',
                                                    'UA' => 'Ukraine',
                                                    'AE' => 'United Arab Emirates',
                                                    'GB' => 'United Kingdom of Great Britain and Northern Ireland',
                                                    'UM' => 'United States Minor Outlying Islands',
                                                    'US' => 'United States of America',
                                                    'UY' => 'Uruguay',
                                                    'UZ' => 'Uzbekistan',
                                                    'VU' => 'Vanuatu',
                                                    'VE' => 'Venezuela (Bolivarian Republic of)',
                                                    'VN' => 'Viet Nam',
                                                    'VG' => 'Virgin Islands (British)',
                                                    'VI' => 'Virgin Islands (U.S.)',
                                                    'WF' => 'Wallis and Futuna',
                                                    'YE' => 'Yemen',
                                                    'ZM' => 'Zambia',
                                                    'ZW' => 'Zimbabwe',
                                                );
        
        $this->bsk_gfblcv_server_api_list = array(
                                                'ipinfodb.com' => array( 
                                                                         'label' => 'ipinfodb.com',
                                                                         'api' => 'http://api.ipinfodb.com/v3/ip-country/?key=API_KEY&ip=IP_ADDRESS&format=json',
                                                                         'ref' => 'https://ipinfodb.com/api',
                                                                         'key' => 'YES',
                                                                       ),
                                                'ip2location.com' => array(
                                                                            'label' => 'ip2location.com',
                                                                            'api' => 'https://api.ip2location.com/v2/?key=API_KEY&ip=IP_ADDRESS&format=json&package=WS1&lang=en',
                                                                            'ref' => 'https://www.ip2location.com/web-service/ip2location',
                                                                            'key' => 'YES',
                                                                          ),
                                                'ip-api.com.free' => array(
                                                                            'label' => 'ip-api.com ( free )',
                                                                            'api' => 'http://ip-api.com/json/IP_ADDRESS?fields=status,message,countryCode',
                                                                            'ref' => 'https://members.ip-api.com/',
                                                                            'key' => 'NO',
                                                                          ),
                                                'ip-api.com.pro' => array(
                                                                            'label' => 'ip-api.com ( pro )',
                                                                            'api' => 'https://pro.ip-api.com/json/IP_ADDRESS?key=API_KEY&fields=status,message,countryCode',
                                                                            'ref' => 'https://members.ip-api.com/',
                                                                            'key' => 'YES',
                                                                          ),
                                                'ipstack.com' => array(
                                                                        'label' => 'ipstack.com',
                                                                        'api' => 'http://api.ipstack.com/IP_ADDRESS?access_key=API_KEY&format=1',
                                                                        'ref' => 'https://ipstack.com/product',
                                                                        'key' => 'YES',
                                                                      ),
            
                                                'ipgeolocation.io' => array(
                                                                        'label' => 'ipgeolocation.io',
                                                                        'api' => 'https://api.ipgeolocation.io/ipgeo?apiKey=API_KEY&ip=IP_ADDRESS',
                                                                        'ref' => 'https://ipgeolocation.io/',
                                                                        'key' => 'YES',
                                                                      ),
            
            
                                            );
        
        add_action( 'wp_ajax_bsk_gfblcv_ip_list_test_API', array( $this, 'bsk_gfblcv_ip_list_test_API_fun' ) );
        
	}
    
    function bsk_gfblcv_get_county_code_list(){
        return $this->bsk_gfblcv_countries_code;
    }
    
    function bsk_gfblcv_get_api_server_list(){
        return $this->bsk_gfblcv_server_api_list;
    }
	
    function bsk_gfblcv_ip_list_test_API_fun(){

        if( !check_ajax_referer( 'bsk_gfblcv_ip_list_test_api_ajax_oper_nonce', 'nonce' ) ){
            $data_to_return = '<p style="color: #FF0000;">'.__( 'Invalid nonce, please refresh page to try again', 'bskgfblcv' ).'</p>';
            
            wp_die( $data_to_return );
        }
        
        $api_server = $_POST['server'];
        $api_key = $_POST['key'];
        $ip_address = $_POST['ip'];
        $selected_country = $_POST['selected_country'];
        
        if( $api_server == '' || !array_key_exists( $api_server, $this->bsk_gfblcv_server_api_list ) ){
            $data_to_return = '<p style="color: #FF0000;">'.__( 'Invalid API server', 'bskgfblcv' ).'</p>';
            
            wp_die( $data_to_return );
        }
        
        $api_key_required = $this->bsk_gfblcv_server_api_list[$api_server]['key'] == 'YES' ? true : false;
        if( $api_key_required && $api_key == '' ){
            $data_to_return = '<p style="color: #FF0000;">'.__( 'Invalid API key', 'bskgfblcv' ).'</p>';
            
            wp_die( $data_to_return );
        }
        
        if( $ip_address == '' ){
            $data_to_return = '<p style="color: #FF0000;">'.__( 'Invalid IP address', 'bskgfblcv' ).'</p>';
            
            wp_die( $data_to_return );
        }
        
        $data_to_return = '<p style="color: #FF0000;">'.__( 'Call server API failed.', 'bskgfblcv' ).'</p>';
        if( $api_server == 'ipinfodb.com' ){
            $response_array = $this->ipinfodb_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['html'];
        }else if( $api_server == 'ip2location.com' ){
            $response_array = $this->ip2location_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['html'];
        }else if( $api_server == 'ip-api.com.free' ){
            $response_array = $this->ip_api_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['html'];
        }else if( $api_server == 'ipstack.com' ){
            $response_array = $this->ipstack_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['html'];
        }else if( $api_server == 'ipgeolocation.io' ){
            $response_array = $this->ipgeolocation_io_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['html'];
        }
        
        wp_die( $data_to_return );
    }
    
    function ipinfodb_com_api_response( $api_server, $ip_address, $api_key, $selected_country ){
        
        $args = array(
                        'method' => 'GET',
                        'timeout' => 60,
                     );
        $server_url = $this->bsk_gfblcv_server_api_list[$api_server]['api'];
        $server_url = str_replace( 'IP_ADDRESS', $ip_address, $server_url );
        $server_url = str_replace( 'API_KEY', $api_key, $server_url );

        $ip_country_return = wp_remote_post( $server_url, $args );
        if( is_wp_error( $ip_country_return ) ) {
			$error_message = $ip_country_return->get_error_message();
            $data_to_return = '<p style="color: #FF0000;">'.$error_message.'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
		}

        $ip_country_respond_body  = wp_remote_retrieve_body( $ip_country_return );
        $ip_country_return_array = json_decode( $ip_country_respond_body, true );

        if( $ip_country_return_array['statusCode'] != 'OK' ){
            $data_to_return = '<p style="color: #FF0000;">ERROR: '.$ip_country_return_array['statusMessage'].'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
        }
        
        $selected_country_message = '';
        $in_country = false;
        if( $selected_country ){
            $selected_country_array = explode( ',', $selected_country );
            if( in_array( $ip_country_return_array['countryCode'], $selected_country_array ) ){
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #1abb25;">It is in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #1abb25;">It is in the country you added.</p>';
                }
                $in_country = true;
            }else{
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the country you added.</p>';
                }
                $in_country = false;
            }
        }
        $data_to_return = '<p style="color: #1abb25;">The IP address: <strong>'.$ip_address.'</strong> belongs to <strong>'.$ip_country_return_array['countryName'].'</strong>.</p>';
        $data_to_return .= $selected_country_message;
        
        return( array( 'result' => $in_country, 'html' => $data_to_return ) );
    }
    
    function ip2location_com_api_response( $api_server, $ip_address, $api_key, $selected_country ){
        
        $args = array(
                        'method' => 'GET',
                        'timeout' => 60,
                     );

        $server_url = $this->bsk_gfblcv_server_api_list[$api_server]['api'];
        $server_url = str_replace( 'IP_ADDRESS', $ip_address, $server_url );
        $server_url = str_replace( 'API_KEY', $api_key, $server_url );

        $ip_country_return = wp_remote_post( $server_url, $args );
        if( is_wp_error( $ip_country_return ) ) {
			$error_message = $ip_country_return->get_error_message();
            $data_to_return = '<p style="color: #FF0000;">'.$error_message.'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
		}
        
        $ip_country_respond_body  = wp_remote_retrieve_body( $ip_country_return );
        $ip_country_return_array = json_decode( $ip_country_respond_body, true );
        
        if( !isset( $ip_country_return_array['country_code'] ) ){
            $data_to_return = '';
            if( isset( $ip_country_return_array['response'] ) ){
                $message = $ip_country_return_array['response'];
                if( strpos( $message, 'Invalid account' ) !== false ){
                    $message .= ' Please check if your API key right.';
                }
                $data_to_return = '<p style="color: #FF0000;">ERROR: '.$message.'</p>';
            }else{
                $message = '';
                foreach( $ip_country_return_array['response'] as $key => $reason ){
                    $message .= $key.' : '.$reason.' ';
                }
                $message = trim( $message );
                $data_to_return = '<p style="color: #FF0000;">ERROR: '.$message.'</p>';
            }
            
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
        }
        
        $selected_country_message = '';
        $in_country = false;
        if( $selected_country ){
            $selected_country_array = explode( ',', $selected_country );
            if( in_array( $ip_country_return_array['country_code'], $selected_country_array ) ){
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #1abb25;">It is in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #1abb25;">It is in the country you added.</p>';
                }
                $in_country = true;
            }else{
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the country you added.</p>';
                }
                $in_country = false;
            }
        }
        
        $country_name = isset( $this->bsk_gfblcv_countries_code[$ip_country_return_array['country_code']] ) ? $this->bsk_gfblcv_countries_code[$ip_country_return_array['country_code']] : $ip_country_return_array['country_code'];
        
        $data_to_return = '<p style="color: #1abb25;">The IP address: <strong>'.$ip_address.'</strong> belongs to <strong>'.$country_name.'</strong>.</p>';
        $data_to_return .= $selected_country_message;
        
        return( array( 'result' => $in_country, 'html' => $data_to_return ) );
    }
    
    function ip_api_com_api_response( $api_server, $ip_address, $api_key, $selected_country ){
        
        $args = array(
                        'method' => 'GET',
                        'timeout' => 60,
                     );

        $server_url = $this->bsk_gfblcv_server_api_list[$api_server]['api'];
        $server_url = str_replace( 'IP_ADDRESS', $ip_address, $server_url );

        $ip_country_return = wp_remote_post( $server_url, $args );
        if( is_wp_error( $ip_country_return ) ) {
			$error_message = $ip_country_return->get_error_message();
            $data_to_return = '<p style="color: #FF0000;">'.$error_message.'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
		}
        
        $ip_country_respond_body  = wp_remote_retrieve_body( $ip_country_return );
        $ip_country_return_array = json_decode( $ip_country_respond_body, true );
        
        if( $ip_country_return_array['status'] != 'success' ){
            $data_to_return = '<p style="color: #FF0000;">ERROR: '.$ip_country_return_array['message'].'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
        }
        
        $selected_country_message = '';
        $in_country = false;
        if( $selected_country ){
            $selected_country_array = explode( ',', $selected_country );
            if( in_array( $ip_country_return_array['countryCode'], $selected_country_array ) ){
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #1abb25;">It is in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #1abb25;">It is in the country you added.</p>';
                }
                $in_country = true;
            }else{
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the country you added.</p>';
                }
                $in_country = false;
            }
        }
        
        $country_name = isset( $this->bsk_gfblcv_countries_code[$ip_country_return_array['countryCode']] ) ? $this->bsk_gfblcv_countries_code[$ip_country_return_array['countryCode']] : $ip_country_return_array['countryCode'];
        
        $data_to_return = '<p style="color: #1abb25;">The IP address: <strong>'.$ip_address.'</strong> belongs to <strong>'.$country_name.'</strong>.</p>';
        $data_to_return .= $selected_country_message;
        
        return( array( 'result' => $in_country, 'html' => $data_to_return ) );
    }
    
    function ipstack_com_api_response( $api_server, $ip_address, $api_key, $selected_country ){
        
        $args = array(
                        'method' => 'GET',
                        'timeout' => 60,
                     );

        $server_url = $this->bsk_gfblcv_server_api_list[$api_server]['api'];
        $server_url = str_replace( 'IP_ADDRESS', $ip_address, $server_url );
        $server_url = str_replace( 'API_KEY', $api_key, $server_url );

        $ip_country_return = wp_remote_post( $server_url, $args );
        if( is_wp_error( $ip_country_return ) ) {
			$error_message = $ip_country_return->get_error_message();
            $data_to_return = '<p style="color: #FF0000;">'.$error_message.'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
		}
        
        $ip_country_respond_body  = wp_remote_retrieve_body( $ip_country_return );
        $ip_country_return_array = json_decode( $ip_country_respond_body, true );
        
        if( isset($ip_country_return_array['success']) && !$ip_country_return_array['success'] ){
            $data_to_return = '<p style="color: #FF0000;">ERROR: '.$ip_country_return_array['error']['type'].', '.$ip_country_return_array['error']['info'].'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
        }
        
        if( !isset($ip_country_return_array['country_code']) || !$ip_country_return_array['country_code'] ){
            $data_to_return = '<p style="color: #FF0000;">ERROR: query '.$ip_country_return_array['ip'].' failed.</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
        }
            
        $selected_country_message = '';
        $in_country = false;
        if( $selected_country ){
            $selected_country_array = explode( ',', $selected_country );
            if( in_array( $ip_country_return_array['country_code'], $selected_country_array ) ){
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #1abb25;">It is in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #1abb25;">It is in the country you added.</p>';
                }
                $in_country = true;
            }else{
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the country you added.</p>';
                }
                $in_country = false;
            }
        }
        
        $country_name = isset( $this->bsk_gfblcv_countries_code[$ip_country_return_array['country_code']] ) ? $this->bsk_gfblcv_countries_code[$ip_country_return_array['country_code']] : $ip_country_return_array['country_code'];
        
        $data_to_return = '<p style="color: #1abb25;">The IP address: <strong>'.$ip_address.'</strong> belongs to <strong>'.$country_name.'</strong>.</p>';
        $data_to_return .= $selected_country_message;
        
        return( array( 'result' => $in_country, 'html' => $data_to_return ) );
    }
    
    function ipgeolocation_io_api_response( $api_server, $ip_address, $api_key, $selected_country ){
        
        $args = array(
                        'method' => 'GET',
                        'timeout' => 60,
                     );

        $server_url = $this->bsk_gfblcv_server_api_list[$api_server]['api'];
        $server_url = str_replace( 'IP_ADDRESS', $ip_address, $server_url );
        $server_url = str_replace( 'API_KEY', $api_key, $server_url );

        $ip_country_return = wp_remote_post( $server_url, $args );
        if( is_wp_error( $ip_country_return ) ) {
			$error_message = $ip_country_return->get_error_message();
            $data_to_return = '<p style="color: #FF0000;">'.$error_message.'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
		}
        
        $ip_country_respond_body  = wp_remote_retrieve_body( $ip_country_return );
        $ip_country_return_array = json_decode( $ip_country_respond_body, true );
        
        if( isset($ip_country_return_array['message']) && !isset($ip_country_return_array['country_code2']) ){
            $data_to_return = '<p style="color: #FF0000;">ERROR: '.$ip_country_return_array['message'].'</p>';
            
            return( array( 'result' => false, 'html' => $data_to_return ) );
        }
        
        
        $selected_country_message = '';
        $in_country = false;
        if( $selected_country ){
            $selected_country_array = explode( ',', $selected_country );
            if( in_array( $ip_country_return_array['country_code2'], $selected_country_array ) ){
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #1abb25;">It is in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #1abb25;">It is in the country you added.</p>';
                }
                $in_country = true;
            }else{
                if( count($selected_country_array) > 1 ){
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the countries you added.</p>';
                }else{
                    $selected_country_message = '<p style="color: #ff5b00;">It isn\'t in the country you added.</p>';
                }
            }
        }
        
        $country_name = isset( $this->bsk_gfblcv_countries_code[$ip_country_return_array['country_code2']] ) ? $this->bsk_gfblcv_countries_code[$ip_country_return_array['country_code2']] : $ip_country_return_array['country_code2'];
        
        $data_to_return = '<p style="color: #1abb25;">The IP address: <strong>'.$ip_address.'</strong> belongs to <strong>'.$country_name.'</strong>.</p>';
        $data_to_return .= $selected_country_message;
        
        return( array( 'result' => $in_country, 'html' => $data_to_return ) );
    }
    
    function validate_ip_in_country( $list_data, $ip_address ){
        
        $api_server = false;
        $api_key = false;
        $selected_country = false;
        
        if( isset( $list_data['api_server'] ) ){
            $api_server = $list_data['api_server'];
        }
        
        if( isset( $list_data['api_key'] ) ){
            $api_key = $list_data['api_key'];
        }
        
        if( isset( $list_data['country'] ) ){
            $selected_country = $list_data['country'];
        }

        if( !$api_server || $selected_country == '' ){
            return false;
        }
        
        if( $api_server != 'ip-api.com.free' && $api_key == '' ){
            return false;
        }

        $data_to_return = false;
        if( $api_server == 'ipinfodb.com' ){
            $response_array = $this->ipinfodb_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['result'];
        }else if( $api_server == 'ip2location.com' ){
            $response_array = $this->ip2location_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['result'];
        }else if( $api_server == 'ip-api.com.free' ){
            $response_array = $this->ip_api_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['result'];
        }else if( $api_server == 'ipstack.com' ){
            $response_array = $this->ipstack_com_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['result'];
        }else if( $api_server == 'ipgeolocation.io' ){
            $response_array = $this->ipgeolocation_io_api_response( $api_server, $ip_address, $api_key, $selected_country );
            $data_to_return = $response_array['result'];
        }
        
        return $data_to_return;
    }
    
    function get_countrys_name_by_code( $countrys_code ){
        if( !$countrys_code || !is_array( $countrys_code ) || count( $countrys_code ) < 1 ){
            return '';
        }
        
        $countrys_name_array = array();
        foreach( $countrys_code as $country_code ){
            $countrys_name_array[] = $this->bsk_gfblcv_countries_code[$country_code];
        }
        
        if( count($countrys_name_array) < 1 ){
            return '';
        } 
        
        return ( implode( ', ', $countrys_name_array ) );
    }
}
