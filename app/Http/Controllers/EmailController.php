<?php

namespace App\Http\Controllers;

use App\Mail\CustomNotificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Address;

class EmailController extends Controller
{
    public function sendCustomNotification(Request $request, $notificationContent, $subject, $toRecipients, $ccRecipients = [], $attachmentPaths = [])
    {
        // Validate that there are recipients for the email
        if (empty($toRecipients) && empty($ccRecipients)) {
            throw new \InvalidArgumentException('Email must have at least one recipient (To, Cc).');
        }

        // Convert email addresses to Address objects
        $toAddresses = [];
        foreach ($toRecipients as $toRecipient) {
            $toAddresses[] = new Address($toRecipient);
        }

        $ccAddresses = [];
        foreach ($ccRecipients as $ccRecipient) {
            $ccAddresses[] = new Address($ccRecipient);
        }

        // Send the email
        $sent = Mail::to($toAddresses)
            ->cc($ccAddresses)
            ->send(new CustomNotificationEmail($subject, $notificationContent, $attachmentPaths));

        // Check if there were any failures
        if ($sent) {
            // Email sent successfully to all recipients
            $response = ['type' => 'success', 'message' => 'Your message has been sent successfully, One of our team members will reach back to you shortly!'];
            return $response;
        } else {
            $response = ['type' => 'error', 'message' => 'Failed to send email to send your message, try again later!'];
            return $response;
        }
    }
}
