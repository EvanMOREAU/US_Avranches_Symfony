<?php

namespace App\Entity;

class Pdf extends \TCPDF
 {

    //Page header
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak( false, 0 );
        // set bacground image
        $img_file = 'img/US-Avranches.jpg';
        $this->Image( $img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0 );
        // restore auto-page-break status
        $this->SetAutoPageBreak( $auto_page_break, $bMargin );
        // set the starting point for the page content
        $this->setPageMark();
// =======
// use App\Repository\PdfRepository;
// use Doctrine\ORM\Mapping as ORM;
// use Doctrine\DBAL\Types\Types;

// #[ORM\Entity(repositoryClass: PdfRepository::class)]
// class Pdf
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     #[ORM\Column(length: 255)]
//     private ?string $name = null;

//     #[ORM\Column(type: Types::TEXT, nullable: true)]
//     private ?string $content = null;

//     #[ORM\Column(nullable: true)]
//     private ?int $stats = null;




//     public function getId(): ?int
//     {
//         return $this->id;

//     function getName(): ?string
//     {
//         return $this->name;
// >>>>>>> 831052605a9ca53b3ac394f58c01e99ab12b35e8
    }

}
