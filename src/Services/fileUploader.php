<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

class fileUploader
{
    private $fileName;
    private $fileType;
    private $fileTmpName;
    private $fileError;
    private $fileSize;
    private $config;
    private $status = true;
    private $uploadPath;
    private $imageName;
    private FlashBag $flashBag;

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
     * @return bool
     */
    public function checkFile()
    {
        $this
            ->checkEmptyFile()
            ->checkFileExt()
            ->checkFileMime()
            ->checkFileError();

        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->imageName;
    }

    /**
     * @return bool
     */
    public function upload()
    {
        $this->uploadPath = PUBLIC_DIR . '/img' . $this->config['imgUploadPath'];

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 755, true);
        }

        $this->renameFile();

        return move_uploaded_file($this->fileTmpName, $this->uploadPath . '/' . $this->getName());
    }

    private function renameFile(): void
    {
        $this->imageName = date('ymd-His') . '_' . $this->fileName;
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
     * @return $this
     */
    private function checkFileExt()
    {
        if (!empty($this->fileName)) {
            $allowedImgExt = $this->config['fileAllowedExt'];

            $allowedExt = '';

            foreach ($allowedImgExt as $ext) {
                $allowedExt .= $ext . ',';
            }

            if (!in_array($this->getFileExt($this->fileName), $allowedImgExt, true)) {
                $this->setError(FlashBag::ERROR, "Seul les fichiers {$allowedExt} sont valides.");
                $this->status = false;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function checkFileMime()
    {
        if (!empty($this->fileTmpName)) {

            $mimeType = mime_content_type($this->fileTmpName);

            if (!in_array($mimeType, $this->config['fileAllowedMime'], true)) {
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
