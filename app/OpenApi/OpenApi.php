<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'GCMS API',
    description: 'Dokumentasi dasar REST API v1 Government Complaint Management System.'
)]
#[OA\Server(url: '/', description: 'Aplikasi GCMS')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class OpenApi {}
