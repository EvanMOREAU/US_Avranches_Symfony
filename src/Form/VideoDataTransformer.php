<?php
namespace App\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Exception\TransformationFailedException;

class VideoDataTransformer implements DataTransformerInterface
{
    private $videoDirectory;

    public function __construct($videoDirectory)
    {
        $this->videoDirectory = $videoDirectory;
    }

    public function transform($value)
    {
        return null; // Le transformer ne sera utilisé que pour la partie "view"
    }

    public function reverseTransform($value)
    {
        // $value contiendra le nom du fichier actuel

        if (!$value) {
            return null;
        }

        // Ajoutez une vérification si $value est déjà une instance de File
        if ($value instanceof File) {
            return $value;
        }

        $filePath = $this->videoDirectory . DIRECTORY_SEPARATOR . $value;

        // Si le fichier vidéo actuel n'existe pas, lancez une exception (à adapter selon vos besoins)
        if (!file_exists($filePath)) {
            throw new TransformationFailedException("Le fichier vidéo '$value' n'existe pas.");
        }

        return new File($filePath);
    }
}