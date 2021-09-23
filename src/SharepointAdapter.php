<?php

namespace Homedesignshops\FlysystemSharepoint;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Config;
use Microsoft\Graph\Model\DriveItem;

class SharepointAdapter extends AbstractAdapter
{
    use NotSupportingVisibilityTrait;

    protected SharepointClient $client;

    protected string $prefix;

    public function __construct(SharepointClient $client, string $prefix = '')
    {
        $this->client = $client;

        $this->setPathPrefix($prefix);
    }

    /**
     * @inheritDoc
     */
    public function write($path, $contents, Config $config)
    {
        return $this->upload($path, $contents, 'add');
    }

    /**
     * @inheritDoc
     */
    public function writeStream($path, $resource, Config $config)
    {
        // TODO: Implement writeStream() method.
    }

    /**
     * @inheritDoc
     */
    public function update($path, $contents, Config $config)
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function updateStream($path, $resource, Config $config)
    {
        // TODO: Implement updateStream() method.
    }

    /**
     * @inheritDoc
     */
    public function rename($path, $newpath)
    {
        // TODO: Implement rename() method.
    }

    /**
     * @inheritDoc
     */
    public function copy($path, $newpath)
    {
        // TODO: Implement copy() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($path)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    /**
     * @inheritDoc
     */
    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }

    /**
     * @inheritDoc
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * @inheritDoc
     */
    public function has($path)
    {
        // TODO: Implement has() method.
    }

    /**
     * @inheritDoc
     */
    public function read($path)
    {
        // TODO: Implement read() method.
    }

    /**
     * @inheritDoc
     */
    public function readStream($path)
    {
        // TODO: Implement readStream() method.
    }

    /**
     * @inheritDoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        // TODO: Implement listContents() method.
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($path)
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * @inheritDoc
     */
    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    /**
     * @inheritDoc
     */
    public function getMimetype($path)
    {
        // TODO: Implement getMimetype() method.
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    /**
     * @inheritDoc
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }

    /**
     * @inheritDoc
     */
    public function applyPathPrefix($path): string
    {
        $path = parent::applyPathPrefix($path);

        return '/'.trim($path, '/');
    }

    public function getClient(): SharepointClient
    {
        return $this->client;
    }

    /**
     * Returns an array with DriveItem if uploaded successfully. Returns false if not.
     *
     * @param string $path
     * @param $contents
     * @param string $mode
     * @return DriveItem|bool
     */
    protected function upload(string $path, $contents, string $mode): DriveItem|bool
    {
        $path = $this->applyPathPrefix($path);

        try {
            return $this->client->upload($path, $contents, $mode);
        } catch (\Exception $e) {
            return false;
        }
    }
}