<?php

namespace App\Services;

use App\Exception\FileException;
use Symfony\Component\Yaml\Yaml;

class FileUploader
{
    private $fileName;
    private $fileType;
    private $fileTmpName;
    private $fileError;
    private $fileSize;
    private $config;
    private $status = true;
    private $uploadPath;
    private $newFileName;
    private FlashBag $flashBag;
    public const TYPE_POST    = 'post';
    public const TYPE_PROFILE = 'profile';
    public const FILE_PDF     = 'pdf';
    public const FILE_IMG        = 'image';
    public const FILE_ADMIN_PATH = '/upload';

    /**
     * FileUploader constructor.
     * @param array|null $file
     * @param string|null $inputName
     */
    public function __construct(?array $file = [], ?string $inputName = null)
    {
        $this->config   = Yaml::parseFile(CONF_DIR . '/config.yml');
        $this->flashBag = new FlashBag();

        if (!empty($file)) {
            $fieldName         = $inputName ?? array_key_first($file);
            $this->fileName    = $file[$fieldName]['name'];
            $this->fileType    = $file[$fieldName]['type'];
            $this->fileTmpName = $file[$fieldName]['tmp_name'];
            $this->fileError   = $file[$fieldName]['error'];
            $this->fileSize    = $file[$fieldName]['size'];
        }
    }

    /**
     * @param string|null $type
     * @return bool
     */
    public function checkFile(string $type = null): bool
    {
        $this
            ->checkEmptyFile()
            ->checkFileExt($type)
            ->checkFileMime($type)
            ->checkFileError();

        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->newFileName;
    }

    /**
     * @param string $actionType
     * @param string|null $customPath
     * @return bool
     */
    public function upload(string $actionType, ?string $customPath = null): bool
    {
        $fileTypePath = $customPath ?? '/img' . $this->config['imgUploadPath'];
        $this->uploadPath = PUBLIC_DIR . $fileTypePath;

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 755, true);
        }

        $this->renameFile($actionType);

        return move_uploaded_file($this->fileTmpName, $this->uploadPath . '/' . $this->getName());
    }

    /**
     * @param string $actionType
     * @param string $file
     * @return bool
     * @throws FileException
     */
    public function deleteFile(string $actionType, string $file): bool
    {
        switch ($actionType) {
            case self::TYPE_POST;
                $imagePath = PUBLIC_DIR . '/img' . $this->config['imgUploadPath'] . '/' . $file;
                break;
            case self::TYPE_PROFILE;
                $imagePath = PUBLIC_DIR . self::FILE_ADMIN_PATH . '/' . $file;
                break;
            default:
                $imagePath = null;
        }

        try {
            return unlink($imagePath);
        } catch (\Exception $e) {
            throw new FileException("Erreur lors la suppression de l'image.");
        }
    }

    /**
     * @param string|null $actionType
     */
    private function renameFile(string $actionType): void
    {
        $this->newFileName =  $actionType . '_' . str_replace('.', '', microtime(true)) . '.' . $this->getFileExt($this->fileName);
    }

    /**
     * @param string $type
     * @param string $message
     * @return $this
     */
    private function setError(string $type, string $message): FileUploader
    {
        $this->flashBag->set($type, $message);

        return $this;
    }

    /**
     * @return $this
     */
    private function checkFileError(): FileUploader
    {
        if ($this->fileError !== 0) {
            $this->setError(FlashBag::ERROR, "Une erreur est survenue pendant le chargement du fichier.");
            $this->status = false;
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function checkEmptyFile(): FileUploader
    {
        if ($this->fileSize === 0) {
            $this->setError(FlashBag::ERROR, "Le fichier ne peut pas être vide.");
            $this->status = false;
        }

        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    private function checkFileExt($type): FileUploader
    {
        if (!empty($this->fileName)) {
            $allowedFileExt = ($type === self::FILE_PDF) ? $this->config['fileAllowedExt'] : $this->config['imgAllowedExt'];
            $allowedExt     = '';

            foreach ($allowedFileExt as $ext) {
                $allowedExt .= $ext . ',';
            }

            if (!in_array($this->getFileExt($this->fileName), $allowedFileExt, true)) {
                $this->setError(FlashBag::ERROR, "Seul les fichiers {$allowedExt} sont valides.");
                $this->status = false;
            }
        }

        return $this;
    }

    /**
     * @param $type
     * @return $this
     */
    private function checkFileMime($type): FileUploader
    {
        if (!empty($this->fileTmpName)) {
            $allowedMimeType = ($type === self::FILE_PDF) ? $this->config['fileAllowedMime'] : $this->config['imgAllowedMime'];

            $mimeType = mime_content_type($this->fileTmpName);

            if (!in_array($mimeType, $allowedMimeType, true)) {
                $this->setError(FlashBag::ERROR, "Le type de fichier est invalide.");
                $this->status = false;
            }
        }

        return $this;
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getFileExt(string $fileName): string
    {
        $fileExt = explode('.', $fileName);

        return strtolower(end($fileExt));
    }
}
