<?php 
declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\User;

interface NotificationStrategy {

    public function send(User $user, string $message): void;

}