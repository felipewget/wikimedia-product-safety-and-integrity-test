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

|Table name: article_logs ("Primary key index on article_id.")                                                   |
|Column name      |Data type|Description                                               |
|-----------------|---------|----------------------------------------------------------|
|article_id       |integer  | The ID of the article                                    |
|current_version  |integer  |Version number of the article                             |
|hash             |string   |Hash of the previous article version                      |

* Primary key index on article_id.

---

|Table name: article_versioning_logs                                                                  |
|Column name      |Data type                 |Description                                             |
|-----------------|--------------------------|--------------------------------------------------------|
|id               |integer                   |Record ID                                               |
|article_id       |integer                   |The ID of the article                                   |
|editor_ip        |varbinary(16)             |Editor IP (IPv4/IPv6 in binary format)                  |
|article_category |string                    |Category of the Article                                 |
|version          |integer                   |Version number of the article                           |
|previous_hash    |string                    |Hash of the previous article version                    |
|user_agent       |string                    |User-Agent                                              |
|warning_level    |decimal(3, 2)             |article_size / Num. of suspicious words by category     |
|created_at       |timestamp                 |When the log happened                                   |

* Primary key index on id
* Foreign key / index on article_id (links to article_logs.article_id)
* Index on editor_ip (for IP range searches)
* BTREE index on article_id
* BTREE index on editor_ip
* Composite BTREE index on (article_id, editor_ip)
* Composite BTREE index on (article_id, version)
* Composite BTREE index on (editor_ip, article_id)
* BTREE index on article_category
* FULLTEXT index on user_agent
* BTREE index on previous_hash

How to get the articles between IPs:
SELECT DISTINCT article_id FROM article_versioning_logs
WHERE editor_ip BETWEEN INET6_ATON('72.1.3.4') AND INET6_ATON('72.1.3.89')
ORDER BY article_id;

Structure explanation: article_logs would represent the main and important data of current version with primary_key on article_id, we would have one versioning table with metadata, information and logs of article each version.

My first idea was to get the articles by IP, but thinking a bit more, these students could be running sophisticated operations with rotating IPs and VPSs, and in that case, it wouldn’t work as expected. For that reason, I’d like to ask if I could add a few more fields:
	•	Article category – to understand their intentions and why they are vandalizing the content.
	•	Version – we can easily check how many versions exist. If we have many versions with value 1, it means they are creating new articles as well, not just vandalizing old ones.
	•	User agent – even if they use a bot, rotating IP, or VPS, we can check which browsers they are using or user agent used by bots.
	•	Previous hash – similar to a blockchain, we create a hash to ensure that the previous version is indeed the real previous version and everything is consistent.

Additional things we could do and add these data to article_logs (I plan to include these in the table):
	•	Analyze the text emotions and text and add to one "human fast check" list.
	•	Identify the most used words in detected vandalized articles (by category). We could create one "collection of words" to human article check, if this article has x% of it content with these words, it goes to human check
* Note, I didn't develop this task for now but I've created the field "warning level", it would be results of emotions and words on article


---

*Note: I've seen some TODOs on index but I think it's part of other test because it's writtent o fix todos in api.php and based on time I think I shouldn't fix index.php, but I'm super excited to do it, let me know pls if I should fix todos on index.php as well (: *

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
