<?php
namespace Mouf\Security\UserService;

use Mouf\Utils\Common\ConditionInterface\ConditionInterface;
use Mouf\Utils\Value\ValueInterface;

/**
 * This class implements ValueInterface and returns the ID of the current user when the val()
 * method is called on it.
 * Throws a UserServiceException if noone is logged.
 */
class CurrentUserIdValue implements ValueInterface {

	/**
	 * @var UserServiceInterface
	 */
	protected $userService;
	
	/**
	 * @Important
	 * @param UserServiceInterface $userService The user service to call.
	 */
	public function __construct(UserServiceInterface $userService) {
		$this->userService = $userService;
	}
	
		
	/**
	 * Returns the value represented by this object.
	 * 
	 * @return mixed
	 */
	public function val() {
		$userId = $this->userService->getUserId();
		if ($userId === null) {
			throw new UserServiceException("No user logged.");
		}
		return $userId;
	}
}
?>