<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, Security $security)
    {
        $this->urlGenerator = $urlGenerator;
        $this->security = $security;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if (!$this->isCaptchaValid($request)) {
            throw new CustomUserMessageAuthenticationException('Invalid CAPTCHA.');
        }
    
        $role = $this->security->getUser()->getRole();

        if ($role === 'Admin') {
            return new RedirectResponse($this->urlGenerator->generate('app_base'));
        } elseif ($role === 'Client') {
            return new RedirectResponse($this->urlGenerator->generate('app_front'));
        }
    
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
    private function isCaptchaValid(Request $request): bool
    {
        $hCaptchaResponse = $request->request->get('h-captcha-response');
        if (!$hCaptchaResponse) {
            return false;
        }
        $secret = $_ENV['HCAPTCHA_SECRET_KEY'];
        $url = 'https://hcaptcha.com/siteverify';
        $response = file_get_contents($url, false, stream_context_create([
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query([
                    'secret' => $secret,
                    'response' => $hCaptchaResponse,
                    'remoteip' => $request->getClientIp()
                ])
            ]
        ]));

        $result = json_decode($response, true);
        return $result['success'] ?? false;
    }
}
