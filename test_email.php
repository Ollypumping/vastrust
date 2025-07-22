<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Helpers\MailerHelper;

MailerHelper::sendEmail('olayemiojo49@gmail.com', 'Test Email', 'This is a test');
