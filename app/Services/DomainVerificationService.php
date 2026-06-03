<?php

namespace App\Services;

use App\Models\Office;
use RuntimeException;

class DomainVerificationService
{
    /**
     * Check whether the office's verify token exists in a DNS TXT record for its custom domain.
     *
     * Returns true on success, false when the record is not found yet.
     * Throws RuntimeException with a user-facing Arabic message on configuration errors.
     */
    public function verify(Office $office): bool
    {
        if (! $office->custom_domain) {
            throw new RuntimeException('لم يتم إدخال دومين بعد — أدخله واحفظ أولاً.');
        }

        if (! $office->domain_verify_token) {
            throw new RuntimeException('رمز التحقق غير موجود — احفظ الدومين أولاً لإنشاء الرمز.');
        }

        // TXT records are almost always on the apex domain (without www)
        $apex = preg_replace('/^www\./i', '', $office->custom_domain);

        $records = @dns_get_record($apex, DNS_TXT);

        if (! is_array($records)) {
            return false;
        }

        foreach ($records as $record) {
            // Different DNS extensions return txt in different keys
            $txt = $record['txt'] ?? ($record['entries'][0] ?? '');
            if ($txt === $office->domain_verify_token) {
                return true;
            }
        }

        return false;
    }
}
