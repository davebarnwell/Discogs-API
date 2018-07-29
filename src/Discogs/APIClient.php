<?php
/**
 * Created by PhpStorm.
 * User: dave
 * Date: 30/04/2017
 * Time: 16:39
 */

namespace davebarnwell\Discogs;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class APIClient
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $username;


    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    private $responseJSON;


    /**
     * @var Client
     */
    private $client;

    /**
     * APIClient constructor.
     *
     * @param string|null $token
     */
    public function __construct(string $token = null)
    {
        if ($token) {
            $this->setToken($token);
        }
        $this->clientFactory();
    }

    /**
     * @param string $token
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     *
     */
    private function clientFactory()
    {
        $this->client = new Client();
    }

    /**
     * @param string $method
     * @param string $url
     *
     * @param string $body
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendRequest(string $method, string $url, string $body = '')
    {
        $request        = new Request(
            $method,
            $url,
            $this->getRequestOptions(),
            $body
        );
        $this->response = $this->client->send($request);
        $this->decodeAndSaveJSONBody();
        return $this->response;
    }

    /**
     * @return array
     */
    private function getRequestOptions(): array
    {
        /**
         * @var string[string]
         */
        $headers = [
            'http_errors' => true   // Ensure Exception on 4xx and 5xx
        ];
        if ($this->token) {
            $headers['Authorization'] = sprintf('Discogs token=%s', $this->token);
        }
        return $headers;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * Get first page of items stored in the users all collection
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUsersCollectionAllReleases()
    {
        $this->sendRequest(
            'GET',
            sprintf('https://api.discogs.com/users/%s/collection/folders/%d/releases', $this->username, 0)
        );
        return $this->getJSONBody();
    }


    /**
     * decode last queries body as json and cache it
     */
    private function decodeAndSaveJSONBody() {
        $this->responseJSON = json_decode($this->response->getBody()->getContents(), true);
    }

    /**
     * @return array
     */
    public function getJSONBody()
    {
        return $this->responseJSON;
    }

    /**
     * @return bool
     */
    public function hasNextPage() : bool
    {
        return $this->getNextPageUrl() !== null;
    }

    /**
     * @return null|string
     */
    private function getNextPageUrl() {
        if (!isset($this->getJSONBody()['pagination']['urls']['next'])) {
            return null;
        }
        return $this->getJSONBody()['pagination']['urls']['next'];
    }

    /**
     * @return array
     * @throws NoNextPageException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getNextPage()
    {
        $nextPageUrl = $this->getNextPageUrl();
        if ($nextPageUrl === null) {
            throw new NoNextPageException();
        }
        $this->sendRequest('GET',$nextPageUrl);
        return $this->getJSONBody();
    }
}