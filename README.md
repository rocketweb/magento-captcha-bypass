# RocketWeb Captcha Bypass
The extension disables Google reCAPTCHA by providing a simple hashed value thru a cookie

## Installation
Using composer:
```
composer2 require rocketweb/magento-captcha-bypass
```

## Setup
Once installed, you need to configure the extension in 
**Stores -> Settings -> Configuration** then open **Security (tab)
-> Google reCAPTCHA Storefront -> Bypass Storefront ReCaptcha** and set a unique
random string for Secret Key field:
![Screenshot from Admin displaying Secret Key field](https://user-images.githubusercontent.com/9031414/250605077-4fc259d6-3f7a-4001-8154-4ca4893d7eb8.png)

## Usage
First, you need to add the Secret Key to the `cypress.config.js` you are using:
```
module.exports = defineConfig({
    projectId: "xxxxxx",
    e2e: {
        ...
        secretKey: '-key-from-magento-configuration-',
        ....
```

Next, you need to add ``CryptoJs`` library (or something similar that supports MD5):
```
npm install crypto-js
```
If all your packages are dev-dependencies, install this also as dependency:
```
npm install crypto-js  --save-dev
```
Then include the crypto-js into at the top of the Cypress Test file:
```
# ... (other import lines) ...
import CryptoJS from 'crypto-js';
```
The final step is setting the Cookie needed to activate the Recaptcha Bypass 
inside the `it()` before any `cy.visit(...)` is called:
```
it(['Can create an account', () => {
    let secretKey = Cypress.config('secretKey')
    let date = new Date(). getTime()
    let hash = CryptoJS.MD5(secretKey + '-' + date).toString(CryptoJS.enc.Hex)
    cy.setCookie('__rbp', hash);

    cy.visit(...)
    ...
})
```

This will add a cookie with specific hash that is than recognized by Magento code
which disabled ReCaptcha on the page (if it's enabled that is).
