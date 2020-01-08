<?php

namespace spec\SWP\Bundle\ContentBundle\Resolver;

use Aws\S3\S3Client;
use Aws\S3\S3ClientInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolver;

class AssetLocationResolverSpec extends ObjectBehavior
{
    public function it_is_initializable(S3ClientInterface $s3Client)
    {
        $this->beConstructedWith('local_adapter', '', null, new S3Client(['region' => 'us-west-2', 'version' => '2006-03-01']), 'uploads');
        $this->shouldHaveType(AssetLocationResolver::class);
    }

    public function it_generates_correct_path_for_local_storage(FileInterface $file)
    {
        $file->getAssetId()->willReturn('image');
        $file->getFileExtension()->willReturn('jpg');
        $this->beConstructedWith('local_adapter', '', null, new S3Client(['region' => 'us-west-2', 'version' => '2006-03-01']), 'uploads');
        $this->getAssetUrl($file)->shouldReturn('uploads/swp/media/image.jpg');
    }

    public function it_generates_correct_path_for_local_aws_without_prefix(FileInterface $file)
    {
        $file->getAssetId()->willReturn('image');
        $file->getFileExtension()->willReturn('jpg');
        $this->beConstructedWith('aws_adapter', 'bucket', null, new S3Client(['region' => 'us-west-2', 'version' => '2006-03-01']), 'uploads');
        $this->getAssetUrl($file)->shouldReturn('https://bucket.s3.us-west-2.amazonaws.com/swp/media/image.jpg');
    }

    public function it_generates_correct_path_for_local_aws_with_prefix(FileInterface $file)
    {
        $file->getAssetId()->willReturn('image');
        $file->getFileExtension()->willReturn('jpg');
        $this->beConstructedWith('aws_adapter', 'bucket', 'prefix', new S3Client(['region' => 'us-west-2', 'version' => '2006-03-01']), 'uploads');
        $this->getAssetUrl($file)->shouldReturn('https://bucket.s3.us-west-2.amazonaws.com/prefix/swp/media/image.jpg');
    }
}
