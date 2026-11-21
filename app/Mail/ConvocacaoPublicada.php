<?php
namespace App\Mail;

use App\Models\Convocacao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConvocacaoPublicada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Convocacao $convocacao) {}

    public function envelope(): Envelope
    {
        $concurso = $this->convocacao->inscricao->concurso->nome ?? 'concurso';

        return new Envelope(subject: 'Convocação — ' . $concurso);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.convocacao-publicada');
    }
}
