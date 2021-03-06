<?php
/**
 * Memcached
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Cache\Adapter;

use Exception;
use Memcached as phpMemcached;
use CommonApi\Exception\RuntimeException;
use Molajo\Cache\CacheItem;
use CommonApi\Cache\CacheInterface;

/**
 * Memcached Cache
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class Memcached extends AbstractAdapter implements CacheInterface
{
    /**
     * Memcached Instance
     *
     * @var    object
     * @since  1.0
     */
    protected $memcached;

    /**
     * Constructor
     *
     * @param  string $cache_handler
     *
     * @since  1.0
     */
    public function __construct(array $options = array())
    {
        $this->cache_handler = 'Memcached';

        $this->connect($options);
    }

    /**
     * Connect to Cache
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function connect($options = array())
    {
        parent::connect($options);

        if (extension_loaded('memcached') && class_exists('\\Memcached')) {
        } else {
            throw new RuntimeException
            (
                'Cache: Memcached not supported.'
            );
        }

        $pool = null;
        if (isset($options['memcached_pool'])) {
            $pool = $options['memcached_pool'];
        }

        $compression = false;
        if (isset($options['memcached_compression'])) {
            $compression = $options['memcached_compression'];
        }

        $servers = array();
        if (isset($options['memcached_servers'])) {
            $servers = $options['memcached_servers'];
        }

        $this->memcached = new phpMemcached($pool);

        $this->memcached->setOption(phpMemcached::OPT_COMPRESSION, $compression);
        $this->memcached->setOption(phpMemcached::OPT_LIBKETAMA_COMPATIBLE, true);

        $serverList = $this->memcached->getServerList();

        if (empty($serverList)) {
            foreach ($servers as $server) {
                $this->memcached->addServer($server->host, $server->port);
            }
        }

        return $this;
    }

    /**
     * Return cached value
     *
     * @param   string $key
     *
     * @return  null|mixed cached value
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function get($key)
    {
        if ($this->cache_enabled == 0) {
            return false;
        }

        try {
            $value = $this->memcached->get($key);

            $results = $this->memcached->getResultCode();

            if (phpMemcached::RES_SUCCESS == $results) {
            } elseif (phpMemcached::RES_NOTFOUND == $results) {
                return null;
            } else {
                throw new RuntimeException
                (
                    sprintf(
                        'Unable to fetch cache entry for %s. Error message `%s`.',
                        $key,
                        $this->memcached->getResultMessage()
                    )
                );
            }
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Cache: Get Failed for Memcached ' . $key . $e->getMessage()
            );
        }

        return new CacheItem($key, $value, (bool)$value);
    }

    /**
     * Create a cache entry
     *
     * @param   null    $key
     * @param   null    $value
     * @param   integer $ttl (number of seconds)
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function set($key, $value = null, $ttl = 0)
    {
        if ($this->cache_enabled == 0) {
            return false;
        }

        if ($key === null) {
            $key = serialize($value);
        }

        if ((int)$ttl == 0) {
            $ttl = (int)$this->cache_time;
        }

        $results = $this->memcached->set($key, $value, (int)$ttl);

        if (phpMemcached::RES_SUCCESS == $results) {
        } else {
            throw new RuntimeException
            (
                'Cache Memcached Adapter: Set failed for Key: ' . $key
            );
        }

        return $this;
    }

    /**
     * Remove cache for specified $key value
     *
     * @param   string $key
     *
     * @return  object
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function remove($key = null)
    {
        try {
            $this->memcached->delete($key);

            $results = $this->memcached->getResultCode();

            if (phpMemcached::RES_SUCCESS == $results) {
            } elseif (phpMemcached::RES_NOTFOUND == $results) {
            } else {
                throw new RuntimeException
                (
                    sprintf(
                        'Unable to remove cache entry for %s. Error message `%s`.',
                        $key,
                        $this->memcached->getResultMessage()
                    )
                );
            }
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Cache: Get Failed for Memcached ' . $key . $e->getMessage()
            );
        }

        return $this;
    }

    /**
     * Clear all cache
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function clear()
    {
        try {
            $this->memcached->flush();

            $results = $this->memcached->getResultCode();

            if (phpMemcached::RES_SUCCESS == $results) {
            } elseif (phpMemcached::RES_NOTFOUND == $results) {
            } else {
                throw new RuntimeException('Unable to flush Memcached.');
            }
        } catch (Exception $e) {
            throw new RuntimeException
            (
                'Cache: Flush Failed for Memcached ' . $e->getMessage()
            );
        }
    }
}
