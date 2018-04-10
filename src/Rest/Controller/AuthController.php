<?php declare(strict_types=1);

namespace Shopware\Rest\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopware\Rest\Firewall\JWTAuthenticator;
use Shopware\Rest\Firewall\RestUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthController extends Controller
{
    /**
     * @var JWTAuthenticator
     */
    private $jwtAuthenticator;

    public function __construct(JWTAuthenticator $jwtAuthenticator)
    {
        $this->jwtAuthenticator = $jwtAuthenticator;
    }

    /**
     * Dummy route for JWT authentication
     *
     * @Route("/api/auth", name="api_auth")
     */
    public function auth(Request $request)
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            throw new NotAcceptableHttpException('Authentication only supported using the POST method.');
        }

        if ($request->headers->get('content-type') === 'application/json') {
            $content = json_decode($request->getContent(), true);

            $username = $content['username'] ?? '';
            $password = $content['password'] ?? '';

            $expiry = array_key_exists('expiry', $content) ? (int) $content['expiry'] : 0;
        } else {
            $username = $request->get('username');
            $password = $request->get('password');

            $expiry = (int) $request->get('expiry');
        }

        if (!$expiry) {
            $expiry = 3600;
        }

        if (false === $this->jwtAuthenticator->checkPassword($username, $password)) {
            throw new UnauthorizedHttpException('json', 'Invalid username and/or password.');
        }

        $token = $this->jwtAuthenticator->createToken([
            'username' => $username
        ]);

        return new JsonResponse([
            'token' => $token,
            'expiry' => time() + $expiry
        ]);
    }
}
