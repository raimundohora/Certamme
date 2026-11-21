<?php
namespace App\Mail;

use App\Models\Recurso;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecursoRespondido extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Recurso $recurso) {}

    public function envelope(): Envelope
    {
        $concurso = $this->recurso->inscricao->concurso->nome ?? 'concurso';
        return new Envelope(subject: 'Resposta ao seu recurso — ' . $concurso);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.recurso-respondido');
    }
}
