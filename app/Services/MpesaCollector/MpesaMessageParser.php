<?php

namespace App\Services\MpesaCollector;

use Carbon\Carbon;
use Throwable;

class MpesaMessageParser
{
    private const VERSION = '1.1.0';

    /**
     * Parse an original M-Pesa SMS.
     */
    public function parse(string $rawMessage): ?array
    {
        $message = $this->normalize($rawMessage);

        if ($message === '') {
            return null;
        }

        // Agent withdrawal
        $withdrawal = $this->parseAgentWithdrawal($message);

        if ($withdrawal !== null) {
            return $withdrawal;
        }

        // PayBill
        $paybill = $this->parsePayBill($message);

        if ($paybill !== null) {
            return $paybill;
        }

        // Buy Goods / Till
$buyGoods = $this->parseBuyGoods($message);

if ($buyGoods !== null) {
    return $buyGoods;
}

// Receive Money
$receiveMoney = $this->parseReceiveMoney($message);

if ($receiveMoney !== null) {
    return $receiveMoney;
}

$result = $this->parseMshwariTransfer($message);

if ($result) {
    return $result;
}



        // Send Money
        $sendMoney = $this->parseSendMoney($message);

        if ($sendMoney !== null) {
            return $sendMoney;
        }

        return null;
    }

    /**
     * Normalize whitespace without changing the original SMS.
     */
    private function normalize(string $message): string
    {
        $message = str_replace(
            ["\xC2\xA0", "\r", "\n"],
            [' ', ' ', ' '],
            $message
        );

        return trim(preg_replace('/\s+/u', ' ', $message));
    }

