<?php
namespace 1ike\resize-image;

class HandleImage
{
    /**
     * description: 图像等比例压缩
     * @param $path 输出路径
     * @param null $out_path 输出路径
     * @param int $max_width 最大宽度
     * @param int $max_height 最大高度
     * @return bool
     */
    public function resizeImage($path, $out_path = null, $max_width = 800, $max_height = 800)
    {
        try {
            $im = imagecreatefromjpeg($path);
            $width = imagesx($im);
            $height = imagesy($im);

            if (!is_dir(dirname($out_path))) {
                mkdir(dirname($out_path), 0755, true);
            }

            if (($max_width && $width > $max_width) || ($max_height && $height > $max_height)) {
                if ($max_width && $width > $max_width) {
                    $width_ratio = $max_width / $width;
                    $resize_width_tag = true;
                }
                if ($max_height && $height > $max_height) {
                    $height_ratio = $max_height / $height;
                    $resize_height_tag = true;
                }
                if ($resize_width_tag && $resize_height_tag) {
                    if ($width_ratio < $height_ratio)
                        $ratio = $width_ratio;
                    else
                        $ratio = $height_ratio;
                }
                if ($resize_width_tag && !$resize_height_tag)
                    $ratio = $width_ratio;
                if ($resize_height_tag && !$resize_width_tag)
                    $ratio = $height_ratio;
                $new_width = $width * $ratio;
                $new_height = $height * $ratio;

                if (function_exists("imagecopyresampled")) {
                    $new_im = imagecreatetruecolor($new_width, $new_height);
                    imagecopyresampled($new_im, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                } else {
                    $new_im = imagecreate($new_width, $new_height);
                    imagecopyresized($new_im, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                }

                $res = imagejpeg($new_im, !empty($out_path) ? $out_path : $path);
                imagedestroy($new_im);
            } else {
                $res = imagejpeg($im, !empty($out_path) ? $out_path : $path);
            }
            return $res;
        } catch (\Exception $e) {
            return false;
        }
    }
}
