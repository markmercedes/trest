<?php

/**
 * Class for HTTP requests via curl
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Http
 */
namespace TRest\Http;

class Client {

    const POST = 'POST';

    const GET = 'GET';

    const PUT = 'PUT';

    const DELETE = 'DELETE';

    /**
     * 
     * HTTP POST
     * 
     * @param Request $request
     * @return mixed
     */
    public function post(Request $request) {
        return $this->execute($request->setMethod(self::POST));
    }

    /**
     * 
     * HTTP GET
     * 
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request) {
        return $this->execute($request->setMethod(self::GET));
    }

    /**
     * 
     * HTTP PUT
     * 
     * @param Request $request
     * @return mixed
     */
    public function put(Request $request) {
        return $this->execute($request->setMethod(self::PUT));
    }

    /**
     * 
     * HTTP DELETE
     * 
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request) {
        return $this->execute($request->setMethod(self::DELETE));
    }

    /**
     * 
     * creates a curl resource with the parameters provided by the {@link Request} object
     * 
     * @param Request $request
     * @return curl resource
     */
    private function getCurlInstance(Request $request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request->buildUrl(true, ($request->getMethod() == 'GET')));
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

    /**
     * 
     * sets the POST/PUT fields to the curl resource
     * 
     * @param curl resource $ch
     * @param Request $request
     * @return curl resource
     */
    private function setPostFields($ch, Request $request) {
        $entityProperties = $request->getEntity();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $entityProperties);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($entityProperties)
        ));
        return $ch;
    }

    /**
     * 
     * sets curl method to the curl resource handled by the request
     * 
     * @param curl resource $ch
     * @param Request $request
     * @return curl resource
     */
    private function setCurlMethod($ch, Request $request) {
        switch ($request->getMethod()) {
            case self::POST :
            case self::PUT :
                $ch = $this->setPostFields($ch, $request);
                break;
            case self::DELETE :
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
                break;
            case self::GET :
            default :
                break;
        }
        return $ch;
    }

    /**
     * 
     * Executes http request 
     * 
     * @param Request $request
     * @throws \Exception if the response http code is 400 
     * @return mixed web service response
     */
    public function execute(Request $request) {
        $ch = $this->setCurlMethod($this->getCurlInstance($request), $request);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status >= 400) {
            curl_close($ch);
            return null;
        }
        curl_close($ch);
        return json_decode($result);
    }
}