    /**
     * Parse Send Money messages.
     */
    private function parseSendMoney(string $message): ?array
    {
        $pattern = '/^
            (?<receipt>[A-Z0-9]{8,12})
            \s+Confirmed\.\s*
            Ksh\s*(?<amount>[\d,]+(?:\.\d{2})?)
            \s+sent\s+to\s+
            (?<name>.+?)
            \s+(?<phone>0\d{9}|\+254\d{9})
            \s+on\s+
            (?<date>\d{1,2}\/\d{1,2}\/\d{2,4})
            \s+at\s+
            (?<time>\d{1,2}:\d{2}\s*[AP]M)
        /ix';

        if (!preg_match($pattern, $message, $matches)) {
            return null;
        }

        return [
            'receipt_number' => strtoupper($matches['receipt']),
            'transaction_type' => 'SEND_MONEY',
            'direction' => 'OUTGOING',
            'payment_channel' => 'MPESA',

            'amount' => $this->money($matches['amount']),

            'transaction_cost' => $this->extractAmount(
                '/Transaction\s+cost\s*,?\s*Ksh\s*([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'balance' => $this->extractAmount(
                '/New\s+M-PESA\s+balance\s+is\s+Ksh\s*([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'daily_limit_remaining' => $this->extractAmount(
                '/Amount\s+you\s+can\s+transact\s+within\s+the\s+day\s+is\s+([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'counterparty_name' => trim($matches['name']),
            'counterparty_phone' => $matches['phone'],

            'shortcode' => null,
            'account_reference' => null,

            'agent_number' => null,
            'agent_name' => null,
            'agent_outlet' => null,

            'transaction_at' => $this->parseDateTime(
                $matches['date'],
                $matches['time']
            ),

            'parsed_data' => [
                'format' => 'send_money',
            ],

            'parser_version' => self::VERSION,
            'processing_status' => 'processed',
            'processing_error' => null,
        ];
    }

    /**
     * Parse PayBill payments.
     *
     * Example:
     * UJT76B6D6B Confirmed. Ksh2,000.00 sent to LIONS EYE HOSPITAL
     * for account 102610294177 on 29/10/26 at 9:38 AM
     */
    private function parsePayBill(string $message): ?array
    {
        $pattern = '/^
            (?<receipt>[A-Z0-9]{8,12})
            \s+Confirmed\.\s*
            Ksh\s*(?<amount>[\d,]+(?:\.\d{2})?)
            \s+sent\s+to\s+
            (?<name>.+?)
            \s+for\s+account\s+
            (?<account_reference>.+?)
            \s+on\s+
            (?<date>\d{1,2}\/\d{1,2}\/\d{2,4})
            \s+at\s+
            (?<time>\d{1,2}:\d{2}\s*[AP]M)
        /ix';

        if (!preg_match($pattern, $message, $matches)) {
            return null;
        }

        return [
            'receipt_number' => strtoupper($matches['receipt']),
            'transaction_type' => 'PAYBILL',
            'direction' => 'OUTGOING',
            'payment_channel' => 'PAYBILL',

            'amount' => $this->money($matches['amount']),

            'transaction_cost' => $this->extractAmount(
                '/Transaction\s+cost\s*,?\s*Ksh\s*([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'balance' => $this->extractAmount(
                '/New\s+M-PESA\s+balance\s+is\s+Ksh\s*([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'daily_limit_remaining' => $this->extractAmount(
                '/Amount\s+you\s+can\s+transact\s+within\s+the\s+day\s+is\s+([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'counterparty_name' => trim($matches['name']),
            'counterparty_phone' => null,

            'shortcode' => null,
            'account_reference' => trim($matches['account_reference']),

            'agent_number' => null,
            'agent_name' => null,
            'agent_outlet' => null,

            'transaction_at' => $this->parseDateTime(
                $matches['date'],
                $matches['time']
            ),

            'parsed_data' => [
                'format' => 'paybill',
            ],

            'parser_version' => self::VERSION,
            'processing_status' => 'processed',
            'processing_error' => null,
        ];
    }


/**
 * Parse Buy Goods / Till payment messages.
 */
private function parseBuyGoods(string $message): ?array
{
    $pattern = '/^
        (?<receipt>[A-Z0-9]{8,12})
        \s+Confirmed\.\s*
        Ksh\s*(?<amount>[\d,]+(?:\.\d{2})?)
        \s+paid\s+to\s+
        (?<name>.+?)
        \s+on\s+
        (?<date>\d{1,2}\/\d{1,2}\/\d{2,4})
        \s+at\s+
        (?<time>\d{1,2}:\d{2}\s*[AP]M)
    /ix';

    if (!preg_match($pattern, $message, $matches)) {
        return null;
    }

    return [
        'receipt_number' => strtoupper($matches['receipt']),
        'transaction_type' => 'BUY_GOODS',
        'direction' => 'OUTGOING',
        'payment_channel' => 'BUY_GOODS',

        'amount' => $this->money($matches['amount']),

        'transaction_cost' => $this->extractAmount(
            '/Transaction\s+cost\s*,?\s*Ksh\s*([\d,]+(?:\.\d{2})?)/i',
            $message
        ),

        'balance' => $this->extractAmount(
            '/New\s+M-PESA\s+balance\s+is\s+Ksh\s*([\d,]+(?:\.\d{2})?)/i',
            $message
        ),

        'daily_limit_remaining' => $this->extractAmount(
            '/Amount\s+you\s+can\s+transact\s+within\s+the\s+day\s+is\s+([\d,]+(?:\.\d{2})?)/i',
            $message
        ),

        'counterparty_name' => trim($matches['name']),
        'counterparty_phone' => null,

        'shortcode' => null,
        'account_reference' => null,

        'agent_number' => null,
        'agent_name' => null,
        'agent_outlet' => null,

        'transaction_at' => $this->parseDateTime(
            $matches['date'],
            $matches['time']
        ),

        'parsed_data' => [
            'format' => 'buy_goods',
        ],

        'parser_version' => self::VERSION,
        'processing_status' => 'processed',
        'processing_error' => null,
    ];
}


/**
 * Parse incoming M-Pesa money received messages.
 */
private function parseReceiveMoney(string $message): ?array
{
    $pattern = '/^
        (?<receipt>[A-Z0-9]{8,12})
        \s*Confirmed\.\s*
        You\s+have\s+received\s+
        Ksh\s*(?<amount>[\d,]+(?:\.\d{2})?)
        \s+from\s+
        (?<name>.+?)
        \s+
        (?<phone>0\d{3}\*{3}\d{3}|0\d{9}|\+254\d{9})
        \s+on\s+
        (?<date>\d{1,2}\/\d{1,2}\/\d{2,4})
        \s+at\s+
        (?<time>\d{1,2}:\d{2}\s*[AP]M)
    /ix';

    if (!preg_match($pattern, $message, $matches)) {
        return null;
    }

    return [
        'receipt_number' => strtoupper($matches['receipt']),
        'transaction_type' => 'RECEIVE_MONEY',
        'direction' => 'INCOMING',
        'payment_channel' => 'MPESA',

        'amount' => $this->money($matches['amount']),

        'transaction_cost' => null,

        'balance' => $this->extractAmount(
            '/New\s+M-PESA\s+balance\s+is\s+Ksh\s*([\d,]+(?:\.\d{2})?)/i',
            $message
        ),

        'daily_limit_remaining' => null,

        'counterparty_name' => trim($matches['name']),
        'counterparty_phone' => $matches['phone'],

        'shortcode' => null,
        'account_reference' => null,

        'agent_number' => null,
        'agent_name' => null,
        'agent_outlet' => null,

        'transaction_at' => $this->parseDateTime(
            $matches['date'],
            $matches['time']
        ),

        'parsed_data' => [
            'format' => 'receive_money',
            'phone_masked' => str_contains($matches['phone'], '*'),
        ],

        'parser_version' => self::VERSION,
        'processing_status' => 'processed',
        'processing_error' => null,
    ];
}

private function parseMshwariTransfer(string $message): ?array
{
    $pattern = '/^
        (?<receipt>[A-Z0-9]{8,12})
        \s*Confirmed\.\s*
        Ksh\s*(?<amount>[\d,]+(?:\.\d{2})?)
        \s+transferred\s+from\s+M-Shwari\s+account
        \s+on\s+
        (?<date>\d{1,2}\/\d{1,2}\/\d{2,4})
        \s+at\s+
        (?<time>\d{1,2}:\d{2}\s*[AP]M)
        \.\s*
        M-Shwari\s+balance\s+is\s+Ksh\s*(?<mshwari_balance>[\d,]+(?:\.\d{2})?)
        \s*\.?\s*
        M-PESA\s+balance\s+is\s+Ksh\s*(?<mpesa_balance>[\d,]+(?:\.\d{2})?)
        \s*\.?\s*
        Transaction\s+cost\s+Ksh\.?\s*(?<cost>[\d,]+(?:\.\d{2})?)
    /ix';

    if (!preg_match($pattern, trim($message), $matches)) {
        return null;
    }

    return [
        'receipt_number' => strtoupper($matches['receipt']),
        'transaction_type' => 'MSHWARI_TRANSFER',
        'direction' => 'INCOMING',
        'payment_channel' => 'MSHWARI',
        'currency' => 'KES',
        'amount' => $this->money($matches['amount']),
        'transaction_cost' => $this->money($matches['cost']),
        'balance' => $this->money($matches['mpesa_balance']),
        'daily_limit_remaining' => null,
        'counterparty_name' => 'M-Shwari',
        'counterparty_phone' => null,
        'shortcode' => null,
        'account_reference' => null,
        'agent_number' => null,
        'agent_name' => null,
        'agent_outlet' => null,
        'transaction_at' => $this->parseDateTime(
            $matches['date'],
            $matches['time']
        ),
        'parsed_data' => [
            'format' => 'mshwari_transfer',
            'mshwari_balance' => $this->money(
                $matches['mshwari_balance']
            ),
        ],
        'parser_version' => self::VERSION,
        'processing_status' => 'processed',
        'processing_error' => null,
    ];
}




    /**
     * Parse M-Pesa agent withdrawal messages.
     */
    private function parseAgentWithdrawal(string $message): ?array
    {
        $pattern = '/
            ^(?<receipt>[A-Z0-9]{8,12})
            \s+Confirmed\.\s*
            on\s+
            (?<date>\d{1,2}\/\d{1,2}\/\d{2,4})
            \s+at\s+
            (?<time>\d{1,2}:\d{2}\s*[AP]M)
            \s*Withdraw\s+
            Ksh\s*(?<amount>[\d,]+(?:\.\d{2})?)
            \s+from\s+
            (?<agent_number>\d+)
            \s*-\s*
            (?<agent_description>.+?)
            (?=\s+Transaction\s+cost|\s+Amount\s+you\s+can\s+transact|\s*$)
        /ix';

        if (!preg_match($pattern, $message, $matches)) {
            return null;
        }

        return [
            'receipt_number' => strtoupper($matches['receipt']),
            'transaction_type' => 'AGENT_WITHDRAWAL',
            'direction' => 'OUTGOING',
            'payment_channel' => 'AGENT',

            'amount' => $this->money($matches['amount']),

            'transaction_cost' => $this->extractAmount(
                '/Transaction\s+cost\s*,?\s*Ksh\s*([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'balance' => $this->extractAmount(
                '/New\s+M-PESA\s+balance\s+is\s+Ksh\s*([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'daily_limit_remaining' => $this->extractAmount(
                '/Amount\s+you\s+can\s+transact\s+within\s+the\s+day\s+is\s+([\d,]+(?:\.\d{2})?)/i',
                $message
            ),

            'counterparty_name' => null,
            'counterparty_phone' => null,

            'shortcode' => null,
            'account_reference' => null,

            'agent_number' => $matches['agent_number'],
            'agent_name' => trim($matches['agent_description']),
            'agent_outlet' => null,

            'transaction_at' => $this->parseDateTime(
                $matches['date'],
                $matches['time']
            ),

            'parsed_data' => [
                'format' => 'agent_withdrawal',
            ],

            'parser_version' => self::VERSION,
            'processing_status' => 'processed',
            'processing_error' => null,
        ];
    }

    /**
     * Extract an optional monetary value.
     */
    private function extractAmount(
        string $pattern,
        string $message
    ): ?float {
        if (!preg_match($pattern, $message, $matches)) {
            return null;
        }

        return $this->money($matches[1]);
    }

    /**
     * Convert a monetary string into a numeric value.
     */
    private function money(string $value): float
    {
        return (float) str_replace(',', '', $value);
    }

    /**
     * Parse transaction date and time.
     */
    private function parseDateTime(
        string $date,
        string $time
    ): ?Carbon {
        $date = str_replace('-', '/', $date);

        $formats = [
            'd/m/y g:i A',
            'd/m/Y g:i A',
            'j/n/y g:i A',
            'j/n/Y g:i A',
        ];

        $value = $date . ' ' . strtoupper(trim($time));

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat(
                    $format,
                    $value,
                    config('app.timezone')
                );

                if ($parsed !== false) {
                    return $parsed;
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        return null;
    }
}
