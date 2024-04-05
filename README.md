
<p align="center">
<a href="https://www.codetriage.com/magento/magento2"><img src="https://www.codetriage.com/magento/magento2/badges/users.svg" alt="Open Source Helpers" /></a>
<a href="https://gitter.im/magento/magento2?utm_source=badge&amp;utm_medium=badge&amp;utm_campaign=pr-badge"><img src="https://badges.gitter.im/Join%20Chat.svg" alt="Gitter" /></a> <a href="https://crowdin.com/project/magento-2"><img src="https://d322cqt584bo4o.cloudfront.net/magento-2/localized.svg" alt="Crowdin" /></a><br/>
<a href="https://magento.com/products/magento-open-source">
<img alt="Adobe logo" height="50px" src="https://www.adobe.com/content/dam/cc/icons/Adobe_Corporate_Horizontal_Red_HEX.svg"/>
</a>
</p>

<h1 align="center">Magento Open Source</h1>

Welcome to the Magento Open Source project! [Magento Open Source](https://magento.com/products/magento-open-source) software delivers basic eCommerce capabilities to build a unique online store from the ground up.

However, for those who need a full-featured eCommerce solution, we recommend [Adobe Commerce](https://magento.com/products/magento-commerce), which includes our optimized cloud architecture and hosting as well as AI-powered merchandising and analytics.

## Dependencies
* You will need to have the [Google Retail](https://cloud.google.com/retail/docs) service enabled in GCP
* You will need to [import your product catalog](https://cloud.google.com/retail/docs/upload-catalog) into Google Retail (this can be achieved easily if your products are merchandised in the [Google Merchant Centre](https://www.google.com/retail/))
* You will need to capture specific user-event metrics in GA as [Recommendations AI](https://cloud.google.com/blog/topics/developers-practitioners/recommendations-ai-modeling) uses a number of [key user-events](https://cloud.google.com/retail/docs/user-events) to train the AI models (please read [Retail API requirements](https://cloud.google.com/retail/docs/user-events#retail-reqs) for general user-event requirements and [recommendations-specific requirements](https://cloud.google.com/retail/docs/user-events#rec-reqs) to further understand the `purchase-complete` event). Note the key fields to map a recommendation back to a user are the `visitorId` and the `userId`. This module assumes that in both cases the GA [`clientId`] and `CustomerIncrementId` respectively are being being used. 
* The `visitorId` should be able to uniquely identify a visitor on a single device. The recommendation is to use the GA [`clientId`](https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#clientId) as this is device specific. For a more granular level of perosnalisation you should also be capturing the `userId` when capturing the user events.
* You will need to create a Google Retail [service account](https://cloud.google.com/iam/docs/service-account-overview) to grant access to applications that need to integrate with the service
* Please consult supplementary details on [best practices](https://cloud.google.com/iam/docs/best-practices-service-accounts) for using GCP service accounts to support your own hosting environment  

## Get started
* Drop the module into your working environment in `app/code`
* Set the ready-made config details for the Product Detail Page (other models can easily be created reusing product lists)
* Tested on Magento 2.4.6-p1, 2.4.7-p3 (PHP 8.2)
* The blocks for the product lists bust the full page cache without invalidating the entire page  

## How to use
* Inject the `RetailAI_Recommendations::recommendation/view.phtml` template into the relevant block as per the catalog product view example, it requires the `placement-id` and `event-type` to be configured to surface the product recommendations from the relevant model.

## Learn About GraphQL in Magento 2
- [Quick start install](https://devdocs.magento.com/guides/v2.4/install-gde/composer.html)
- [System requirements](https://devdocs.magento.com/guides/v2.4/install-gde/system-requirements.html)
- [Prerequisites](https://devdocs.magento.com/guides/v2.4/install-gde/prereq/prereq-overview.html)
- [More installation options](https://devdocs.magento.com/guides/v2.4/install-gde/bk-install-guide.html)

## Get help

- [Help center](https://support.magento.com/hc/en-us)

## Contribute

Our [Community](https://opensource.magento.com/) is large and diverse, and our project is enormous. As a contributor, you have countless opportunities to impact product development and delivery by introducing new features or improving existing ones, enhancing test coverage, updating documentation for [developers](https://devdocs.magento.com/) and [end-users](https://docs.magento.com/user-guide/), catching and fixing code bugs, suggesting points for optimization, and sharing your great ideas.

- [Contribute to the code](https://devdocs.magento.com/contributor-guide/contributing.html)
- [Report an issue](https://devdocs.magento.com/contributor-guide/contributing.html#report)
- [Improve the developer documentation](https://github.com/magento/devdocs)
- [Improve the end-user documentation](https://github.com/magento/merchdocs)
- [Shape the future of Magento Open Source](https://developer.adobe.com/open/magento)

### Maintainers

We encourage experts from the Community to help us with GitHub routines such as accepting, merging, or rejecting pull requests and reviewing issues. Adobe has granted the Community Maintainers permission to accept, merge, and reject pull requests, as well as review issues. Thanks to invaluable input from the Community Maintainers team, we can significantly improve contribution quality and accelerate the time to deliver your updates to production. 

- [Learn more about the Maintainer role](https://devdocs.magento.com/contributor-guide/maintainers.html)
- [Maintainer's Handbook](https://devdocs.magento.com/contributor-guide/maintainer-handbook.html)

[![](https://raw.githubusercontent.com/wiki/magento/magento2/images/maintainers.png)](https://magento.com/magento-contributors#maintainers)

### Leaders

Adobe highly appreciates contributions that help us to improve the code, clarify the documentation, and increase test coverage. Check out our Community leaders, superstars, and superheroes on the [leaderboard](https://magento.biterg.io/app/kibana#/dashboard/41dc0c60-fa06-11eb-bbaa-dd6ca6f8fda8?_g=()).

[![](https://raw.githubusercontent.com/wiki/magento/magento2/images/contributors.png)](https://magento.com/magento-contributors)

### Labeling

We use labels in the GitHub issues and pull requests to help the participants retrieve additional information such as progress, component assignments, or release lines.

- [Labels applied by the Community Engineering team](https://devdocs.magento.com/contributor-guide/contributing.html#labels)

## Security

[Security](https://devdocs.magento.com/guides/v2.4/architecture/security_intro.html) is one of the highest priorities at Adobe. To learn more about reporting security concerns, visit the [Adobe Bug Bounty Program](https://hackerone.com/adobe).

Stay up-to-date on the latest security news and patches by signing up for [Security Alert Notifications](https://magento.com/security/sign-up).

## Licensing

Each Magento source file included in this distribution is licensed under OSL 3.0 or the terms and conditions of the applicable ordering document between Licensee/Customer and Adobe (or Magento).
 
[Open Software License (OSL 3.0)](https://opensource.org/licenses/osl-3.0.php) – Please see [LICENSE.txt](LICENSE.txt) for the full text of the OSL 3.0 license.
 
Subject to Licensee's/Customer's payment of fees and compliance with the terms and conditions of the applicable ordering document between Licensee/Customer and Adobe (or Magento), the terms and conditions of the applicable ordering between Licensee/Customer and Adobe (or Magento) supersede the OSL 3.0 license for each source file.

## Communications

We are dedicated to our Community and encourage your contributions and welcome feedback through [events](https://www.adobe.io/open/magento/calendar), our [DevBlog](https://community.magento.com/t5/Magento-DevBlog/bg-p/devblog), Twitter and YouTube channels, and [other Community resources](https://devdocs.magento.com/community/resources.html).

To connect with people from the Community and Adobe engineering, [join us in Slack](https://magentocommeng.slack.com). We have a channel for every project. To join a particular channel, send us a request at [engcom@adobe.com](mailto:engcom@adobe.com), or [sign up](https://opensource.magento.com/slack).

- [Popular Slack channels](https://www.adobe.io/open/magento/slack)

If you are a new Community member, check out the following channels:

- [general](https://magentocommeng.slack.com/archives/C4YS78WE6) is an open chat for introductions and Magento 2 questions
- [github](https://magentocommeng.slack.com/archives/C7KB93M32) is a support channel for GitHub issues, pull requests, and processes
- [public-backlog](https://magentocommeng.slack.com/archives/CCV3J3RV5) for discussions of the backlog
