<?php

namespace spec\SWP\Bundle\ContentBundle\Resolver;

use Aws\S3\S3ClientInterface;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Model\FileInterface;
use SWP\Bundle\ContentBundle\Resolver\AssetLocationResolver;

class AssetLocationResolverSpec extends ObjectBehavior
{
    public function it_is_initializable(S3ClientInterface $s3Client)
    {
        $this->beConstructedWith('local_adapter');
        $this->shouldHaveType(AssetLocationResolver::class);
    }

    public function it_generates_correct_path_for_local_storage(FileInterface $file)
    {
        $file->getAssetId()->willReturn('image');
        $file->getFileExtension()->willReturn('jpg');
        $this->beConstructedWith('local_adapter');
        $this->getAssetUrl($file)->shouldReturn('swp/media/image.jpg');
    }

    public function it_generates_correct_path_for_local_storage_with_custom_local_directory(FileInterface $file)
    {
        $file->getAssetId()->willReturn('image');
        $file->getFileExtension()->willReturn('jpg');
        $this->beConstructedWith('local_adapter', null, null, null, 'uploads');
        $this->getAssetUrl($file)->shouldReturn('uploads/swp/media/image.jpg');
    }
}
