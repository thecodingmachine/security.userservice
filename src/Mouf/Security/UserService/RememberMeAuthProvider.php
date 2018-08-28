<?php
namespace Mouf\Security\UserService;

use Mouf\Utils\Common\ConditionInterface\ConditionInterface;

/**
 * This class will implement the "remember me" functionality.
 * It plugs to the userService on both authenticationListener and authenticationProvider properties
 */
class RememberMeAuthProvider implements AuthenticationProviderInterface, AuthenticationListenerInterface
{


    /**
     * The rememberMeProvider will provide "context specific" functions
     * to get the user by it's token, or assign a token to a given user.
     *
     * @var RememberMeProviderInterface
     */
    private $rememberMeProvider;

    /**
     * The duration of the remember me cookie : can be either an integer value in which
     * case it will be handled as minutes, or a string entering in the strtotime function, e.g. '1 month'
     *
     * @var int|string $expire
     */
    private $expire;

    /**
     * The URL for which the cookie is valid
     *
     * @var string $url
     */
    private $url;

    /**
     * The name of the rememerMe cookie
     *
     * @var string $cookieName
     */
    private $cookieName;

    /**
     * Tells when the remember me feature is active
     * @var ConditionInterface
     */
    private $activateCondition;

    /**
     * @param RememberMeProviderInterface $rememberMeProvider
     * @param string|int $expire the duration of the remember me cookie : can be either an integer value in which case it will be handled as minutes, or a string entering in the strtotime function, e.g. '1 month'
     * @param string $url the URL for which the cookie is valid
     * @param string $cookieName the name of the rememerMe cookie
     * @param ConditionInterface $activateCondition tells when the remember me feature is active
     */
    public function __construct(RememberMeProviderInterface $rememberMeProvider, $expire, string $url, string $cookieName, ConditionInterface $activateCondition)
    {
        $this->rememberMeProvider = $rememberMeProvider;
        $this->expire = $expire;
        $this->url = $url;
        $this->cookieName = $cookieName;
        $this->activateCondition = $activateCondition;
    }


    /**
     * (non-PHPdoc)
     * @see \Mouf\Security\UserService\IsLoggedProviderInterface::isLogged()
     */
    public function isLogged(UserServiceInterface $userService): bool
    {
        $user = $this->getAndLogUserByCookie($userService);
        if ($user) {
            $this->refreshRememberCookie($user, false);
            return true;
        } else {
            return false;
        }
    }

    /**
     * (non-PHPdoc)
     * @see \Mouf\Security\UserService\AuthenticationProviderInterface::getUserId()
     */
    public function getUserId(UserServiceInterface $userService)
    {
        $user = $this->getAndLogUserByCookie($userService);
        return $user ? $user->getId() : null;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Mouf\Security\UserService\AuthenticationProviderInterface::getUserLogin()
     */
    public function getUserLogin(UserServiceInterface $userService): ?string
    {
        $user = $this->getAndLogUserByCookie($userService);
        return $user ? $user->getLogin() : null;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Mouf\Security\UserService\AuthenticationProviderInterface::getLoggedUser()
     */
    public function getLoggedUser(UserServiceInterface $userService): ?UserInterface
    {
        return $this->getAndLogUserByCookie($userService);
    }

    /**
     * @param UserServiceInterface $userService
     */
    public function beforeLogOut(UserServiceInterface $userService): void
    {
        if (isset($_COOKIE[$this->cookieName])) {
            unset($_COOKIE[$this->cookieName]);
            setcookie($this->cookieName, '', time() - 3600, $this->url);
        }
    }

    /**
     * @param UserServiceInterface $userService
     */
    public function afterLogIn(UserServiceInterface $userService): void
    {
        if ($this->activateCondition->isOk($_REQUEST)) {
            $user = $userService->getLoggedUser();
            /* @var $adminUser AdminUserBean */
            $this->refreshRememberCookie($user);
        }
    }

    /**
     * Refresh the expiration time of the remember me cookie.
     * If $gerenateToken is passed to false, then the value of the cookie is not recaculated
     * @param UserInterface $user
     * @param bool $generateToken
     */
    private function refreshRememberCookie(UserInterface $user, bool $generateToken = true): void
    {
        $expire = is_numeric($this->expire) ? time() + 60 * $this->expire : strtotime("+" . $this->expire);
        if (!$generateToken) {
            $token = $_COOKIE[$this->cookieName];
        } else {
            $token = $this->randomPassword(32);
        }

        $this->rememberMeProvider->setUserRememberMeToken($user, $token);

        setcookie($this->cookieName, $token, $expire, $this->url);
    }

    private function getAndLogUserByCookie(UserServiceInterface $userService): ?UserInterface
    {
        if (isset($_COOKIE[$this->cookieName])) {
            $token = $_COOKIE[$this->cookieName];
            $user = $this->rememberMeProvider->getUserByRememberMeToken($token);
            if ($user) {
                $userService->loginWithoutPassword($user->getLogin());
                return $user;
            }
        }
        return null;
    }

    private function randomPassword(int $length = 8): string
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}
