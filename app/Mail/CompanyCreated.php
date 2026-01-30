<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompanyCreated extends Mailable
{
    use Queueable, SerializesModels;

    public Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function build(): self
    {
        return $this->subject('New company created')
            ->view('emails.company-created', [
                'company' => $this->company,
            ]);
    }
}
