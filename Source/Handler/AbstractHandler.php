<?php
/**
 * Abstract Handler Cache
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Cache\Handler;

use Molajo\Cache\CacheItem;
use CommonApi\Cache\CacheInterface;

/**
 * Abstract Handler Cache
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class AbstractHandler implements CacheInterface
{
    /**
     * Cache Handler
     *
     * @var    string
     * @since  1.0
     */
    protected $cache_handler;

    /**
     * Cache
     *
     * @var    bool
     * @since  1.0
     */
    protected $cache_service = false;

    /**
     * Cache Time in seconds
     *
     * @var    Integer
     * @since  1.0
     */
    protected $cache_time = 86400;

    /**
     * Constructor
     *
     * @param   string $cache_handler
     * @param   array  $options
     *
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $this->connect($options);
    }

    /**
     * Connect to Cache
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function connect($options = array())
    {
        if (isset($options['cache_service'])) {
            $this->cache_service = (boolean)$options['cache_service'];
        }

        if (isset($options['cache_time'])) {
            $this->cache_time = $options['cache_time'];
        }

        if ((int)$this->cache_time === 0) {
            $this->cache_time = 86400;
        }

        return $this;
    }

    /**
     * Return cached value
     *
     * @param   string $key
     *
     * @return  CacheItem
     * @since   1.0
     */
    public function get($key)
    {
        return $this;
    }

    /**
     * Create a cache entry
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   integer $ttl (number of seconds)
     *
     * @return  $this
     * @since   1.0
     */
    public function set($key = null, $value, $ttl = 0)
    {
        return $this;
    }

    /**
     * Remove cache for specified $key value
     *
     * @param   string $key
     *
     * @return  $this
     * @since   1.0
     */
    public function remove($key = null)
    {
        return $this;
    }

    /**
     * Clear all cache
     *
     * @return  $this
     * @since   1.0
     */
    public function clear()
    {
        return $this;
    }
}