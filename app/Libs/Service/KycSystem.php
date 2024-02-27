<?php

namespace App\Libs\Service;

use App\Exceptions\ApiCallException;
use App\Libs\ApiCaller\ItsazanApiCenter;
use App\Libs\ApiCaller\SanbadApiCenter;
use Illuminate\Support\Facades\Log;

class KycSystem
{

    public $provider = "itsaz";
    public $apiCenter;

    public function __construct($provider = "sanbad")
    {
        $this->provider = $provider;

        if ($this->provider == "sanbad") {
            $this->apiCenter = new SanbadApiCenter();
        }
        if ($this->provider == "itsaz") {
            $this->apiCenter = new ItsazanApiCenter();
        }
    }

    public function shahkar($ssn, $mobile, $orderid)
    {
        if ($this->provider == "sanbad") {
            try {
                $resultBase = $this->apiCenter->VerifyMobile($ssn, $mobile, "LOG-" . $orderid);

                $result = $resultBase->getBody()->getContents();
                $result = \GuzzleHttp\json_decode($result);

                return ['data' => $result, 'match' => ($result->message->ismatched == 1) ? 1 : 0];

            } catch (\Exception $e) {
                $error = json_decode($e->getMessage());
                Log::error("----------------------------------- MOBILE");
                Log::error("KYC ID:" . $orderid);
                Log::error(json_encode([$ssn, $ssn]));
                Log::error($e->getMessage());
                Log::error("============");
                return false;


            }
        }
        if ($this->provider == "itsaz") {
            try {
                $resultBase = $this->apiCenter->VerifyMobile($ssn, $mobile, "LOG-" . $orderid);
                return ['data' => $resultBase, 'match' => ($resultBase->data) ? 1 : 0];
            } catch (\Exception $e) {
                dd($e);
                $error = json_decode($e->getMessage());
                Log::error("----------------------------------- MOBILE");
                Log::error("KYC ID:" . $orderid);
                Log::error(json_encode([$ssn, $ssn]));
                Log::error($e->getMessage());
                Log::error("============");
                return false;
            }
        }

    }


    public function personPhoto($ssn, $birthday)
    {
        try {
            $resultBase = $this->apiCenter->personPhoto($ssn, $birthday);
            return $resultBase->data;
        } catch (ApiCallException $e) {
            return 0;
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function civil($ssn, $birthday, $orderid)
    {

        if ($this->provider == "sanbad") {
            try {
                $resultBase = $this->apiCenter->civilRegistryInfo($ssn, $birthday, "Log-" . $orderid);
                $result = $resultBase->getBody()->getContents();
                $result = \GuzzleHttp\json_decode($result);
                /*if (count($result->message->images) > 0) {
                    $obj = new \stdClass();
                    $obj->data = $result->message->images[0]->image;
                    $obj->meta = null;
                    $kycData->photo_data = json_encode($obj);
                    $kycData->save();
                    $validateData->photo = 1;
                    $validateData->save();

                    $code = uniqid();
                    try {
                        $apiPresent = new ApiCenterPresent();
                        $registerPhoto = $apiPresent->registerUserImage($code, $result->data);
                        $kyc->photoId = $registerPhoto->statusMessage;
                        $kyc->save();
                    } catch (\Exception $e) {
                        report($e);
                    }

                }*/
                return $result;
            } catch (ApiCallException $e) {
//                report($e);

                return 0;
            } catch (\Exception $e) {
//                report($e);
                return -1;
            }

        }
        if ($this->provider == "itsaz") {
            try {
                $resultBase = $this->apiCenter->civilRegistryInfo($ssn, $birthday, "Log-" . $orderid);
                $result = $resultBase->getBody()->getContents();
                $result = \GuzzleHttp\json_decode($result);
                return $result;
            } catch (ApiCallException $e) {
//                report($e);
                return 0;

            } catch (\Exception $e) {
//                report($e);
                return -1;
            }
        }
    }


    public function car($nationalCode, $mobile, $plate, $orderId, $kycCar)
    {
        $this->apiCenter = new SanbadApiCenter();

        try {
            $resultBase = $this->apiCenter->car($nationalCode, $mobile, $plate, $orderId);

            $result = $resultBase->getBody()->getContents();
            $result = \GuzzleHttp\json_decode($result);


            if ($result === false) {
                $kycCar->response_data = json_encode($result);
                $kycCar->plate_text = $result->message->plateWord;
                $kycCar->is_verified = 1;
                $kycCar->save();
            } else {
                $kycCar->is_verified = 0;
                $kycCar->save();
            }
        } catch (ApiCallException $e) {
            $kycCar->is_verified = 0;
            $kycCar->save();

            return 0;
        } catch (\Exception $e) {
            $kycCar->is_verified = 0;
            $kycCar->save();

            return -1;

        }
    }
}
