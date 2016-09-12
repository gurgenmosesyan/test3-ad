<?php

namespace App\Core\Image;

use Intervention\Image\ImageManagerStatic as ImageManager;

class SaveImage
{
    public static function save($image, $model, $column = 'image')
    {
        $image = trim($image);
        if ($image === 'same') {
            return $model->$column;
        } else if (empty($image)) {
            $filePath = env('PUBLIC_PATH', public_path()) . '/' . $model->getStorePath();
            if (!empty($model->getFile($column)) && file_exists($filePath . '/' . $model->getFile($column))) {
                unlink($filePath . '/' . $model->getFile($column));
            }
            $model->setFile('', $column);
            return '';
        } else {
            $file = new SubmittedImage();
            $file->setImageType(SubmittedImage::FILE_TYPE_NEW);
            $tempFile = Uploader::getTempImage($image, false, true);
            $file->setTempImage($tempFile);

            $tempFile = $file->getTempFile();
            list($filePath, $subDir, $fileName) = self::createPathInfo($model, $tempFile->getExtension());
            $tempFile->save($filePath . '/' . $subDir, $fileName);
            if (!empty($model->getFile($column)) && file_exists($filePath . '/' . $model->getFile($column))) {
                unlink($filePath . '/' . $model->getFile($column));
            }
            $model->setFile($subDir . '/' . $fileName, $column);
            return $subDir . '/' . $fileName;
        }
    }

    public static function createPathInfo($model, $extension)
    {
        $filePath = env('PUBLIC_PATH', public_path()) . '/' . $model->getStorePath();
        if (!is_dir($filePath)) {
            mkdir($filePath, 0775, true);
        }

        $subFilesCount = 1000;
        $subDir = intval($model->id / $subFilesCount) + 1;
        if (!file_exists($filePath . '/' . $subDir)) {
            mkdir($filePath . '/' . $subDir);
        }
        $fileName = str_replace('.', '', microtime(true) . '') . '.' . $extension;

        return [$filePath, $subDir, $fileName];
    }

    public static function savePrintImage($image, $model, $column = 'image_print')
    {
        $image = trim($image);
        if ($image === 'same') {
            return $model->$column;
        } else if (empty($image)) {
            $filePath = env('PUBLIC_PATH', public_path()) . '/images/commercial_origin';
            if (!empty($model->getFile($column)) && file_exists($filePath . '/' . $model->getFile($column))) {
                unlink($filePath . '/' . $model->getFile($column));
                $bigFilePath = env('PUBLIC_PATH', public_path()) . '/images/commercial_big';
                if (file_exists($bigFilePath . '/' . $model->getFile($column))) {
                    unlink($bigFilePath . '/' . $model->getFile($column));
                }
            }
            $model->setFile('', $column);
            return '';
        } else {
            $file = new SubmittedImage();
            $file->setImageType(SubmittedImage::FILE_TYPE_NEW);
            $tempFile = Uploader::getTempImage($image, false, true);
            $file->setTempImage($tempFile);

            $tempFile = $file->getTempFile();
            list($filePath, $subDir, $fileName) = self::createPrintPathInfo($model, $tempFile->getExtension());
            $tempFile->save($filePath . '/' . $subDir, $fileName);

            $bigFilePath = env('PUBLIC_PATH', public_path()) . '/images/commercial_big';
            $img = ImageManager::make($filePath.'/'.$subDir.'/'.$fileName);
            $img->resize(690, null, function($constraint) {
                $constraint->aspectRatio();
            });
            if (!is_dir($bigFilePath)) {
                mkdir($bigFilePath, 0775, true);
            }
            if (!is_dir($bigFilePath.'/'.$subDir)) {
                mkdir($bigFilePath.'/'.$subDir, 0775, true);
            }
            $img->save($bigFilePath.'/'.$subDir.'/'.$fileName);

            if (!empty($model->getFile($column)) && file_exists($filePath . '/' . $model->getFile($column))) {
                unlink($filePath . '/' . $model->getFile($column));
                if (file_exists($bigFilePath . '/' . $model->getFile($column))) {
                    unlink($bigFilePath . '/' . $model->getFile($column));
                }
            }

            $model->setFile($subDir . '/' . $fileName, $column);
            return $subDir . '/' . $fileName;
        }
    }

    public static function createPrintPathInfo($model, $extension)
    {
        $filePath = env('PUBLIC_PATH', public_path()) . '/images/commercial_origin';
        if (!is_dir($filePath)) {
            mkdir($filePath, 0775, true);
        }

        $subFilesCount = 1000;
        $subDir = intval($model->id / $subFilesCount) + 1;
        if (!file_exists($filePath . '/' . $subDir)) {
            mkdir($filePath . '/' . $subDir);
        }
        $fileName = str_replace('.', '', microtime(true) . '') . '.' . $extension;

        return [$filePath, $subDir, $fileName];
    }
}