A PHP wrapper to be used with [Gitlab's API](https://github.com/gitlabhq/gitlabhq/tree/master/doc/api).
==============

[![Build Status](https://travis-ci.org/Jeroeny/gitlab-api.svg?branch=master)](https://travis-ci.org/jeroeny/gitlab-api)
[![StyleCI](https://styleci.io/repos/217726394/shield?branch=master)](https://github.styleci.io/repos/217726394)


Forked from [php-gitlab-api](https://github.com/jeroeny/gitlab-api) and based on [php-github-api](https://github.com/m4tthumphrey/php-github-api) and code from [KnpLabs](https://github.com/KnpLabs/php-github-api).

Installation
------------

Via [composer](https://getcomposer.org)

```bash
composer require jeroeny/gitlab-api
```

You can visit [HTTPlug for library users](http://docs.php-http.org/en/latest/httplug/users.html) to get more information about installing HTTPlug related packages.

General API Usage
-----------------

```php
$client = \Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN)
;

// or for OAuth2 (see https://github.com/m4tthumphrey/php-gitlab-api/blob/master/lib/Gitlab/HttpClient/Plugin/Authentication.php#L47)
$client = \Gitlab\Client::create('http://gitlab.yourdomain.com')
    ->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_OAUTH_TOKEN)
;

$project = $client->api('projects')->create('My Project', array(
  'description' => 'This is a project',
  'issues_enabled' => false
));

```

Example with Pager
------------------

to fetch all your closed issue with pagination ( on the gitlab api )

```php
$client = \Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN)
;
$pager = new \Gitlab\ResultPager($client);
$issues = $pager->fetchAll($client->api('issues'),'all',[null, ['state' => 'closed']]);

```



Model Usage
-----------

You can also use the library in an object oriented manner:

```php
$client = \Gitlab\Client::create('http://git.yourdomain.com')
    ->authenticate('your_gitlab_token_here', \Gitlab\Client::AUTH_URL_TOKEN)
;

# Creating a new project
$project = \Gitlab\Model\Project::create($client, 'My Project', array(
  'description' => 'This is my project',
  'issues_enabled' => false
));

$project->addHook('http://mydomain.com/hook/push/1');

# Creating a new issue
$project = new \Gitlab\Model\Project(1, $client);
$issue = $project->createIssue('This does not work.', array(
  'description' => 'This doesn\'t work properly. Please fix.',
  'assignee_id' => 2
));

# Closing that issue
$issue->close();
```

You get the idea! Take a look around ([API methods](https://github.com/jeroeny/gitlab-api/tree/master/lib/Gitlab/Api),
[models](https://github.com/m4tthumphrey/php-gitlab-api/tree/master/lib/Gitlab/Model)) and please feel free to report any bugs.

Framework Integrations
----------------------
- **Symfony** - https://github.com/Zeichen32/GitLabApiBundle
- **Laravel** - https://github.com/GrahamCampbell/Laravel-GitLab

If you have integrated GitLab into a popular PHP framework, let us know!

Contributing
------------

Feel free to fork and add new functionality and/or tests, I'll gladly accept decent pull requests.
