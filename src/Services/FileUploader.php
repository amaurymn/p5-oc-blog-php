<?php

namespace App\Services;

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
    public const FILE_PDF        = 'pdf';
    public const FILE_IMG        = 'image';
    public const FILE_ADMIN_PATH = '/upload';

    /**
     * FileUploader constructor.
     * @param array $file
     * @param string|null $inputName
     */
    public function __construct(array $file, ?string $inputName = null)
    {
        $this->config   = Yaml::parseFile(CONF_DIR . '/config.yml');
        $this->flashBag = new FlashBag();

        $fieldName = $inputName ?? array_key_first($file);

        $this->fileName    = $file[$fieldName]['name'];
        $this->fileType    = $file[$fieldName]['type'];
        $this->fileTmpName = $file[$fieldName]['tmp_name'];
        $this->fileError   = $file[$fieldName]['error'];
        $this->fileSize    = $file[$fieldName]['size'];
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
     * @param string|null $type
     * @param string|null $customPath
     * @return bool
     */
    public function upload(?string $type = null, ?string $customPath = null): bool
    {
        $fileTypePath = $customPath ?? '/img' . $this->config['imgUploadPath'];

        $this->uploadPath = PUBLIC_DIR . $fileTypePath;

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 755, true);
        }

        $this->renameFile($type);

        return move_uploaded_file($this->fileTmpName, $this->uploadPath . '/' . $this->getName());
    }

    /**
     * @param null $type
     */
    private function renameFile($type = null): void
    {
        $this->newFileName = ($type === self::FILE_PDF) ? 'cv.pdf' : date('ymd-His') . '_' . $this->fileName;
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

            if (!in_array($this->getFileExt($this->fileName), $allowedFileExt)) {
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
