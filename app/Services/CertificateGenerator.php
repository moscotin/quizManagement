<?php

namespace App\Services;

use Imagick;
use ImagickDraw;

class CertificateGenerator
{
    public function generate($quiz, string $name): string
    {
        $template = storage_path('images/certificate_templates/' . $quiz->certificate_image);

        // using GD just echo same image
        $image = imagecreatefromjpeg($template);
        $black = imagecolorallocate($image, 0, 0, 0);
        $fontPath = storage_path('fonts/MyriadProRegular.ttf');
        $fontSize = 72;
        $angle = 0;
        $textBox = imagettfbbox($fontSize, $angle, $fontPath, $name);$textWidth = $textBox[2] - $textBox[0];
        $imageWidth = imagesx($image);

        // check text width and adjust font size if needed
        while ($textWidth > ($imageWidth - 400)) {
            $fontSize -= 2;
            $textBox = imagettfbbox($fontSize, $angle, $fontPath, $name);
            $textWidth = $textBox[2] - $textBox[0];
        }

        $x = ($imageWidth - $textWidth) / 2;
        $y = 1400; // fixed y position

        imagettftext($image, $fontSize, $angle, $x, $y, $black, $fontPath, $name);

        ob_start();
        imagejpeg($image, null, 90);
        $binary = ob_get_clean();

        imagedestroy($image);

        return $binary;
    }
}
