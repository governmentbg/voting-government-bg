<?php

/**
 * Helper function to send email
 *
 * @param string $template - name of the email template view
 * @param array $mailData - tha data to be passed to the email template
 * @param string $to - email address of the recipient
 * @param string $subject - email subject
 *
 * @return boolean - true on success, false on failure
 */
function sendEmail($template, $mailData, $to, $subject) {
    try {
        \Mail::send($template, $mailData, function ($m) use ($to, $subject) {
            $m->from(config('mail.from.address'), config('mail.from.name'));
            $m->to($to);
            $m->subject($subject);
        });
    } catch (\Exception $e) {
        logger()->error('Send email error: '. $e->getMessage());
        return false;
    }

    return empty(\Mail::failures());
}
