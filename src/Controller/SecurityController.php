<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $hCaptchaSiteKey = $_ENV['HCAPTCHA_SITE_KEY'];
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        $captchaValid = true;

        $hCaptchaResponse = $request->request->get('h-captcha-response');
        $captchaValid = $this->verifyHCaptcha($hCaptchaResponse, $request);

        if ($request->isMethod('POST')) {
      
            $hCaptchaResponse = $request->request->get('h-captcha-response');
            $captchaValid = $this->verifyHCaptcha($hCaptchaResponse, $request);

            if (!$captchaValid) {
            
                $error = 'CAPTCHA verification failed. Please try again.';
            }
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'hCaptchaSiteKey' => $hCaptchaSiteKey]);
    }

    private function verifyHCaptcha($response, Request $request): bool
    {
$secret = $_ENV['HCAPTCHA_SECRET_KEY'];
        $url = 'https://hcaptcha.com/siteverify';

        $data = http_build_query([
            'secret' => $secret,
            'response' => $response,
            'remoteip' => $request->getClientIp()
        ]);

        $opts = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => $data
            ]
        ];

        $context = stream_context_create($opts);
        $verificationResponse = file_get_contents($url, false, $context);
        $verificationData = json_decode($verificationResponse);

        return $verificationData && $verificationData->success;
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }
}
