<?php

namespace App\Services;

use App\Manager\ArticleManager;

class Slugifier
{
    public function __construct()
    {
        setlocale(LC_ALL, 'en_US.UTF-8');
    }

    /**
     * @param $rawString
     * @param string $delimiter
     * @param int $threshold
     * @return false|string
     */
    public function slugify($rawString, $delimiter = '-', $threshold = 2)
    {
        if (!in_array($delimiter, ['-', '_'])) {
            return false;
        }
        // remove accents
        $cleanString = iconv('UTF-8', 'ASCII//TRANSLIT', $rawString);
        // replace underscores and slash by a dash
        $cleanString = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", $delimiter, $cleanString);
        // Replace unwanted chars by a dash and space
        $cleanString = preg_replace("/[^a-zA-Z0-9 -]/", '', $cleanString);
        // Add threshold for little words
        $cleanString = preg_replace("/\b[a-zA-Z0-9]{1,$threshold}\b/", $delimiter, $cleanString);
        // Replace spaces and doublons $delimiter by one $delimiter
        $cleanString = preg_replace("/[\/_|+ -]+/", $delimiter, $cleanString);
        // Set string in lowercase
        $cleanString = strtolower($cleanString);
        // Trim possible delimiter at start and end of string
        $cleanString = trim($cleanString, $delimiter);

        return $cleanString;
    }

    /**
     * @param string $string
     * @return string
     */
    public function getUniqueSlug(string $string): string
    {
        $count = -1;
        do {
            $slug = $this->slugify($string);
            ++$count;

            if ($count > 0) {
                $slug .= "-" . $count;
            }
        } while ((new ArticleManager())->checkSlugExist($slug) !== false);

        return $slug;
    }
}
