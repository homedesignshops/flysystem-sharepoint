<?php

// TODO: Implement Microsoft Docs into this class

namespace Homedesignshops\FlysystemSharepoint;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Http\GraphRequest;
use Microsoft\Graph\Model\Drive;
use Microsoft\Graph\Model\DriveItem;
use Microsoft\Graph\Model\Group;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class SharepointClient
{

    /**
     * Max chunk size is 4 MB.
     *
     * @see https://docs.microsoft.com/en-us/graph/api/driveitem-put-content?view=graph-rest-1.0&tabs=http
     */
    protected const MAX_CHUNK_SIZE = 1024 * 1024 * 4;

    protected string $tenantId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $sharepointGroupName;
    protected string $drivePath;

    protected Client $client;
    protected Graph $graph;
    protected string $baseUri = 'https://login.microsoftonline.com/';
    protected string $graphResource = 'https://graph.microsoft.com/';

    protected string $accessToken;

    protected int $maxChunkSize;

    protected LoggerInterface $logger;

    /**
     * The first string replacer is the tenant id.
     *
     * @var string
     */
    protected string $tokenUri = '%s/oauth2/token?api-version=1.0';

    /**
     * @param string $tenantId
     * @param string $clientId
     * @param string $clientSecret
     * @param string $sharepointGroupName
     * @param int $maxChunkSize
     * @param Graph|null $graph
     * @throws GuzzleException
     */
    public function __construct(string $tenantId, string $clientId, string $clientSecret, string $sharepointGroupName, int $maxChunkSize = self::MAX_CHUNK_SIZE, Graph $graph = null)
    {
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => $this->baseUri
        ]);
        $this->sharepointGroupName = $sharepointGroupName;
        $this->maxChunkSize = ($maxChunkSize < self::MAX_CHUNK_SIZE ? ($maxChunkSize > 1 ? $maxChunkSize : 1) : self::MAX_CHUNK_SIZE);

        $this->logger = new NullLogger();

        try {
            $this->graph = $graph ?? new Graph();
            $this->refreshAccessToken();
            $this->applyAccessToken($this->accessToken);
            $this->setDrivePath($this->getGroupByName($sharepointGroupName));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }

    }

    /**
     * Set the log handler.
     *
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Creates a new file with the contents provided in the request.
     *
     * Returns a DriveItem if the item was uploaded successfully and false if not.
     *
     * @param string $path
     * @param string|resource $contents
     * @return DriveItem|bool
     */
    public function upload(string $path, $contents)
    {
        if($this->shouldUploadChunked($contents)) {
            $this->logger->info('File "' . $path . '" should be uploaded as chunk. Not implemented.');
            return false;
        }

        try {
            $request = $this->graph->createRequest('PUT', $this->drivePath.'/root:/'.$path.':/content')
                ->addHeaders(['Content-Type' => 'text/csv'])
                ->attachBody($contents)
                ->setReturnType(DriveItem::class);

            $driveItem = $this->executeRequest($request);

        } catch (GuzzleException | GraphException $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            return false;
        }

        return $driveItem;
    }

    /**
     * Returns an access token for the Graph API.
     *
     * @return mixed
     * @throws UnableToGetAccessToken
     */
    public function getGraphAccessToken()
    {
        try {
            $token = json_decode($this->client->post($this->getTokenUri(), [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'resource' => $this->graphResource,
                    'grant_type' => 'client_credentials'
                ],
            ])->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
        } catch (GuzzleException | \JsonException | \Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
            throw new UnableToGetAccessToken($e->getMessage(), $e->getCode(), $e->getTrace());
        }

        return $token->access_token;
    }

    /**
     * The file should be uploaded in chunks if the size exceeds the 4 MB threshold.
     *
     * @param string|resource $contents
     */
    protected function shouldUploadChunked($contents): bool
    {
        $size = is_string($contents) ? strlen($contents) : fstat($contents)['size'];

        if($size === null || $this->isPipe($contents)) {
            return true;
        }

        return $size > $this->maxChunkSize;
    }

    /**
     * Check if the contents is a pipe stream (not seekable, no size defined).
     *
     * @param string|resource $contents
     *
     * @return bool
     */
    protected function isPipe($contents): bool
    {
        return is_resource($contents) && (fstat($contents)['mode'] & 010000) !== 0;
    }

    /**
     * @return string
     */
    protected function getTokenUri(): string
    {
        return sprintf($this->tokenUri, $this->tenantId);
    }

    /**
     * @param Group $group
     * @throws GraphException
     * @throws GuzzleException
     */
    public function setDrivePath(Group $group): void
    {
        /**
         * @var Drive[] $drives
         */
        $drives = $this->getDrivesByGroup($group);
        if(count($drives) === 0) {
            throw new GraphException('No drives available');
        } else if (count($drives) > 1) {
            throw new GraphException('Multiple drives found. No drive specified.');
        }

        $this->drivePath = '/drives/'.$drives[0]->getId();
    }

    /**
     * @param string $name
     * @return Group|null
     * @throws GuzzleException
     * @throws GraphException
     */
    public function getGroupByName(string $name): ?Group
    {
        foreach ($this->getGroups() as $group)
        {
            if($group->getDisplayName() === $name) {
                return $group;
            }
        }

        return null;
    }

    /**
     * @return Group[]
     * @throws GuzzleException
     * @throws GraphException
     */
    protected function getDrivesByGroup(Group $group): array
    {
        return $this->graph->createRequest('GET', '/groups/'.$group->getId().'/drives')
            ->setReturnType(\Microsoft\Graph\Model\Drive::class)
            ->execute();
    }

    /**
     * @return Group[]
     * @throws GuzzleException
     * @throws GraphException
     */
    protected function getGroups(): array
    {
        return $this->graph->createRequest('GET', '/groups')->setReturnType(\Microsoft\Graph\Model\Group::class)->execute();
    }

    /**
     * @throws GuzzleException
     */
    protected function executeRequest(GraphRequest $graphRequest)
    {
        try {
            return $graphRequest->execute();
        } catch (GuzzleException $e) {
            if($e->getCode() == 401) {
                $this->refreshAccessToken();
                $this->applyAccessToken($this->accessToken, $graphRequest);
                return $this->executeRequest($graphRequest);
            }
            throw $e;
        }
    }

    /**
     * @return void
     */
    protected function refreshAccessToken(): void
    {
        $this->accessToken = $this->getGraphAccessToken();
    }

    protected function applyAccessToken(string $accessToken, GraphRequest $graphRequest = null)
    {
        $this->graph->setAccessToken($accessToken);
        if($graphRequest) {
            $graphRequest->setAccessToken($accessToken);
        }
    }

}