<?php

namespace Ikoncept\Fabriq\Actions;

class ConvertPdfToImage
{
    public function __invoke(string $outputPath, string $inputPath, int $firstPage = 1, int $lastPage = 1) : string
    {
        $command = ('gs -sDEVICE=png16m \
            -dBATCH \
            -dNOPAUSE \
            -r144 \
            -dCompatibilityLevel=1.4 \
            -dOptimize=true \
            -dColorConversionStrategy=/sRGB \
            -dProcessColorModel=/DeviceRGB  \
            -dFirstPage='.$firstPage.' \
            -dLastPage='.$lastPage.' \
            -sOutputFile='.$outputPath.'-%d.png16m '.$inputPath);

        exec($command);

        return $outputPath;
    }
}
