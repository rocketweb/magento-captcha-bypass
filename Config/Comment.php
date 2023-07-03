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

use Magento\Config\Model\Config\CommentInterface;

/**
 * Simple class that takes care of showing additional code section of the comment with simple instructions
 * on how to include it inside of Cypress Test
 */
class Comment implements CommentInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCommentText($elementValue): string
    {
        $html = '<p>This secret key gets used to match the Automated Test Sessions and disables ReCaptcha.</p>';
        $html .= '<p>To activate the Bypass, add the following to the Cypress Test:</p>
        <div style="background-color:lightgrey;">
        <code><pre>

        let secretKey = \'-the-value-from-above-\'
        let date = new Date(). getTime()
        let hash = CryptoJS.MD5(secretKey + \'-\' + date)
        cy.setCookie(\'' . Provider::COOKIE_KEY . '\', hash);
        </pre></code>
                </div>';

        return $html;
    }
}
