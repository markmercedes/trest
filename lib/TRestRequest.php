<?php

/**
 * Class that handles the abstraction of a HTTP Request to a REST web service.
 *
 * @author    Marcos Mercedes <marcos.mercedesn@gmail.com>
 */

class TRestRequest {

    private $url;

    private $resource;

    private $path;

    private $method = TRestClient::GET;

    private $parameters = array();

    private $username;

    private $password;

    private $responseType = 'json';

    private $apiKey;

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    public function getResource() {
        return $this->resource;
    }

    public function setResource($resource) {
        $this->resource = $resource;
        return $this;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    public function getMethod() {
        return $this->method;
    }

    public function setParameters($parameters) {
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameters() {
        return $this->parameters;
    }
    
    public function setParameter($parameter, $value) {
        $this->parameters[$parameter] = $value;
        return $this;
    }
    
    public function getParameter($parameter) {
        return $this->parameters[$parameter];
    }

    public function setResponseType($responseType) {
        $this->responseType = $responseType;
        return $this;
    }

    public function getResponseType() {
        return $this->responseType;
    }

    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    public function getPassword() {
        return $this->password;
    }

    public function buildUrl() {
        $array = array(
            $this->getUrl(),
            $this->getResource()
        );
        if ($this->getPath())
            $array[] = $this->getPath();
        if (count($this->getParameters()))
            $array[] = '?' . http_build_query($this->getParameters());
        array_walk($array, function (&$item, $key) {
            $item = rtrim($item, '/');
        });
        return implode('/', $array);
    }
}