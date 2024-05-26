<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('chunk_split', [$this, 'chunkSplit']),
        ];
    }

    public function chunkSplit($string, $length = 2, $delimiter = ' ')
    {
        $chunks = str_split($string, $length);
        return implode($delimiter, $chunks);
    }
}
