<?php

namespace Origines\Manufacturer\Service;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\App\Filesystem\DirectoryList;

class ManufacturerImageService
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var ImageUploader $imageUploader */
    private $imageUploader;

    /** @var UrlInterface $urlBuilder */
    private $urlBuilder;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var File $file */
    private $file;

    /**
     * ManufacturerImageService constructor.
     * @param Filesystem $filesystem
     * @param File $file
     * @param ImageUploader $imageUploader
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Filesystem $filesystem,
        File $file,
        ImageUploader $imageUploader,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->filesystem = $filesystem;
        $this->file = $file;
        $this->imageUploader = $imageUploader;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Get image url
     *
     * @param $imageName
     * @param bool $tmpFolder
     * @return string
     * @throws NoSuchEntityException
     */
    public function getImageUrl($imageName, $tmpFolder = false)
    {
        $subDirectoryPath = $tmpFolder ? $this->imageUploader->getBaseTmpPath() : $this->imageUploader->getBasePath();

        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . $subDirectoryPath . '/'
            . $imageName;
    }

    /**
     * Move image from tmp
     *
     * @param $imageName
     *
     * @return string
     * @throws LocalizedException
     */
    public function moveImageFromTmp($imageName)
    {
        return $this->imageUploader->moveFileFromTmp($imageName);
    }

    /**
     * Save file to tmp
     *
     * @param $imageName
     *
     * @return string[]
     * @throws LocalizedException
     */
    public function saveFileToTmp($imageName)
    {
        return $this->imageUploader->saveFileToTmpDir($imageName);
    }

    /**
     * Delete image
     *
     * @param      $imageName
     * @param bool $tmpFolder
     *
     * @throws FileSystemException
     */
    public function deleteImage($imageName, $tmpFolder = false)
    {
        if (!$imageName) {
            return;
        }

        $directory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $subDirectoryPath = $tmpFolder ? $this->imageUploader->getBaseTmpPath() : $this->imageUploader->getBasePath();
        $imagePath = $directory->getAbsolutePath($this->imageUploader->getFilePath($subDirectoryPath, $imageName));

        $this->file->deleteFile($imagePath);
    }
}
