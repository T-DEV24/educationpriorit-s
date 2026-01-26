<?php

declare(strict_types=1);

const JWT_SECRET_KEY = getenv('JWT_SECRET_KEY') ?: 'change-this-secret';
const JWT_ISSUER = getenv('JWT_ISSUER') ?: 'EducationPriorite';
const JWT_TTL_SECONDS = (int) (getenv('JWT_TTL_SECONDS') ?: 86400);
