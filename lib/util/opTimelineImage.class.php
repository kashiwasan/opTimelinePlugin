<?php
class opTimelineImage
{
  public static function getNotImageUrl()
  {
    return op_image_path('no_image.gif', true);
  }

  public static function createMinimumImageByWidthSizeAndPaths($minimumWidthSize, $paths)
  {
    $image = self::_getImageResourceByPath($paths['resource']);
    $fileSize = self::getImageSizeByPath($paths['resource']);

    $newWidth = $minimumWidthSize;
    $newHeight = abs($minimumWidthSize * $fileSize['height'] / $fileSize['width']);

    $newImage = ImageCreateTrueColor($newWidth, $newHeight);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $fileSize['width'], $fileSize['height']);

    self::_saveImageByImageResourceAndSavePath($newImage, $paths['save']);

    return true;
  }

  public static function getImageSizeByPath($path)
  {
    $image = self::_getImageResourceByPath($path);

    return array(
        'width' => imagesx($image),
        'height'=> imagesy($image)
        );
  }

  private static function _getImageResourceByPath($path)
  {
    $info = getimagesize($path);
    switch ($info['mime'])
    {
      case 'image/png':
        $image = imagecreatefrompng($path);
        break;

      case 'image/jpeg':
        $image = imagecreatefromjpeg($path);
        break;

      case 'image/gif':
        $image = imagecreatefromgif($path);
        break;
    }

    return $image;
  }

  private static function _saveImageByImageResourceAndSavePath($resource, $savePath)
  {
    $extension = pathinfo($savePath, PATHINFO_EXTENSION);

    switch ($extension)
    {
      case 'png':
        return imagepng($resource, $savePath);
        break;

      case 'jpeg':
        return imagejpeg($resource, $savePath);
        break;

      case 'gif':
        return imagegif($resource, $savePath);
        break;
    }
  }
}
