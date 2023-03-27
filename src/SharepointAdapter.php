<?php

namespace Homedesignshops\FlysystemSharepoint;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use Microsoft\Graph\Model\DriveItem;

class SharepointAdapter implements FilesystemAdapter
{
    protected SharepointClient $client;

    protected string $prefix;

    public function __construct(SharepointClient $client, string $prefix = '')
    {
        $this->client = $client;

        $this->prefix = $prefix;
    }

    /**
     * @inheritDoc
     */
    public function write($path, $contents, Config $config): void
    {
        $this->upload($path, $contents, 'add');
    }

    /**
     * @inheritDoc
     */
    public function writeStream($path, $resource, Config $config): void
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
    public function copy($path, $newpath, Config $config): void
    {
        // TODO: Implement copy() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($path): void
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
    public function setVisibility($path, $visibility): void
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
    public function read($path): string
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
    public function listContents($directory = '', $recursive = false): iterable
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
        return '/'.trim($this->prefix.'/'.$path, '/');
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
    protected function upload(string $path, $contents): DriveItem|bool
    {
        $path = $this->applyPathPrefix($path);

        try {
            return $this->client->upload($path, $contents);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileExists(string $path): bool
    {
        // TODO: Implement fileExists() method.
    }

    public function directoryExists(string $path): bool
    {
        // TODO: Implement directoryExists() method.
    }

    public function deleteDirectory(string $path): void
    {
        // TODO: Implement deleteDirectory() method.
    }

    public function createDirectory(string $path, Config $config): void
    {
        // TODO: Implement createDirectory() method.
    }

    public function visibility(string $path): FileAttributes
    {
        // TODO: Implement visibility() method.
    }

    public function mimeType(string $path): FileAttributes
    {
        // TODO: Implement mimeType() method.
    }

    public function lastModified(string $path): FileAttributes
    {
        // TODO: Implement lastModified() method.
    }

    public function fileSize(string $path): FileAttributes
    {
        // TODO: Implement fileSize() method.
    }

    public function move(string $source, string $destination, Config $config): void
    {
        // TODO: Implement move() method.
    }
}