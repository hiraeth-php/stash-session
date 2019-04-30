<?php

namespace Hiraeth\Stash\Session;

use Hiraeth;
use Cache\SessionHandler\Psr6SessionHandler;

/**
 *
 */
class HandlerDelegate implements Hiraeth\Delegate
{
	/**
	 * Get the class for which the delegate operates.
	 *
	 * @static
	 * @access public
	 * @return string The class for which the delegate operates
	 */
	static public function getClass(): string
	{
		return Psr6SessionHandler::class;
	}


	/**
	 *
	 */
	public function __construct(Hiraeth\Caching\PoolManagerInterface $manager)
	{
		$this->manager = $manager;
	}


	/**
	 * Get the instance of the class for which the delegate operates.
	 *
	 * @access public
	 * @param Hiraeth\Application $app The application instance for which the delegate operates
	 * @return object The instance of the class for which the delegate operates
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$pool = $this->manager->get('session');
		$ttl  = $app->getEnvironment('SESSION.TTL', ini_get('session.gc_maxlifetime'));

		return new Psr6SessionHandler($pool, ['ttl' => $ttl]);
	}
}
