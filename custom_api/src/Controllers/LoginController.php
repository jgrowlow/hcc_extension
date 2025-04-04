<?php
namespace CustomApi\Controllers;

use Flarum\User\User;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface as Request;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use CustomApi\Services\NeonCRMService;
use Flarum\User\Authenticator;
use Flarum\Foundation\ValidationException;
use Flarum\Http\Middleware\CheckCsrfToken;
use Psr\Log\LoggerInterface;

class LoginController
{
    protected $neonService;
    protected $authenticator;
    protected $logger;

    public function __construct(NeonCRMService $neonService, Authenticator $authenticator, LoggerInterface $logger)
    {
        $this->neonService = $neonService;
        $this->authenticator = $authenticator;
        $this->logger = $logger;
    }

    public function __invoke(Request $request)
    {
        // Validate CSRF token
        $csrfToken = $request->getHeader('X-CSRF-Token')[0] ?? '';
        $this->logger->info('CSRF Token: ' . $csrfToken);

        if (!CheckCsrfToken::isValid($csrfToken)) {
            $this->logger->error('CSRF token mismatch');
            return new JsonResponse(['error' => 'CSRF token mismatch'], 400);
        }

        try {
            $body = $request->getParsedBody();
            $accountId = Arr::get($body, 'accountId');  // We're using accountId now, not userId
            $this->logger->info('Account ID: ' . $accountId);

            if (!$accountId) {
                throw new ValidationException(['error' => 'Account ID is required']);
            }

            // Directly match the user by accountId (assuming it's stored in Flarum's User table)
            $user = User::where('neoncrm_account_id', $accountId)->first();  // Assuming you store the NeonCRM account ID in the `neoncrm_account_id` field in your Flarum User model
            $this->logger->info('User: ' . ($user ? $user->id : 'not found'));

            if (!$user) {
                return new JsonResponse(['error' => 'User not found in forum'], 404);
            }

            RequestUtil::getActor($request)->login($user);

            $redirectUrl = getenv('FLARUM_REDIRECT_URL') ?: '/';
            $this->logger->info('Redirect URL: ' . $redirectUrl);

            return new RedirectResponse($redirectUrl);
        } catch (\Exception $e) {
            $this->logger->error('An unexpected error occurred: ' . $e->getMessage());
            return new JsonResponse(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }
}
