# About Framework

## Lumen
Lumen is one of the fastest micro-framework. It is used for building Laravel based microservices. Find More details on Lumen.

## Version
Laravel Framework Lumen: 5.7.8
Laravel Components: 5.7.*
PHP: 7.3.0alpha3
MySql: 5.7.18

## Additional Key Packages
tymon/jwt-auth (Json Web Token Documentation, JWT)
Generates token for safe url accessed. This comply Industry Standard Internet Engineering Task Force (IETF) RFC 7519.

## dingo/api
This package consist of set of tools help to develop flexible system. Some of the tools are error handling, responses, throttling, transformers etc. documentation

## phpunit/phpunit
PHPUnit is a programmer-oriented testing framework for PHP.
It is an instance of the xUnit architecture for unit testing frameworks phpunit

# System Details

## Throttling (Rate Limiting)
Each API is limited to 100 request for second. Client will be banned for 3 minute if the request limit is crossed. Request is count based on IP. This can be configurable if needed.

## HTTPS
Only HTTPS call will be responded by the server to ensure secure channel communication.

## JWT
After Email Registration almost all API’s are tokenized with Json Web Token. This token is signed by server secret key and also stores custom claims like (ip address for now). This token can be easily blacklisted upon suspicious behaviour detection.

## LOG
Most of the Activities with system API are logged with appropriate flag . This will help later to detect anomalies or in the analysis or behavior study. Log are stored on daily basis file.

## Versioning
Every API has a version level (initially v1). This can be easily managed and can be moved to next version (let’s say v2) by keeping v1 or completely disabling v1.

## Mail
Currently SMTP (Simple Mail Transfer Protocol) is used for mailing the recipients. I’m using my gmail account for testing purposes. Later we can move to other popular mail services. Lumen supports SMTP Mailgun, Mandrill, Amazon SES.

# Database
## Table Schema
Users
CREATE TABLE users (
id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
name varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
email varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
password varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
verified tinyint(1) NOT NULL DEFAULT ‘0’,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY users_email_unique (email)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

Password Resets
CREATE TABLE password_resets (
email varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
token varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
created_at timestamp NULL DEFAULT NULL,
KEY password_resets_email_index (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

Verify Users
CREATE TABLE verify_users (
user_id int(11) NOT NULL,
token varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

Referral Codes
CREATE TABLE referral_codes (
id int(10) unsigned NOT NULL AUTO_INCREMENT,
user_id int(10) unsigned NOT NULL,
code varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id),
UNIQUE KEY referral_codes_code_unique (code)
) ENGINE=InnoDB AUTO_INCREMENT=689 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

Referrals
CREATE TABLE referrals (
id int(10) unsigned NOT NULL AUTO_INCREMENT,
referred_by int(10) unsigned NOT NULL,
referred_to varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
is_registered tinyint(1) NOT NULL DEFAULT ‘0’,
created_at timestamp NULL DEFAULT NULL,
updated_at timestamp NULL DEFAULT NULL,
PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
