<?php
namespace Mouf\Security\UserService;

use Mouf\MoufException;
use Mouf\Utils\Log\LogInterface;
use Mouf\Utils\Session\SessionManager\SessionManagerInterface;
use Psr\Log\LoggerInterface;
use Mouf\Validator\MoufStaticValidatorInterface;
use Mouf\MoufManager;
use Mouf\Validator\MoufValidatorResult;

/**
 * This class can be used to login or logoff users, and get their object.
 * Please see documentation at: <a href="http://www.thecodingmachine.com/ext/mouf/doc/manage_users/userservice_package.html">http://www.thecodingmachine.com/ext/mouf/doc/manage_users/userservice_package.html</a>
 *
 * @Component
 */
class UserService implements UserServiceInterface
{
    /**
     * The user DAO
     *
     * @var UserDaoInterface
     */
    private $userDao;
    
    /**
     * The logger for this service
     *
     * @var LoggerInterface
     */
    private $log;
    
    /**
     * This is an array containing all components that should be notified
     * when a user logs in or logs out.
     * All components in this array should implement the AuthenticationListenerInterface
     * interface.
     * For instance, the MoufRightsService, that manages the rights of users is
     * one of those.
     *
     * @var array<AuthenticationListenerInterface>
     */
    private $authenticationListeners = [];

    /**
     * In case you have several Mouf applications using the UserService running on the same server, in the same domain, you
     * should use a different session prefix for each application in order to avoid "melting" the sessions.
     *
     * @var string
     */
    private $sessionPrefix;

    /**
     * The session manager interface.
     * If set, it will be used to init the session if the session was not started.
     *
     * @var SessionManagerInterface|null
     */
    private $sessionManager;
    
    /**
     * A list of authentication providers that will complete the default 'logged' status, and help retrieve the current user
     * @var array<AuthenticationProviderInterface>
     */
    private $authProviders = [];

    private $byPassIsLogged = false;

    public function __construct(UserDaoInterface $userDao, LoggerInterface $log, SessionManagerInterface $sessionManager = null, string $sessionPrefix = '')
    {
        $this->userDao = $userDao;
        $this->log = $log;
        $this->sessionManager = $sessionManager;
        $this->sessionPrefix = $sessionPrefix;
    }

    /**
     * Logs the user using the provided login and password.
     * Returns true on success, false if the user or password is incorrect.
     * A Mouf Exception is thrown if no session is initialized. Require the mouf/load.php file in Mouf to start a session.
     *
     * @param string $login
     * @param string $password
     * @return boolean.
     */
    public function login(string $login, string $password): bool
    {
        // Is a session mechanism available?
        if (!session_id()) {
            if ($this->sessionManager) {
                $this->sessionManager->start();
            } else {
                throw new UserServiceException("The session must be initialized before trying to login. Please use session_start().");
            }
        }

        // Let's regenerate the session ID to avoid session fixation attacks.
        if ($this->sessionManager) {
            $this->sessionManager->regenerateId();
        }
        
        // First, if we are logged, let's unlog the user.
        if ($this->isLogged()) {
            $this->logoff();
        }
        
        $user = $this->userDao->getUserByCredentials($login, $password);
        if ($user != null) {
            $this->log->debug("User '{login}' logs in.", array('login'=>$user->getLogin()));
            $_SESSION[$this->sessionPrefix.'MoufUserId'] = $user->getId();
            $_SESSION[$this->sessionPrefix.'MoufUserLogin'] = $user->getLogin();
            
            if (is_array($this->authenticationListeners)) {
                foreach ($this->authenticationListeners as $listener) {
                    $listener->afterLogIn($this);
                }
            }
            return true;
        } else {
            $this->log->debug("Identication failed for login '{login}'", array('login'=>$login));
            return false;
        }
    }
    
    /**
     * Logs the user using the provided login.
     * The password is not needed if you use this function.
     * Of course, you should use this functions sparingly.
     * For instance, it can be useful if you want an administrator to "become" another
     * user without requiring the administrator to provide the password.
     *
     * @param string $login
     */
    public function loginWithoutPassword(string $login): void
    {
        // First, if we are logged, let's unlog the user.
        if (!$this->byPassIsLogged && $this->isLogged()) {
            $this->logoff();
        }
        
        $user = $this->userDao->getUserByLogin($login);
        if ($user == null) {
            throw new UserServiceException("Unable to find user whose login is ".$login);
        }
        $this->log->debug("User '{login}' logs in, without providing a password.", array('login'=>$user->getLogin()));

        $_SESSION[$this->sessionPrefix.'MoufUserId'] = $user->getId();
        $_SESSION[$this->sessionPrefix.'MoufUserLogin'] = $user->getLogin();
        
        if (is_array($this->authenticationListeners)) {
            foreach ($this->authenticationListeners as $listener) {
                $listener->afterLogIn($this);
            }
        }
    }
    
