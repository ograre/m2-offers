<?php

namespace Ograre\Offers\Ui\Component\DataProvider;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\ExtendedDriverInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ograre\Offers\Api\Data\OfferInterface;
use Ograre\Offers\Helper\Image;

class ImageModifier implements ModifierInterface
{
    /** @var WriteInterface $mediaDirectory */
    protected $mediaDirectory;

    /**
     * @param Filesystem $filesystem
     * @param Mime $mime
     * @param Image $offerImageHelper
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        protected Mime $mime,
        protected Image $offerImageHelper,
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        if (!empty($data['image'])) {
            $imageFilename = $data['image'];
            unset($data['image']);
            $stat = $this->getStat($imageFilename);
            $data['image'] = [
                [
                    'name' => basename($imageFilename),
                    'url' => $this->offerImageHelper->getImageUrlFromFilename($imageFilename),
                    'size' => $stat['size'],
                    'type' => $this->getMimeType($imageFilename)
                ]
            ];
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * @param string $filename
     * @return string
     * @throws FileSystemException
     */
    protected function getMimeType(string $filename): string
    {
        if ($this->mediaDirectory->getDriver() instanceof ExtendedDriverInterface) {
            return $this->mediaDirectory->getDriver()->getMetadata($filename)['mimetype'];
        } else {
            return $this->mime->getMimeType(
                $this->mediaDirectory->getAbsolutePath(
                    $this->getFilePath($filename)
                )
            );
        }
    }

    /**
     * @param string $filename
     * @return array
     */
    protected function getStat(string $filename): array
    {
        return $this->mediaDirectory->stat($this->getFilePath($filename));
    }

    /**
     * @param string $fileName
     * @return string
     */
    protected function getFilePath(string $fileName)
    {
        return sprintf('%s/%s', OfferInterface::IMAGE_FOLDER, $fileName);
    }
}
