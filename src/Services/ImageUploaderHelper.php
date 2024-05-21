<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploaderHelper {

    private $slugger;
    private $params;
    private $flash;

    public function __construct(SluggerInterface $slugger, ParameterBagInterface $params) {
        $this->slugger = $slugger;
        $this->params = $params;
        }

    public function uploadImage($form, $user): String {
        $errorMessage = "";
        $imageFile = $form->get('profile_image')->getData();

        
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
           
            // dump($originalFilename);

            $safeFilename = $user->getId();
            $newFilename = $safeFilename.'.'.$imageFile->guessExtension();
        
            // dump($safeFilename);
            // dump($newFilename);
            
            try {
                $imageFile->move(
                    $this->params->get('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
               $errorMessage = $e->getMessage();
            }
            $user->setProfileImage($newFilename);
            
        }
        return $errorMessage;
    }
    
    public function uploadImageCategory($form, $category): String {
        $errorMessage = "";
        $imageFile = $form->get('image')->getData();

        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $safeFilename = $category->getRealName();
            $newFilename = $safeFilename.'.'.$imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->params->get('images_directory'),
                    $newFilename
                );

                $category->setImage($newFilename);
            } catch (FileException $e) {
                $errorMessage = $e->getMessage();
            }
        }

        return $errorMessage;
    }
}