    /**
     * Logs a user using a token. The token should be discarded as soon as it
     * was used.
     *
     * Returns false if the token is not valid, else true.
     *
     *
     * @param string $token
     * @return bool
     */
    public function loginViaToken(string $token): bool
    {
        $user = $this->userDao->getUserByToken($token);
        if ($user) {
            $this->loginWithoutPassword($user->getLogin());
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns "true" if the user is logged, "false" otherwise.
     *
     * @return boolean
     */
    public function isLogged(): bool
    {
        $this->byPassIsLogged = true;
        try {
            // Is a session mechanism available?
            if (!session_id()) {
                if ($this->sessionManager) {
                    $this->sessionManager->start();
                } else {
                    throw new UserServiceException("The session must be initialized before checking if the user is logged. Please use session_start().");
                }
            }
            
            if (isset($_SESSION[$this->sessionPrefix.'MoufUserId'])) {
                return true;
            } else {
                foreach ($this->authProviders as $provider) {
                    /* @var $provider AuthenticationProviderInterface */
                    if ($provider->isLogged($this)) {
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->byPassIsLogged = false;
            throw $e;
        }
        $this->byPassIsLogged = false;
        return false;
    }

    /**
     * Logs the user off.
     *
     */
    public function logoff(): void
    {
        // Is a session mechanism available?
        if (!session_id()) {
            if ($this->sessionManager) {
                $this->sessionManager->start();
            } else {
                throw new UserServiceException("The session must be initialized before trying to login. Please use session_start().");
            }
        }
        
        if (isset($_SESSION[$this->sessionPrefix.'MoufUserLogin'])) {
            $login = $_SESSION[$this->sessionPrefix.'MoufUserLogin'];
            if (is_array($this->authenticationListeners)) {
                foreach ($this->authenticationListeners as $listener) {
                    $listener->beforeLogOut($this);
                }
            }
            $this->log->debug("User '{login}' logs out.", array('login' => $login));
            unset($_SESSION[$this->sessionPrefix.'MoufUserId']);
            unset($_SESSION[$this->sessionPrefix.'MoufUserLogin']);
        }
    }
    
    /**
     * Returns the current user ID.
     *
     * @return string|int|null
     */
    public function getUserId()
    {
        // Is a session mechanism available?
        if (!session_id()) {
            if ($this->sessionManager) {
                $this->sessionManager->start();
            } else {
                throw new UserServiceException("The session must be initialized before checking if the user is logged. Please use session_start().");
            }
        }
        
        if (isset($_SESSION[$this->sessionPrefix.'MoufUserId'])) {
            return $_SESSION[$this->sessionPrefix.'MoufUserId'];
        } else {
            foreach ($this->authProviders as $provider) {
                /* @var $provider AuthenticationProviderInterface */
                $userId = $provider->getUserId($this);
                if ($userId) {
                    return $userId;
                }
            }
        }
        return null;
    }
    
    /**
     * Returns the current user login.
     *
     * @return string|null
     */
    public function getUserLogin(): ?string
    {
        // Is a session mechanism available?
        if (!session_id()) {
            if ($this->sessionManager) {
                $this->sessionManager->start();
            } else {
                throw new UserServiceException("The session must be initialized before checking if the user is logged. Please use session_start().");
            }
        }
        
        if (isset($_SESSION[$this->sessionPrefix.'MoufUserLogin'])) {
            return $_SESSION[$this->sessionPrefix.'MoufUserLogin'];
        } else {
            foreach ($this->authProviders as $provider) {
                /* @var $provider AuthenticationProviderInterface */
                $login = $provider->getUserLogin($this);
                if ($login) {
                    return $login;
                }
            }
        }
        return null;
    }
    
    /**
     * Returns the user that is logged (or null if no user is logged).
     *
     * return UserInterface
     */
    public function getLoggedUser(): ?UserInterface
    {
        // Is a session mechanism available?
        if (!session_id()) {
            if ($this->sessionManager) {
                $this->sessionManager->start();
            } else {
                throw new UserServiceException("The session must be initialized before checking if the user is logged. Please use session_start().");
            }
        }
        
        if (isset($_SESSION[$this->sessionPrefix.'MoufUserId'])) {
            return $this->userDao->getUserById($_SESSION[$this->sessionPrefix.'MoufUserId']);
        } else {
            foreach ($this->authProviders as $provider) {
                /* @var $provider AuthenticationProviderInterface */
                $user = $provider->getLoggedUser($this);
                if ($user) {
                    return $user;
                }
            }
        }
        return null;
    }
    
    /**
     * A list of authentication providers that will complete the default 'logged' status, and help retrieve the current user
     * @param array<AuthenticationProviderInterface> $providers
     */
    public function setAuthProviders(array $providers): void
    {
        $this->authProviders = $providers;
    }

    public function addAuthProvider(AuthenticationProviderInterface $authenticationProvider): void
    {
        $this->authProviders[] = $authenticationProvider;
    }

    /**
     * @param AuthenticationListenerInterface[] $authenticationListeners
     */
    public function setAuthenticationListeners(array $authenticationListeners): void
    {
        $this->authenticationListeners = $authenticationListeners;
    }

    public function addAuthenticationListener(AuthenticationListenerInterface $authenticationListener): void
    {
        $this->authenticationListeners[] = $authenticationListener;
    }
}
