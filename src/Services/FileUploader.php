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
    public const FILE_PDF = 'pdf';

    public function __construct(array $file)
    {
        $this->config   = Yaml::parseFile(CONF_DIR . '/config.yml');
        $this->flashBag = new FlashBag();

        $inputName = array_key_first($file);

        $this->fileName    = $file[$inputName]['name'];
        $this->fileType    = $file[$inputName]['type'];
        $this->fileTmpName = $file[$inputName]['tmp_name'];
        $this->fileError   = $file[$inputName]['error'];
        $this->fileSize    = $file[$inputName]['size'];
    }

    /**
     * @param string|null $type
     * @return bool
     */
    public function checkFile(string $type = null)
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
     * @param null $type
     * @return bool
     */
    public function upload($type = null)
    {
        $fileTypePath = ($type === self::FILE_PDF) ? '/upload' : '/img' . $this->config['imgUploadPath'];

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
    private function setError(string $type, string $message)
    {
        $this->flashBag->set($type, $message);

        return $this;
    }

    /**
     * @return $this
     */
    private function checkFileError()
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
    private function checkEmptyFile()
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
    private function checkFileExt($type)
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
    private function checkFileMime($type)
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
