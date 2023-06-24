<?php

namespace Leysco100\Gpm\Http\Controllers;

use Illuminate\Http\Request;
use Leysco100\Gpm\Http\Controllers\Controller;


class AfricasTalkingGatewayException extends \Exception
{
}
class AfricasTalkingApi extends Controller
{
    protected $_username;
    protected $_apiKey;
    protected $_requestBody;
    protected $_requestUrl;
    public $_responseBody;
    protected $_responseInfo;
    public $_environment;
    //Turn this on if you run into problems. It will print the raw HTTP response from our server
    const Debug             = false;
    const HTTP_CODE_OK      = 200;
    const HTTP_CODE_CREATED = 201;
    public function __construct($username_, $apiKey_, $environment_ = "production")
    {
        $this->_username     = $username_;
        $this->_apiKey       = $apiKey_;
        $this->_environment  = $environment_;
        $this->_requestBody  = null;
        $this->_requestUrl   = null;
        $this->_responseBody = null;
        $this->_responseInfo = null;
    }

    //Messaging methods
    public function sendMessage($to_, $message_, $from_ = null, $bulkSMSMode_ = 1, array $options_ = array())
    {
        if (strlen($to_) == 0 || strlen($message_) == 0) {
            throw new AfricasTalkingGatewayException('Please supply both to and message parameters');
        }
        $params = array(
            'username' => $this->_username,
            'to'       => $to_,
            'message'  => $message_,
        );
        if ($from_ !== null) {
            $params['from']        = $from_;
            $params['bulkSMSMode'] = $bulkSMSMode_;
        }

        //This contains a list of parameters that can be passed in $options_ parameter
        if (count($options_) > 0) {
            $allowedKeys = array(
                'enqueue',
                'keyword',
                'linkId',
                'retryDurationInHours'
            );

            //Check whether data has been passed in options_ parameter
            foreach ($options_ as $key => $value) {
                if (in_array($key, $allowedKeys) && strlen($value) > 0) {
                    $params[$key] = $value;
                } else {
                    throw new AfricasTalkingGatewayException("Invalid key in options array: [$key]");
                }
            }
        }

        $this->_requestUrl  = $this->getSendSmsUrl();
        $this->_requestBody = http_build_query($params, '', '&');
        $this->executePOST();
        if ($this->_responseInfo['http_code'] == self::HTTP_CODE_CREATED) {
            $responseObject = json_decode($this->_responseBody);
            if (count($responseObject->SMSMessageData->Recipients) > 0)
                return $responseObject->SMSMessageData->Recipients;
            throw new AfricasTalkingGatewayException($responseObject->SMSMessageData->Message);
        }
        throw new AfricasTalkingGatewayException($this->_responseBody);
    }


    //Call methods
    public function call($from_, $to_)
    {
        if (strlen($from_) == 0 || strlen($to_) == 0) {
            throw new AfricasTalkingGatewayException('Please supply both from and to parameters');
        }

        $params = array(
            'username' => $this->_username,
            'from'     => $from_,
            'to'       => $to_
        );

        $this->_requestUrl  = $this->getVoiceUrl() . "/call";
        $this->_requestBody = http_build_query($params, '', '&');

        $this->executePOST();

        if (($responseObject = json_decode($this->_responseBody)) !== null) {
            if (strtoupper(trim($responseObject->errorMessage)) == "NONE") {
                return $responseObject->entries;
            }
            throw new AfricasTalkingGatewayException($responseObject->errorMessage);
        } else
            throw new AfricasTalkingGatewayException($this->_responseBody);
    }

    private function executeGet()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'apikey: ' . $this->_apiKey
        ));
        $this->doExecute($ch);
    }

    private function executePost()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_requestBody);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'apikey: ' . $this->_apiKey
        ));

        $this->doExecute($ch);
    }

    private function executeJsonPost()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_requestBody);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($this->_requestBody),
            'apikey: ' . $this->_apiKey
        ));
        $this->doExecute($ch);
    }

    private function doExecute(&$curlHandle_)
    {
        try {

            $this->setCurlOpts($curlHandle_);
            $responseBody = curl_exec($curlHandle_);

            if (self::Debug) {
                echo "Full response: " . print_r($responseBody, true) . "\n";
            }

            $this->_responseInfo = curl_getinfo($curlHandle_);

            $this->_responseBody = $responseBody;
            curl_close($curlHandle_);
        } catch (\Exception $e) {
            curl_close($curlHandle_);
            throw $e;
        }
    }

    private function setCurlOpts(&$curlHandle_)
    {
        curl_setopt($curlHandle_, CURLOPT_TIMEOUT, 60);
        curl_setopt($curlHandle_, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curlHandle_, CURLOPT_URL, $this->_requestUrl);
        curl_setopt($curlHandle_, CURLOPT_RETURNTRANSFER, true);
    }

    private function getApiHost()
    {
        return ($this->_environment == 'sandbox') ? 'https://api.sandbox.africastalking.com' : 'https://api.africastalking.com';
    }

    private function getVoiceHost()
    {
        return ($this->_environment == 'sandbox') ? 'https://voice.sandbox.africastalking.com' : 'https://voice.africastalking.com';
    }

    private function getSendSmsUrl($extension_ = "")
    {
        return $this->getApiHost() . '/version1/messaging' . $extension_;
    }

    private function getVoiceUrl()
    {
        return $this->getVoiceHost();
    }

    private function getUserDataUrl($extension_)
    {
        return $this->getApiHost() . '/version1/user' . $extension_;
    }

    private function getSubscriptionUrl($extension_)
    {
        return $this->getApiHost() . '/version1/subscription' . $extension_;
    }

    private function getAirtimeUrl($extension_)
    {
        return $this->getApiHost() . '/version1/airtime' . $extension_;
    }

    private function getMobilePaymentCheckoutUrl()
    {
        return $this->getApiHost() . '/payment/mobile/checkout/request';
    }

    private function getMobilePaymentB2CUrl()
    {
        return $this->getApiHost() . '/payment/mobile/b2c/request';
    }
}
