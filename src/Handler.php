<?php

namespace Hiraeth\Stash\Session;

use SessionHandlerInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 *
 * Original authors:
 *
 * (c) 2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Handler implements SessionHandlerInterface
{
	/**
	 * @var CacheItemPoolInterface
	 */
	private $cache;


	/**
	 * @var int Time to live in seconds
	 */
	private $ttl;


	/**
	 * @var string Key prefix for shared environments.
	 */
	private $prefix;


	/**
	 * @param CacheItemPoolInterface $cache
	 * @param array $options An array of options including 'ttl' and 'prefix
	 */
	public function __construct(CacheItemPoolInterface $cache, array $options = [])
	{
		$this->ttl    = isset($options['ttl'])? (int) $options['ttl'] : 86400;
		$this->prefix = isset($options['prefix']) ? $options['prefix'] : 'psr6ses_';
		$this->cache  = $cache;
	}


	/**
	 * {@inheritdoc}
	 */
	public function open($savePath, $sessionName)
	{
		return true;
	}


	/**
	 * {@inheritdoc}
	 */
	public function close()
	{
		return true;
	}


	/**
	 * {@inheritdoc}
	 */
	public function read($sessionId)
	{
		$item = $this->getCacheItem($sessionId);
		if ($item->isHit()) {
			return $item->get();
		}

		return '';
	}


	/**
	 * {@inheritdoc}
	 */
	public function write($sessionId, $data)
	{
		$item = $this->getCacheItem($sessionId);
		$item->set($data)
			->expiresAfter($this->ttl);

		return $this->cache->save($item);
	}


	/**
	 * {@inheritdoc}
	 */
	public function destroy($sessionId)
	{
		return $this->cache->deleteItem($this->prefix.$sessionId);
	}

	/**
	 * Overload the gc function to restore basic garbage collection
	 */
	public function gc($lifetime)
	{
		$this->cache->purge();

		return TRUE;
	}


	/**
	 * @return \Psr\Cache\CacheItemInterface
	 */
	private function getCacheItem($session_id)
	{
		return $this->cache->getItem($this->prefix . $session_id);
	}
}
