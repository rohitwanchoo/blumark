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
use Illuminate\Support\Collection;

class LenderDocumentMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Pre-computed values stored as simple types to survive serialization
    protected string $lenderCompanyName;
    protected string $lenderFullName;
    protected array $documentNames;
    protected array $attachmentData; // Array of ['path' => ..., 'filename' => ...]
    protected array $templateData;
    protected array $itemIds;
    protected ?string $fromEmail = null;
    protected ?string $fromName = null;

    /**
     * Create a new message instance.
     *
     * @param LenderDistribution $distribution
     * @param Collection|LenderDistributionItem[] $items - All items for a single lender
     * @param string $senderName
     * @param string $senderCompany
     * @param bool $attachPdf
     * @param EmailTemplate|null $template
     * @param string|null $fromEmail
     * @param string|null $fromName
     */
    public function __construct(
        public LenderDistribution $distribution,
        Collection $items,
        public string $senderName,
        public string $senderCompany,
        public bool $attachPdf = true,
        public ?EmailTemplate $template = null,
        ?string $fromEmail = null,
        ?string $fromName = null,
    ) {
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        // Get lender info from first item (all items are for same lender)
        $firstItem = $items->first();
        $this->lenderCompanyName = $firstItem->getLenderCompanyName();
        $this->lenderFullName = $firstItem->getLenderFullName();

        // Store item IDs for marking as sent later
        $this->itemIds = $items->pluck('id')->toArray();

        // Collect all document names and attachment data
        $this->documentNames = [];
        $this->attachmentData = [];

        foreach ($items as $item) {
            $docName = $item->getSourceFilename();
            $this->documentNames[] = $docName;

            $outputPath = $item->watermarkJob?->getOutputFullPath();
            if ($outputPath && file_exists($outputPath)) {
                $filename = pathinfo($docName, PATHINFO_FILENAME)
                    . '-' . str_replace(' ', '-', strtolower($this->lenderCompanyName))
                    . '-watermarked.pdf';

                $this->attachmentData[] = [
                    'path' => $outputPath,
                    'filename' => $filename,
                ];
            }
        }

        // Create document name string for template
        $documentNameStr = count($this->documentNames) === 1
            ? $this->documentNames[0]
            : count($this->documentNames) . ' documents';

        // Prepare template data for placeholder replacement
        $this->templateData = [
            'application_name' => $this->distribution->name ?? '',
            'lender_name' => $this->lenderCompanyName,
            'lender_contact' => $this->lenderFullName,
            'sender_name' => $this->senderName,
            'sender_company' => $this->senderCompany,
            'document_name' => $documentNameStr,
        ];
    }

    public function envelope(): Envelope
    {
        $subject = $this->template
            ? $this->template->renderSubject($this->templateData)
            : "Documents from {$this->senderCompany}: {$this->templateData['document_name']}";

        // Log for debugging
        \Log::info('LenderDocumentMail envelope', [
            'fromEmail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'subject' => $subject,
        ]);

        // Set custom from address if provided
        if ($this->fromEmail) {
            return new Envelope(
                from: new \Illuminate\Mail\Mailables\Address($this->fromEmail, $this->fromName ?? $this->fromEmail),
                subject: $subject,
            );
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $downloadUrl = null;
        // For download links, we'll point to the distribution page
        if (!$this->attachPdf) {
            $downloadUrl = route('distributions.show', $this->distribution->id);
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
                'documentName' => $this->templateData['document_name'],
                'documentNames' => $this->documentNames,
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

        $attachments = [];
        foreach ($this->attachmentData as $data) {
            if (file_exists($data['path'])) {
                $attachments[] = Attachment::fromPath($data['path'])
                    ->as($data['filename'])
                    ->withMime('application/pdf');
            }
        }

        return $attachments;
    }

    /**
     * Get the item IDs that this email covers
     */
    public function getItemIds(): array
    {
        return $this->itemIds;
    }
}
