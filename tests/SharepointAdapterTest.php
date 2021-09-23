<?php

namespace Homedesignshops\FlysystemSharepoint\Tests;

use Homedesignshops\FlysystemSharepoint\SharepointAdapter;
use Homedesignshops\FlysystemSharepoint\SharepointClient;
use League\Flysystem\Config;
use Microsoft\Graph\Model\DriveItem;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class SharepointAdapterTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var SharepointClient|ObjectProphecy
     */
    protected ObjectProphecy|SharepointClient $client;

    /**
     * @var SharepointAdapter
     */
    protected SharepointAdapter $sharepointAdapter;

    /**
     * @before
     */
    protected function setUp(): void
    {
        $this->client = $this->prophesize(SharepointClient::class);

        $this->sharepointAdapter = new SharepointAdapter($this->client->reveal(), 'prefix');
    }

    /** @test */
    public function it_can_write(): void
    {
        $this->client->upload(Argument::any(), Argument::any(), Argument::any())->willReturn(new DriveItem());

        $result = $this->sharepointAdapter->write('something', 'contents', new Config());

        self::assertInstanceOf(DriveItem::class, $result);
    }
}