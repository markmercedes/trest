<?php

/**
 * Class that handles the abstraction of a HTTP Request to a REST web service.
 *
 * @author Marcos Mercedes <marcos.mercedesn@gmail.com>
 */
namespace TRest\Http;

class TRestRequest extends TRestRequestProperties {

    public function getUrlHash() {
        return md5($this->buildUrl() . '?' . implode('&', $this->getParameters()));
    }

    public function buildUrl($addParameters = false) {
        $array = array(
            rtrim($this->getUrl(),'/'),
            $this->getResource()
        );
        if ($this->getPath())
            $array[] = $this->getPath();
        if ($addParameters) {
            if (count($this->getParameters()))
                $array[] = '?' . http_build_query($this->getParameters());
            array_walk($array, function (&$item, $key) {
                $item = rtrim($item, '/');
            });
        }
        return implode('/', $array);
    }
}