<?php

return [
    // Messaging settings (super admin)
    'messaging'              => 'Messaging (SMS / WhatsApp)',
    'messaging_desc'         => 'Keys are stored encrypted. Used to send SMS and WhatsApp messages for offices that activated the addon.',
    'provider'               => 'Provider',
    'sid'                    => 'Account SID',
    'token'                  => 'Auth Token',
    'sms_from'               => 'Sender number (SMS)',
    'whatsapp_from'          => 'Sender number (WhatsApp)',
    'test_connection'        => 'Test connection',
    'connection_ok'          => 'Connected to messaging provider ✓',
    'connection_failed'      => 'Connection failed — check your keys',
    'not_configured'         => 'Messaging gateway is not configured yet',
    'saved'                  => 'Messaging settings saved',

    // Provider selection (per channel)
    'sms_provider'           => 'SMS provider',
    'whatsapp_provider'      => 'WhatsApp provider',
    'provider_twilio'        => 'Twilio',
    'provider_meta'          => 'Meta WhatsApp Cloud (official)',
    'provider_egypt'         => 'Egyptian SMS gateway (HTTP)',
    'provider_vonage'        => 'Vonage',
    'meta_token'             => 'Access Token',
    'meta_phone_id'          => 'Phone Number ID',
    'eg_url'                 => 'API URL',
    'eg_method'              => 'Request method',
    'eg_username'            => 'Username',
    'eg_password'            => 'Password',
    'eg_sender'              => 'Sender ID',
    'eg_lang'                => 'Message language (1=English, 2=Arabic)',
    'vonage_key'             => 'API Key',
    'vonage_secret'          => 'API Secret',
    'vonage_from'            => 'Sender name/number',
    'test_sms'               => 'Test SMS',
    'test_whatsapp'          => 'Test WhatsApp',
    'test_telegram'          => 'Test Telegram',

    // Telegram
    'telegram'               => 'Telegram (free)',
    'telegram_enable'        => 'Enable Telegram channel',
    'telegram_desc'          => 'A free reminder channel. The client must link their account first via the "Link Telegram" button on the clients page.',
    'tg_bot_token'           => 'Bot Token',
    'tg_bot_username'        => 'Bot username (without @)',
    'tg_webhook_secret'      => 'Webhook Secret',
    'tg_register_webhook'    => 'Register webhook',
    'tg_webhook_ok'          => 'Telegram webhook registered ✓',
    'tg_webhook_failed'      => 'Failed to register webhook',
    'tg_link'                => 'Link Telegram',
    'tg_no_bot'              => 'Set the bot username in messaging settings first',
    'tg_link_ready'          => 'Copy this link and send it to the client to open and press Start',

    // Gating / general
    'requires_addon'         => 'This feature requires activating the addon first.',

    // SMS / WhatsApp message bodies
    'sms_hearing_reminder'   => 'Reminder: you have a hearing for case :case on :date — :location. (:app)',
    'wa_hearing_reminder'    => "🔔 Hearing reminder\nCase: :case\nDate: :date\nLocation: :location\n\n:app",
    'wa_invoice'             => "🧾 Invoice #:number for :amount\nStatus: :status\n\n:app",

    // E-signature
    'esign'                  => 'E-Signature',
    'esign_send'             => 'Send for signature',
    'esign_status'           => 'Signing status',
    'esign_status_none'      => 'None',
    'esign_status_pending'   => 'Pending signature',
    'esign_status_signed'    => 'Signed',
    'esign_status_rejected'  => 'Rejected',
    'esign_sent'             => 'Signature request sent to the client',
    'esign_no_client'        => 'No client linked to this document',
    'esign_invalid_link'     => 'Signing link is invalid or expired',
    'esign_already_signed'   => 'This document has already been signed',
    'esign_page_title'       => 'Sign Document',
    'esign_instructions'     => 'Please review the document then sign in the box below.',
    'esign_sign_here'        => 'Sign here',
    'esign_clear'            => 'Clear',
    'esign_submit'           => 'Confirm signature',
    'esign_success'          => 'Document signed successfully. Thank you.',
    'esign_notify_subject'   => 'A document awaits your signature',
    'esign_notify_body'      => 'You have a document that needs your e-signature. Tap the link to sign from your phone.',
    'esign_notify_action'    => 'Sign document',

    // Advanced AI
    'ai_advanced'            => 'Advanced AI',
    'ai_draft_memo'          => 'Draft legal memo',
    'ai_compare_contracts'   => 'Compare two contracts',
    'ai_select_second_doc'   => 'Select the second document to compare',
    'ai_predict'             => 'Predict case outcome',
    'ai_predict_disclaimer'  => 'This is an AI advisory estimate and does not replace professional legal advice.',
    'ai_prediction_result'   => 'Case outcome prediction',

    // Advanced reports
    'reports'                => 'Reports & Analytics',
    'reports_generate'       => 'Generate report',
    'reports_export_pdf'     => 'Export PDF',
    'reports_type'           => 'Report type',
    'reports_date_from'      => 'From date',
    'reports_date_to'        => 'To date',
    'reports_financial'      => 'Financial report (revenue & payments)',
    'reports_cases'          => 'Cases report (by status & type)',
    'reports_lawyers'        => 'Lawyer performance',

    // PWA
    'pwa_install'            => 'Install app',

    // Phase 6 — e-invoice / court
    'einvoice_qr'           => 'Invoice QR code',
    'einvoice_qr_hint'      => 'This is the compliant QR payload (TLV/Base64) used when printing the e-invoice.',
    'court_sync'            => 'Sync from court',
    'court_not_configured'  => 'Court portal integration is not enabled yet — it requires a formal access agreement. Enter case data manually for now.',

    // Smart templates
    'tpl_smart_generate'     => 'Smart generate from template',
    'tpl_generated'          => 'Document generated from template ✓',

    // Time tracking & billing
    'time_entries'           => 'Time entries',
    'time_add'               => 'Log time',
    'time_minutes'           => 'Minutes',
    'time_rate'              => 'Hourly rate',
    'time_description'       => 'Description',
    'time_occurred_at'       => 'Date',
    'time_billed'            => 'Billed',
    'time_amount'            => 'Amount',
    'time_invoice_unbilled'  => 'Invoice unbilled time',
    'time_no_unbilled'       => 'No unbilled time entries',
    'time_invoice_created'   => 'Invoice created from time ✓',

    // Fee installments
    'inst_plan'              => 'Installment plan',
    'inst_create'            => 'Create installment plan',
    'inst_count'             => 'Number of installments',
    'inst_first_due'         => 'First installment date',
    'inst_interval'          => 'Days between installments',
    'inst_created'           => 'Installment plan created ✓',
    'inst_amount'            => 'Installment amount',
    'inst_due_date'          => 'Due date',
    'inst_status'            => 'Status',
    'inst_status_pending'    => 'Due',
    'inst_status_paid'       => 'Paid',
    'inst_status_overdue'    => 'Overdue',
    'inst_mark_paid'         => 'Mark paid',
    'inst_installments'      => 'Installments',
    'inst_already'           => 'This invoice already has an installment plan',
];
