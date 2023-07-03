<?php declare(strict_types=1);
/**
 *  RocketWeb
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Open Software License (OSL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/osl-3.0.php
 *
 * @category  RocketWeb
 * @copyright Copyright (c) 2020 RocketWeb (http://rocketweb.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author    Rocket Web Inc.
 */
namespace RocketWeb\CaptchaBypass\Plugin;

use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use RocketWeb\CaptchaBypass\Config\Provider;

/**
 * The after-plugin in which we fetch the cookie value from the browser, and we compare it to what it should be
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class IsCaptchaEnabledPlugin
{
    private \Magento\Framework\Stdlib\Cookie\CookieReaderInterface $cookieReader;
    private \RocketWeb\CaptchaBypass\Config\Provider $configProvider;
    private \Psr\Log\LoggerInterface $logger;

    public function __construct(
        \Magento\Framework\Stdlib\Cookie\CookieReaderInterface $cookieReader,
        \RocketWeb\CaptchaBypass\Config\Provider $configProvider,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->cookieReader = $cookieReader;
        $this->configProvider = $configProvider;
        $this->logger = $logger;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterIsCaptchaEnabledFor(IsCaptchaEnabledInterface $subject, $result)
    {
        if ($result === false) {
            // If Captcha is disabled, then no point of checking if bypass is needed
            return false;
        }

        $cookieData = (string)$this->cookieReader->getCookie(Provider::COOKIE_KEY);
        if (!$cookieData) {
            // cookie with data not set, skipping bypass
            return $result;
        }

        $secretKey = $this->configProvider->getSecretKey();
        if (!$secretKey) {
            // Bypass Secret Key empty/not-set, skipping bypass
            return $result;
        }

        /**
         * Define 1h window in which we look for hash values to compensate for any miss-configurations of system clocks
         */
        $timestamp = time();
        $start = $timestamp - 1800;
        $end = $timestamp + 1800;

        for ($i = $start; $i <= $end; $i++) {
            // since we are not using md5() for security reasons, it's safe to ignore it
            // @phpcs:ignore
            $hash = md5($secretKey . '-' . $i);
            if ($hash === $cookieData) {
                // We found a match for the hash, we are disabling captcha
                return false;
            }
        }
        // Log the missmatch. With cookie set, something had to go wrong?
        $this->logger->info(
            'RecaptchaBypass mismatch. Cookie data: ' . $cookieData . ' | start: ' . $start
            . ' | end: ' . $end . ' | key: ' . $secretKey
        );

        return $result;
    }
}
