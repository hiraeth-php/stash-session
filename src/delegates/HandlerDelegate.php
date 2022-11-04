<?php

namespace Hiraeth\Stash\Session;

use Hiraeth;
use Hiraeth\Caching;
use RuntimeException;
use Stash\Pool;

/**
 *
 */
class HandlerDelegate implements Hiraeth\Delegate
{
	/**
	 * @var Caching\PoolManager
	 */
	protected $manager;


	/**
	 * {@inheritDoc}
	 */
	static public function getClass(): string
	{
		return Handler::class;
	}


	/**
	 *
	 */
	public function __construct(Caching\PoolManager $manager)
	{
		$this->manager = $manager;
	}


	/**
	 * {@inheritDoc}
	 */
	public function __invoke(Hiraeth\Application $app): object
	{
		$pool = $this->manager->get('session');
		$ttl  = $app->getEnvironment('SESSION_TTL', ini_get('session.gc_maxlifetime'));

		if (!$pool instanceof Pool) {
			throw new RuntimeException(sprintf(
				'Attempting to get cache pool resulted in non-stash cache pool %s.',
				get_class($pool)
			));
		}

		return new Handler($pool, ['ttl' => $ttl]);
	}
}
