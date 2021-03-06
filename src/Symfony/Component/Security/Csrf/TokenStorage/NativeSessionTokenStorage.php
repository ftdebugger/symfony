<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Csrf\TokenStorage;

/**
 * Token storage that uses PHP's native session handling.
 *
 * @since  2.4
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class NativeSessionTokenStorage implements TokenStorageInterface
{
    /**
     * The namespace used to store values in the session.
     * @var string
     */
    const SESSION_NAMESPACE = '_csrf';

    /**
     * @var Boolean
     */
    private $sessionStarted = false;

    /**
     * @var string
     */
    private $namespace;

    /**
     * Initializes the storage with a session namespace.
     *
     * @param string  $namespace The namespace under which the token is stored
     *                           in the session
     */
    public function __construct($namespace = self::SESSION_NAMESPACE)
    {
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken($tokenId, $default = null)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        if (isset($_SESSION[$this->namespace][$tokenId])) {
            return $_SESSION[$this->namespace][$tokenId];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($tokenId, $token)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        $_SESSION[$this->namespace][$tokenId] = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function hasToken($tokenId)
    {
        if (!$this->sessionStarted) {
            $this->startSession();
        }

        return isset($_SESSION[$this->namespace][$tokenId]);
    }

    private function startSession()
    {
        if (version_compare(PHP_VERSION, '5.4', '>=')) {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }
        } elseif (!session_id()) {
            session_start();
        }

        $this->sessionStarted = true;
    }
}
