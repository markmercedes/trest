<?php

namespace TRest\Http;

class TRestRequestProperties {

    protected $url;

    protected $resource;

    protected $path;

    protected $method = TRestClient::GET;

    protected $parameters = array();

    protected $username;

    protected $password;

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
        if ($value)
            $this->parameters[$parameter] = $value;
        return $this;
    }

    public function getParameter($parameter) {
        return $this->parameters[$parameter];
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
}