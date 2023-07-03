<?php

namespace KingbirdFastlog;

use COption;
use KingbirdFastlog\exceptions\ConfigException;
use KingbirdFastlog\exceptions\Error;
use KingbirdFastlog\exceptions\RuntimeException;

class Sender
{
    /** @var Config */
    private $config;

    const ENV_URL = 'FASTLOGS_URL';
    const ENV_SLUG = 'FASTLOGS_SLUG';

    public function __construct($slug = null, $url = null)
    {
        if (is_null($slug)) {
            $slug = COption::GetOptionString("fastlogs-sdk-bitrix", "slug");
        }
        if (is_null($slug)) {
            throw new ConfigException(
                sprintf(Error::PARAMETER_NOT_DEFINED_MESSAGE, 'slug', static::ENV_SLUG),
                Error::PARAMETER_SLUG_NOT_DEFINED_CODE
            );
        }

        if (is_null($url)) {
            $url = COption::GetOptionString("fastlogs-sdk-bitrix", "urlAPI");
        }

        if (is_null($url)) {
            throw new ConfigException(
                sprintf(Error::PARAMETER_NOT_DEFINED_MESSAGE, 'url', static::ENV_URL),
                Error::PARAMETER_URL_NOT_DEFINED_CODE
            );
        }

        if (substr($url, -1) === '/') {
            $url = substr($url, 0, -1);
        }

        $this->config = new Config($slug, $url);
    }

    /**
     * @param mixed $data
     * @param string $slug
     * @throws RuntimeException
     */
    public function add($data, $slug = null, $time = null)
    {
        $slug = (null === $slug) ? $this->config->getSlug() : $slug;

        $preparedData = $this->prepareData($data);

        $this->send($slug, $preparedData, $time);
    }

    /**
     * @param $slug
     * @param $data
     * @return void
     * @throws RuntimeException
     */
    protected function send($slug, $data, $time)
    {
        $body = new \stdClass();
        $body->bucket = $slug;
        $body->data = $data;

        if (!is_null($time)){
            $body->mlTimestamp = $time;
        }

        $ch = curl_init();

        $chOptions = [
            CURLOPT_URL => $this->config->getUrl() . '/api/write',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($body)
        ];

        curl_setopt_array($ch, $chOptions);

        $jsonResponse = curl_exec($ch);

        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (200 === $httpStatusCode) {
            return;
        }

        $response = json_decode($jsonResponse);

        throw new RuntimeException($response->error->description, $response->error->code);
    }

    /**
     * @param mixed $data
     * @return string
     * @throws RuntimeException
     */
    protected function prepareData($data)
    {
        $type = gettype($data);

        $preparedData = null;

        switch ($type) {
            case 'string':
                $jsonObject = json_decode($data);
                if (JSON_ERROR_NONE === json_last_error()) {
                    return $data;
                }
                unset($jsonObject);

            case 'integer':
            case 'double':
            case 'boolean':
            case 'null':
                $preparedData = [
                    'value' => $data
                ];
                break;

            case 'object':
            case 'array':
                $preparedData = $data;
                break;

            case 'resource':
            case 'resource (closed)':
            case 'unknown type':
            default:
                throw new RuntimeException(
                    Error::RESOURCE_TYPE_MESSAGE,
                    Error::RESOURCE_TYPE_CODE
                );
        }

        $result = json_encode($preparedData, JSON_UNESCAPED_UNICODE);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $result;
        }

        throw new RuntimeException(
            Error::UNCONVERTED_INPUT_MESSAGE,
            Error::UNCONVERTED_INPUT_CODE
        );
    }
}
