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

        foreach($fields as $field) {
            $from = $field['from'];
            $to = $field['to'];
            $item = $this->sendRequest($bynder, $item, $from, $to, $array_location);
        }

        return $item;
    }

    public function sendRequest(array $bynder, array $item, string $from, string $to, array $array_location)
    {
        if(!isset($item[$from])) {
            return $item;
        }

        // Trim /download
        $code = str_replace('/download', '', $item[$from]);
        // Trim until last /
        $code = preg_replace('/^.*\//', '', $code);

        // Get the code from the item
        $code = $bynder['url'] . $code;

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $code); // API endpoint
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

                foreach ($array_location as $location) {
                    $image = null;
                    if(isset($response_array[$location])) {
                        $image = $response_array[$location];
                        $response_array = $response_array[$location];
                    }
                }

                $item[$to] = $image;
            }
            return $item;

        }

        // Close the cURL session
        curl_close($ch);
        return $item;
    }
}
