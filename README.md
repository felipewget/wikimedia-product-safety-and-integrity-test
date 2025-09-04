This repository contains an application for viewing and editing text documents. Users of the application
can:

* Create new pages
* Edit any existing page
* View a list of existing content

## Your task

After reading through the code and familiarizing yourself with the repository, use not more than 3 hours of your time to address the following questions:

Question 1

In `api.php`, work through the TODOs using your
creativity and judgment. Please only update the api.php file.

TODO A: Improve readability and clean up the following code to prepare for adding new handlers and routes.
* "echo json_encode()" is being repeated many times, I'll create on "response" method/function for that
* if/else if/else can be hard to follow, I'll try to use one match(true) or if the condition keeps to one variable, one looping checking the route by param
* I'll create one handler for each route
* We have one structural code on api.php, I'm not sure if I should convert this code to OOP, even more because all updates will keep on api.php, everything will keep on api.php(as required) and I'll add the repeated code not linked with handlers to one kind of utils/helpers and I'll add one addapter pattern for repeated code linked with handler classes. I can develop in structural way as well, let me know pls if you would like to see it in structural code (: 
* simplified logic

// TODO B: Address performance concerns.
* Limit and pagination could be good, pagination was added on all article list and prefix list(http://localhost:8989/api.php?prefix=b&page=2, http://localhost:8989/api.php?prefix=b&page=1)
* cache-aside would be nice as well (We are indexing the list of articles, I think this like of list can delay 5 minutes to be updates). With it we can protect ourseves againg DDOS as well I think
* indexes on database(BTREE) on prefix search(once it's on db)
* Inecessary variables

// TODO C: Identify and solve any potential security vulnerabilities in this code.
* $this->response($this->app->fetch($_GET)); allowing to pass any param, updated to ['title' => $this->title]
* Sanetization of inputs with Util::GetParam()
* too long params, forcing the CPU and memory for comparisons
* searching by title, if we don't have the article, one warning is returned, I think I can treat it to prevent "<b>Warning</b>:  file_get_contents(articles/Fooasdasd): Failed to open stream: No such file or directory in <b>/Users/.../untitled folder/takehome-task-main/src/App.php</b> on line <b>22</b><br />. Now I'm returning 404 if the file not exist -> $this->response(['error' => 'Article not found'], 404);
[Thu Sep  4 07:38:43 2025] [::1]:55273 [200]: GET /api.php?prefix=foo
[Thu Sep  4 07:38:43 2025] [::1]:55273 Closing
[Thu Sep  4 07:38:43 2025] [::1]:55274 Accepted
[Thu Sep  4 07:38:44 2025] [::1]:55275 Accepted
[Thu Sep  4 07:38:45 2025] [::1]:55274 [200]: GET /api.php?prefix=fooasdsadsada
[Thu Sep  4 07:38:45 2025] [::1]:55274 Closing
[Thu Sep  4 07:38:47 2025] [::1]:55276 Accepted
[Thu Sep  4 07:38:47 2025] [::1]:55275 [400]: GET /api.php?prefix=fooasdsadsadaasdasdsadsa
[Thu Sep  4 07:38:47 2025] [::1]:55275 Closing
[Thu Sep  4 07:38:57 2025] [::1]:55278 Accepted
[Thu Sep  4 07:38:57 2025] [::1]:55276 [200]: GET /api.php?prefix=foo
[Thu Sep  4 07:38:57 2025] [::1]:55276 Closing
[Thu Sep  4 07:39:04 2025] [::1]:55279 Accepted
[Thu Sep  4 07:39:04 2025] [::1]:55278 [404]: GET /api.php?title=sdasdas
[Thu Sep  4 07:39:04 2025] [::1]:55278 Closing
[Thu Sep  4 07:39:06 2025] [::1]:55280 Accepted
[Thu Sep  4 07:39:06 2025] [::1]:55279 [200]: GET /api.php?title=Foo
[Thu Sep  4 07:39:06 2025] [::1]:55279 Closing`

Question 2

Anyone who visits our site can make an edit. We would like to store the IP
address of everyone who has made an edit, and which article they have edited so
that we can guess which articles may have been vandalized. For example, imagine
we have a classroom in a school, where there are a number of students who are
vandalizing articles at once, with IP addresses 72.1.3.4 - 72.1.3.89, and we'd
like a list of which articles they have edited.

Design a database table (or tables) that will allow the app to efficiently look
up:
* For a given article, the IP addresses of all the people who have edited it
* For a given IP range, which articles have been edited from IP addresses in that
range.

Please describe your table(s) in markdown form, and describe any indexes you
would add. (Don't actually implement a database.) For example:

|Column name|Data type|Description          |
|-----------|---------|---------------------|
|article_id |integer  |The ID of the article|
|...        |...      |...                  |

Primary key index on article_id.


## How your response will be evaluated

We are most interested in seeing how you think about the TODOs rather than whether
you have written perfect code in a very limited amount of time. We will use this exercise to ask related questions in the
in-person technical interview. Specifically, we will discuss code quality, accessibility, security and performance. 

We will also evaluate the messages you use in your Git commits, and that you have made your changes in small, logical commits.

### Note

Please do not use any additional external libraries for this exercise.

## Usage

Download [composer](https://getcomposer.org/), then:

1. `composer install` – installs dependencies for the application
2. `composer serve` - Serves the application
   1. Web UI is available at http://localhost:8989.
   2. API is available at http://localhost:8989/api.php
   3. If you need to change the port, you can do that in `composer.json`
3. `composer seed` – Generate seed content for the application
4. `composer test` – Lint files and run tests

## Submission

Please create a ZIP file of the git repository and send back to the recruiter.
