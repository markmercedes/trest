<?php

/**
 * Class that wrappes HTTP requests via curl
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */
namespace TRest\Http;

class TRestClient {

    const POST = 'POST';

    const GET = 'GET';

    const PUT = 'PUT';

    const DELETE = 'DELETE';

    public function post(TRestRequest $request) {
        return $this->execute($request->setMethod(self::POST));
    }

    public function get(TRestRequest $request) {
        return $this->execute($request->setMethod(self::GET));
    }

    public function put(TRestRequest $request) {
        return $this->execute($request->setMethod(self::PUT));
    }

    public function delete(TRestRequest $request) {
        return $this->execute($request->setMethod(self::DELETE));
    }

    private function getCurlInstance(TRestRequest $request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->buildUrl());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json',
            'Accept-Charset: utf-8'
        ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Expect:'
        ));
        if ($request->getUsername() && $request->getPassword()) {
            curl_setopt($ch, CURLOPT_USERPWD, $request->getUsername() . ':' . $request->getPassword());
        }
        return $ch;
    }

    private function setCurlMethod($ch, TRestRequest $request) {
        switch ($request->getMethod()) {
            case self::POST :
            case self::PUT :
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request->getParameters()));
                break;
            case self::DELETE :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            case self::GET :
            default :
                break;
        }
        return $ch;
    }

    public function execute(TRestRequest $request) {
        $ch = $this->setCurlMethod($this->getCurlInstance($request), $request);
        $result = curl_exec($ch);
        if (! $result) {
            $errorNumber = curl_errno($ch);
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception($errorNumber . ': ' . $error);
        }
        curl_close($ch);
        return json_decode($result);
    }
}
