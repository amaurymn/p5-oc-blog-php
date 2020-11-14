<?php

namespace App\Services;

use Symfony\Component\Yaml\Yaml;

class ImageUpload
{
    private $fileName;
    private $fileType;
    private $fileTmpName;
    private $fileError;
    private $fileSize;

    private $config;
    private $errors = false;
    private $status = true;
    private $uploadPath;
    private $imageName;

    public function __construct(array $file)
    {
        $this->config = Yaml::parseFile(CONF_DIR . '/config.yml');

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
    public function checkImage()
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

    /**
     * @return mixed
     */
    public function getErrorMsg()
    {
        return $this->errors;
    }

    private function renameFile(): void
    {
        $this->imageName = date('ymd-His') . '_' . $this->fileName;
    }

    /**
     * @param string $message
     * @return $this
     */
    private function setError(string $message)
    {
        $this->errors[] = $message;

        return $this;
    }

    /**
     * @return $this
     */
    private function checkFileError()
    {
        if ($this->fileError !== 0) {
            $this->setError("Une erreur est survenue pendant le chargement de l'image.");
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
            $this->setError("L'image ne peut pas être vide.");
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
            $allowedImgExt = $this->config['imgAllowedExt'];

            $allowedExt = '';

            foreach ($allowedImgExt as $ext) {
                $allowedExt .= $ext . ',';
            }

            if (!in_array($this->getFileExt($this->fileName), $allowedImgExt)) {
                $this->setError("Seul les images {$allowedExt} sont valides.");
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

            if (!in_array($mimeType, $this->config['imgAllowedMime'])) {
                $this->setError("Le type de fichier est invalide.");
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
