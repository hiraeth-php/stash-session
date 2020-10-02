<?php

namespace Hiraeth\Stash\Session;

use Cache\SessionHandler\Psr6SessionHandler;

/***
 *
 */
class Handler extends Psr6SessionHandler
{
	/**
	 * Overload the gc function to restore basic garbage collection
	 */
	public function gc($lifetime)
	{
		$this->cache->purge();

		return TRUE;
	}
}
