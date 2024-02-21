<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class VideoUploaderHelper {

    private $slugger;
    private $params;
    private $flash;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params) {
        $this->slugger = $slugger;
        $this->params = $params;
    }

    public function uploadVideo($form, $user): string {
        $errorMessage = "";
        $videoFile = $form->get('video')->getData();
    
        if ($videoFile) {
            $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
    
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFilename = $safeFilename . '.' . $videoFile->guessExtension();
    
            try {
                $videoFile->move(
                    $this->params->get('upload_dir'),
                    $newFilename
                );
    
                // Assuming a method setVideo() in your User entity
                $user->setVideo($newFilename);
            } catch (FileException $e) {
                $errorMessage = $e->getMessage();
            }
        }
    
        return $errorMessage;
    }
    
}
