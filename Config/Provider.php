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
namespace RocketWeb\CaptchaBypass\Config;

/**
 * Standard configuration provider class.
 */
class Provider
{
    private const XML_BYPASS_SECRET_KEY = 'recaptcha_frontend/bypass/secret_key';
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public const COOKIE_KEY = '__rbp';

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getSecretKey(): ?string
    {
        $secretKey = $this->scopeConfig->getValue(self::XML_BYPASS_SECRET_KEY);
        if (!$secretKey) {
            return null;
        }

        return $secretKey;
    }
}
