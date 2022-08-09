<?php

namespace Ikoncept\Fabriq\Mail;

use Ikoncept\Fabriq\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class AccountInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public Invitation $invitation;

    public string $inviteUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $signedURL = URL::temporarySignedRoute('invitation.accept', now()->addHours(48), [$this->invitation->uuid]);

        $this->inviteUrl = $signedURL;

        /** @var view-string $viewString * */
        $viewString = 'vendor.mail.account-invitation';

        return $this->replyTo($this->invitation->invitedBy->email)
            ->subject('Acceptera din inbjudan till Fabriq CMS - '.config('app.name'))
            ->markdown($viewString);
    }
}
