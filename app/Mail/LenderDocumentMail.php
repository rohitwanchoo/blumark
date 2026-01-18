<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\LenderDistribution;
use App\Models\LenderDistributionItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LenderDocumentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Pre-computed values stored as simple strings to survive serialization
    protected string $lenderCompanyName;
    protected string $lenderFullName;
    protected string $documentName;
    protected ?string $outputPath;
    protected array $templateData;

    public function __construct(
        public LenderDistribution $distribution,
        public LenderDistributionItem $item,
        public string $senderName,
        public string $senderCompany,
        public bool $attachPdf = true,
        public ?EmailTemplate $template = null,
    ) {
        // Pre-compute all values that depend on model methods BEFORE serialization
        // This ensures we don't call methods on deserialized models that may not work correctly
        $this->lenderCompanyName = $this->item->getLenderCompanyName();
        $this->lenderFullName = $this->item->getLenderFullName();
        $this->documentName = $this->item->getSourceFilename();
        $this->outputPath = $this->item->watermarkJob?->getOutputFullPath();

        // Prepare template data for placeholder replacement
        $this->templateData = [
            'lender_name' => $this->lenderCompanyName,
            'lender_contact' => $this->lenderFullName,
            'sender_name' => $this->senderName,
            'sender_company' => $this->senderCompany,
            'document_name' => $this->documentName,
        ];
    }

    public function envelope(): Envelope
    {
        $subject = $this->template
            ? $this->template->renderSubject($this->templateData)
            : "Document from {$this->senderCompany}: {$this->documentName}";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $downloadUrl = null;
        if (!$this->attachPdf) {
            $downloadUrl = route('distributions.item.download', [
                'distribution' => $this->distribution->id,
                'item' => $this->item->id,
            ]);
        }

        // Use template body if provided
        $customBody = $this->template
            ? $this->template->renderBody($this->templateData)
            : null;

        return new Content(
            view: 'emails.lender-document',
            with: [
                'lenderName' => $this->lenderFullName,
                'lenderCompany' => $this->lenderCompanyName,
                'senderName' => $this->senderName,
                'senderCompany' => $this->senderCompany,
                'documentName' => $this->documentName,
                'downloadUrl' => $downloadUrl,
                'attachPdf' => $this->attachPdf,
                'customBody' => $customBody,
            ],
        );
    }

    public function attachments(): array
    {
        if (!$this->attachPdf) {
            return [];
        }

        if (!$this->outputPath || !file_exists($this->outputPath)) {
            return [];
        }

        $filename = pathinfo($this->documentName, PATHINFO_FILENAME)
            . '-' . str_replace(' ', '-', strtolower($this->lenderCompanyName))
            . '-watermarked.pdf';

        return [
            Attachment::fromPath($this->outputPath)
                ->as($filename)
                ->withMime('application/pdf'),
        ];
    }
}
