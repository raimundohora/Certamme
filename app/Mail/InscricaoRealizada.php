<?php
namespace App\Mail;

use App\Models\Inscricao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InscricaoRealizada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inscricao $inscricao) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Inscrição realizada — ' . $this->inscricao->concurso->nome);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inscricao-realizada');
    }
}
