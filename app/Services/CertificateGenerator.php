<?php

namespace App\Services;

class CertificateGenerator
{
    public function generate($quiz, string $name, string $org): string
    {
        $template = storage_path('images/certificate_templates/' . $quiz->certificate_image);

        // using GD just echo same image
        $image = imagecreatefromjpeg($template);
        $black = imagecolorallocate($image, 0, 0, 0);
        $fontPath = storage_path('fonts/MyriadProRegular.ttf');
        $fontSize = 72;
        $angle = 0;

        // Name text width calculation
        $textBox = imagettfbbox($fontSize, $angle, $fontPath, $name);$textWidth = $textBox[2] - $textBox[0];
        $imageWidth = imagesx($image);

        // check text width and adjust font size if needed
        while ($textWidth > ($imageWidth - 400)) {
            $fontSize -= 2;
            $textBox = imagettfbbox($fontSize, $angle, $fontPath, $name);
            $textWidth = $textBox[2] - $textBox[0];
        }

        // Name position
        $x = ($imageWidth - $textWidth) / 2;
        $y = 1400; // fixed y position
        // Draw the name on the certificate
        imagettftext($image, $fontSize, $angle, $x, $y, $black, $fontPath, $name);

        // Organization text width calculation
        $orgFontSize = 48;
        $orgTextBox = imagettfbbox($orgFontSize, $angle, $fontPath, $org);
        $orgTextWidth = $orgTextBox[2] - $orgTextBox[0];
        // check text width and adjust font size if needed
        while ($orgTextWidth > ($imageWidth - 400)) {
            $orgFontSize -= 2;
            $orgTextBox = imagettfbbox($orgFontSize, $angle, $fontPath, $org);
            $orgTextWidth = $orgTextBox[2] - $orgTextBox[0];
        }

        // Organization position
        $orgX = ($imageWidth - $orgTextWidth) / 2;
        $orgY = 1500; // fixed y position
        // Draw the organization on the certificate
        imagettftext($image, $orgFontSize, $angle, $orgX, $orgY, $black, $fontPath, $org);

        ob_start();
        imagejpeg($image, null, 90);
        $binary = ob_get_clean();

        imagedestroy($image);

        return $binary;
    }
    public function generateDiploma($quiz, string $name, string $org): string
    {
        $template = storage_path('images/diploma_templates/' . $quiz->diploma_image);

        // using GD just echo same image
        $image = imagecreatefromjpeg($template);
        $black = imagecolorallocate($image, 0, 0, 0);
        $fontPath = storage_path('fonts/MyriadProRegular.ttf');
        $fontSize = 72;
        $angle = 0;



        // Name text width calculation
        $textBox = imagettfbbox($fontSize, $angle, $fontPath, $name);$textWidth = $textBox[2] - $textBox[0];
        $imageWidth = imagesx($image);

        // check text width and adjust font size if needed
        while ($textWidth > ($imageWidth - 400)) {
            $fontSize -= 2;
            $textBox = imagettfbbox($fontSize, $angle, $fontPath, $name);
            $textWidth = $textBox[2] - $textBox[0];
        }

        // Name position
        $x = ($imageWidth - $textWidth) / 2;
        $y = 1450; // fixed y position
        // Draw the name on the certificate
        imagettftext($image, $fontSize, $angle, $x, $y, $black, $fontPath, $name);

        // Organization text width calculation
        $orgFontSize = 48;
        $orgTextBox = imagettfbbox($orgFontSize, $angle, $fontPath, $org);
        $orgTextWidth = $orgTextBox[2] - $orgTextBox[0];
        // check text width and adjust font size if needed
        while ($orgTextWidth > ($imageWidth - 400)) {
            $orgFontSize -= 2;
            $orgTextBox = imagettfbbox($orgFontSize, $angle, $fontPath, $org);
            $orgTextWidth = $orgTextBox[2] - $orgTextBox[0];
        }

        // Organization position
        $orgX = ($imageWidth - $orgTextWidth) / 2;
        $orgY = 1550; // fixed y position
        // Draw the organization on the certificate
        imagettftext($image, $orgFontSize, $angle, $orgX, $orgY, $black, $fontPath, $org);


        ob_start();
        imagejpeg($image, null, 90);
        $binary = ob_get_clean();

        imagedestroy($image);

        return $binary;
    }
}
