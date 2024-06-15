<?php

namespace App\Controller;

use App\Entity\User;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager,
        private Environment $twig // Inject Twig Environment
    ) {
    }

    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail($form->get('email')->getData());
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
            'location' => '',
        ]);
    }

    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
            'location' => '',
        ]);
    }

    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, string $token = null): Response
    {
        if ($token) {
            $this->storeTokenInSession($token);
            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                $e->getReason()
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->resetPasswordHelper->removeResetRequest($token);

            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'location' => '',
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $emailFormData]);
    
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }
    
        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->redirectToRoute('app_check_email');
        }
    
        $mail = new PHPMailer(true);
    
        try {
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = 'html';
    
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USERNAME'];
            $mail->Password = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
    
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
    
            $mail->setFrom('evan.moreau@etik.com', 'Application US Avranches');
            $mail->addAddress($user->getEmail());
    
            $mail->isHTML(true);
            $mail->Subject = 'Demande de réinitialisation de mot de passe';
    
            $htmlContent = $this->twig->render('reset_password/email.html.twig', [
                'resetToken' => $resetToken,
                'user' => $user,
            ]);
            $mail->Body = $htmlContent;
    
            $mail->send();
        } catch (Exception $e) {
            $this->addFlash('error', "L'email n'a pas pu être envoyé. Mailer Error: {$mail->ErrorInfo}");
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }
    
        $this->setTokenObjectInSession($resetToken);
    
        return $this->redirectToRoute('app_check_email');
    }
}   
