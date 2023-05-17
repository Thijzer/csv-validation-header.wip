<?php

namespace Misery\Component\Action;

use Misery\Component\Common\Options\OptionsInterface;
use Misery\Component\Common\Options\OptionsTrait;
use function _PHPStan_76800bfb5\regex;

class GetImageFromBynderAction implements OptionsInterface
{
    use OptionsTrait;
    public const NAME = 'get_image_from_bynder';

    /** @var array */
    private $options = [
        'fields' => [],
        'bynder_url' => 'https://demo.getbynder.com/api/v4/media/',
        'bynder_token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        'bynder_cookieid' => 'xxxxxxxx-xxxx-xxxx-xxxxxxxxxxxxxxxx',
        'array_location' => [ 'original' ],
    ];

    public function apply(array $item): array
    {
        $fields = $this->options['fields'];
        $bynder_url = $this->options['bynder_url'];
        $bynder_token = $this->options['bynder_token'];
        $bynder_cookieid = $this->options['bynder_cookieid'];
        $array_location = $this->options['array_location'];

        $bynder = [
            'url' => $bynder_url,
            'token' => $bynder_token,
            'cookieid' => $bynder_cookieid,
        ];

        // validation
        if (!isset($fields) || $fields === []) {
            return $item;
        }

        $item = $this->sendRequest($bynder, $item, $fields, $array_location);

        return $item;
    }

    public function sendRequest(array $bynder, array $item, array $fields, array $array_location)
    {
        if(!isset($fields) || !is_array($fields['from']) || !is_string($fields['to'])) {
            return $item;
        }

        $fullcodeArray = [];
        foreach($fields['from'] as $field) {
            if(!isset($item[$field])) {
                continue;
            }

            // Trim /download
            $code = str_replace('/download', '', $item[$field]);
            // Trim until last /
            $code = preg_replace('/^.*\//', '', $code);

            if($code === '') {
                continue;
            }
            $fullcodeArray[] = $code;

        }

        // Get the code from the item
        $fullcode = $bynder['url'] . implode(',', $fullcodeArray);
        $separator = $fields['separator'] ?? ',';

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $fullcode); // API endpoint
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
        //curl_setopt($ch, CURLOPT_POST, true); // Use HTTP POST method

        // Add Authorization header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $bynder['token']
        ));

        // Add Cookie header
        $cookie = 'DEFAULTLOCALE=en_US; bynder=' . $bynder['cookieid'];
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for errors
        if(curl_error($ch)) {
            echo 'Error: ' . curl_error($ch);
        } else {
            // Decode the response JSON string into an array
            $response_array = json_decode($response, true);

            // Check if the API returned a success status code
            if(isset($response_array['statuscode']) && $response_array['statuscode'] == '400') {
                // Close the cURL session
                curl_close($ch);
                return $item;
            }

            // Get the media item from the response
            if(isset($response_array)) {

                $images = [];
                foreach($response_array as $response) {
                    foreach ($array_location as $location) {
                        $image = null;
                        if(isset($response[$location])) {
                            $image = $response[$location];
                            $response = $response[$location];
                        }
                    }
                    $images[] = $image;
                }
                $item[$fields['to']] = implode($separator, $images);

            }

        }

        // Close the cURL session
        curl_close($ch);
        return $item;
    }
}
