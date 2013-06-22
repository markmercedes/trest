<?php

/**
 * Base class for the {@link TRestRequest}
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 * @package TRest\Http
 */
namespace TRest\Http;

class TRestRequestProperties {

    /**
     *
     * @var string the api url
     */
    protected $url;

    /**
     *
     * @var string the api resource e.g: api_url/<$resource> =>
     *      http://myapi.com/myresource
     */
    protected $resource;

    /**
     *
     * @var string the api resource e.g: api_url/resource/<$path> =>
     *      http://myapi.com/myresource/mypath
     */
    protected $path;

    /**
     *
     * @var {@link TRestClient method} The HTTP method that should be executed,
     *      supported types are: TRestClient::GET, TRestClient::POST,
     *      TRestClient::PUT, TRestClient::DELETE
     */
    protected $method = TRestClient::GET;

    /**
     *
     * @var array() the query string that will be sent in the request
     */
    protected $parameters = array();

    /**
     *
     * @var mixed representation of the entity that will be sent with PUT or
     *      POST, this is done automatically by the Model that handles an
     *      specific resource
     */
    protected $entity;

    /**
     *
     * @var string username if the server requires any username
     */
    protected $username;

    /**
     *
     * @var string password if the server requires any password
     */
    protected $password;

    /**
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     *
     * @param string $path            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getEntity() {
        return $this->entity;
    }

    /**
     *
     * @param mixed $entity            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setEntity($entity) {
        $this->entity = $entity;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     *
     * @param unknown $resource            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setResource($resource) {
        $this->resource = $resource;
        return $this;
    }

    /**
     *
     * @param string $url            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setUrl($url) {
        $this->url = $url;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     *
     * @param
     *            {@link TRestClient} $method
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setMethod($method) {
        $this->method = $method;
        return $this;
    }

    /**
     *
     * @return TRestClient http method
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     *
     * @param array $parameters            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setParameters($parameters) {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     *
     * @return array with valid parameters
     */
    public function getParameters() {
        return (array_filter($this->parameters, function ($element) {
            return $element ? true : false;
        }));
    }

    /**
     *
     * @param string $parameter            
     * @param string $value            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setParameter($parameter, $value) {
        $this->parameters[$parameter] = $value;
        return $this;
    }

    /**
     *
     * @param string $parameter            
     * @return array
     */
    public function getParameter($parameter) {
        return $this->parameters[$parameter];
    }

    /**
     *
     * @param string $username            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setUsername($username) {
        $this->username = $username;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     *
     * @param string $password            
     * @return \TRest\Http\TRestRequestProperties
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }
}