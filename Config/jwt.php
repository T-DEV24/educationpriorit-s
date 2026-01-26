<?php

declare(strict_types=1);

if (!defined('JWT_SECRET_KEY')) {
    define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: 'change-this-secret');
}
if (!defined('JWT_ISSUER')) {
    define('JWT_ISSUER', getenv('JWT_ISSUER') ?: 'EducationPriorite');
}
if (!defined('JWT_TTL_SECONDS')) {
    define('JWT_TTL_SECONDS', (int) (getenv('JWT_TTL_SECONDS') ?: 86400));
}
