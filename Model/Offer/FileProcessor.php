<?php

namespace Ograre\Offers\Model\Offer;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\File\Name;
use Magento\Framework\FileSystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\MediaStorage\Helper\File\Storage\Database as FileStorageDb;
use Magento\Store\Model\StoreManagerInterface;
use Ograre\Offers\Api\Data\OfferFileProcessorInterface;
use Ograre\Offers\Api\Data\OfferInterface;

class FileProcessor implements OfferFileProcessorInterface
{
    public const FILE_DIR = OfferInterface::IMAGE_FOLDER;
    public const FILE_TMP_DIR = self::FILE_DIR.'/tmp';
    public const ALLOWED_MIME_TYPES = [
        'jpg' => 'image/jpg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    /** @var WriteInterface $mediaDirectory  */
    protected WriteInterface $mediaDirectory;

    /** @var Name $fileNameLookup */
    protected Name $fileNameLookup;

    /**
     * @param UploaderFactory $uploaderFactory
     * @param FileStorageDb $fileStorageDb
     * @param StoreManagerInterface $storeManager
     * @param FileSystem $fileSystem
     * @param Name|null $fileNameLookup
     * @throws FileSystemException
     */
    public function __construct(
        protected UploaderFactory $uploaderFactory,
        protected FileStorageDb $fileStorageDb,
        protected StoreManagerInterface $storeManager,
        FileSystem $fileSystem,
        ?Name $fileNameLookup = null
    ) {
        $this->mediaDirectory = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->fileNameLookup = $fileNameLookup ?? ObjectManager::getInstance()->get(Name::class);
    }

    /**
     * @param string $fileId
     * @return array
     */
    public function saveToTmp(string $fileId): array
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowedExtensions(['jpg' => 'jpg', 'jpeg' => 'jpeg', 'png' => 'png']);
            if (!$uploader->checkMimeType(static::ALLOWED_MIME_TYPES)) {
                throw new LocalizedException(__('Invalid file type.'));
            }

            $result  = $uploader->save($this->getAbsoluteTmpMediaPath());
            if (!$result) {
                throw new LocalizedException(__('File cannot be saved to destination folder.'));
            }
            unset($result['path']);

            $result['tmp_name'] =  str_replace('\\', '/', $result['tmp_name']);
            $result['url'] = $this->getTmpMediaUrl($result);
            $result['name'] = $result['file'];

            if (isset($result['file'])) {
                try {
                    $this->fileStorageDb->saveFile($this->getFilePath(static::FILE_TMP_DIR, $result['file']));
                } catch (Exception $e) {
                    throw new LocalizedException(__('Something went wrong while saving the file.'), $e);
                }
            }
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'error_code' => $e->getCode()];
        }

        return $result;
    }

    /**
     * @param string $fileName
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function moveFileFromTmp(string $fileName): string
    {
        $destinationPath = $this->getFilePath(
            static::FILE_DIR,
            $this->fileNameLookup->getNewFileName(
                $this->mediaDirectory->getAbsolutePath(
                    $this->getFilePath(static::FILE_DIR, $fileName)
                )
            )
        );
        $tmpImagePath = $this->mediaDirectory->getAbsolutePath(
            $this->getFilePath(static::FILE_TMP_DIR, $fileName)
        );

        try {
            $this->fileStorageDb->renameFile($tmpImagePath, $destinationPath);
            $this->mediaDirectory->renameFile($tmpImagePath, $destinationPath);
        } catch (Exception $e) {
            throw new LocalizedException(__('Something went wrong while moving the file.'));
        }

        return $destinationPath;
    }

    /**
     * @param string $filename
     * @return bool
     */
    public function tmpFileExists(string $filename): bool
    {
        $filePath = $this->mediaDirectory->getAbsolutePath(
            $this->getFilePath(static::FILE_TMP_DIR, $filename)
        );

        return file_exists($filePath);
    }

    /**
     * @return string
     */
    protected function getAbsoluteTmpMediaPath(): string
    {
        return $this->mediaDirectory->getAbsolutePath(static::FILE_TMP_DIR);
    }

    /**
     * @param array $file
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getTmpMediaUrl(array $file): string
    {
        return sprintf(
            '%s%s',
            $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA),
            $this->getFilePath(static::FILE_TMP_DIR, $file['file'])
        );
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    protected function getFilePath(string $path, string $filename): string
    {
        return sprintf(
            '%s/%s',
            rtrim($path, '/'),
            ltrim($filename, '/')
        );

    }
}
