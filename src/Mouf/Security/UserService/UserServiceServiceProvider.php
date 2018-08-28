<?php


namespace Mouf\Security\UserService;

use Mouf\Utils\Session\SessionManager\SessionManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use TheCodingMachine\Funky\Annotations\Factory;
use TheCodingMachine\Funky\ServiceProvider;

class UserServiceServiceProvider extends ServiceProvider
{
    /**
     * @Factory(aliases={UserServiceInterface::class})
     */
    public static function createUserService(UserDaoInterface $userDao, LoggerInterface $logger, SessionManagerInterface $sessionManager, ContainerInterface $container): UserService
    {
        $sessionPrefix = '';
        if ($container->has('secret')) {
            $sessionPrefix = $container->get('secret');
        }
        return new UserService($userDao, $logger, $sessionManager, $sessionPrefix);
    }
}
