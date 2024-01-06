<?php

namespace Leysco100\Payments\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

trait MpesaTrait
{
    /**
     * The Base URL
     *
     * @var string
     */
    public $url;

    public function __construct()
    {
        $this->url = 'https://sandbox.safaricom.co.ke';
    }

    // Generate an AccessToken using the Consumer Key and Consumer Secret
    public function generateAccessToken()
    {
        $consumer_key = "MmPJGiQ9p1HXVLw6czUhyPJmaQ2C3RrW";
        $consumer_secret = "8ueKrPJLLJhssnGj";

        $url = $this->url . '/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth($consumer_key, $consumer_secret)
            ->get($url);

        $result = json_decode($response);

        return data_get($result, 'access_token');
    }

    // Common Format Of The Mpesa APIs.
    public function MpesaRequest($url, $body)
    {

        $response = Http::withToken($this->generateAccessToken())
            ->acceptJson()
            ->post($url, $body);

        return $response;
    }

    // Generate a base64  password using the Safaricom PassKey and the Business ShortCode to be used in the Mpesa Transaction
    public function LipaNaMpesaPassword()
    {

        $timestamp = Carbon::rawParse('now')->format('YmdHms');
        //passkey
        $passKey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $businessShortCOde = 174379;
        //generate password
        $mpesaPassword = base64_encode($businessShortCOde . $passKey . $timestamp);

        return $mpesaPassword;
    }

    public function phoneValidator($phoneno)
    {
        // Some validations for the phonenumber to format it to the required format
        $phoneno = (substr($phoneno, 0, 1) == '+') ? str_replace('+', '', $phoneno) : $phoneno;
        $phoneno = (substr($phoneno, 0, 1) == '0') ? preg_replace('/^0/', '254', $phoneno) : $phoneno;
        $phoneno = (substr($phoneno, 0, 1) == '7') ? "254{$phoneno}" : $phoneno;

        return $phoneno;
    }

    public function generate_security_credential()
    {
        return;
        if (config('mpesa.environment') == 'sandbox') {
            $pubkey = File::get(__DIR__ . '/../certificates/SandboxCertificate.cer');
        } else {
            $pubkey = File::get(__DIR__ . '/../certificates/ProductionCertificate.cer');
        }
        openssl_public_encrypt(config('mpesa.initiator_password'), $output, $pubkey, OPENSSL_PKCS1_PADDING);

        return base64_encode($output);
    }

    public function validationResponse($result_code, $result_description)
    {
        $result = json_encode([
            'ResultCode' => $result_code,
            'ResultDesc' => $result_description,
        ]);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->setContent($result);

        return $response;
    }
}
