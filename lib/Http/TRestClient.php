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

    private function setPostFields($ch, $request, $method) {
        $entityProperties = $request->getEntity();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $entityProperties);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($entityProperties)
        ));
        return $ch;
    }

    private function setCurlMethod($ch, TRestRequest $request) {
        $method = $request->getMethod();
        switch ($method) {
            case self::POST :
            case self::PUT :
                $ch = $this->setPostFields($ch, $request, $method);
                break;
            case self::DELETE :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
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
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status >= 400) {
            curl_close($ch);
            throw new \Exception($result, $status);
        }
        curl_close($ch);
        return json_decode($result);
    }
}
