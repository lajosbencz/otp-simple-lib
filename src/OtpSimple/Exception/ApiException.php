<?php

namespace OtpSimple\Exception;


use OtpSimple\Exception;
use Throwable;

class ApiException extends Exception
{
    const CODES = [
        5000 => 'General error code.',
        5302 => 'The incoming request contains a signature (“signature”) that is not adequate. (The signature of the request received by the merchant API was not successfully checked.)',
        5304 => 'Asynchronous request failed due to timeout.',
        5305 => 'Forwarding the transaction to the payment system (accepting/acquirer side) is not successful.',
        5306 => 'Creating the transaction failed',
        5307 => 'The currency (“currency”) provided in the request does not match the one set in the account.',
        5308 => 'The two-step transaction received in the request is not permitted in the merchant account',
        5201 => 'The identifier of the merchant account (“merchant”) is missing.',
        5213 => 'The transaction identifier of the merchant ("orderRef") is missing.',
        5219 => 'The e-mail address ("customerEmail") is missing or not in an e-mail format.',
        5223 => 'The currency ("currency") of the transaction is not adequate or missing.',
        5220 => 'The language ("language") of the transaction is not adequate',
        5324 => 'The list of items ("items") and the total amount of the transaction ("total”) are missing.',
        5309 => 'Recipient missing in the invoicing details ("name" in case of natural persons and "company" in case of legal persons).',
        5310 => 'In the invoicing details, city is required.',
        5311 => 'In the invoicing details, postal code is required.',
        5312 => 'In the invoicing details, the first line of the address is required.',
        5313 => 'In the list of items to be purchased ("items”), the name of the item ("title") is required.',
        5314 => 'In the list of items to be purchased ("items”), the unit price of the item (“price") is required.',
        5315 => 'In the list of items to be purchased ("items”), the ordered amount (“amount") must be a positive integer.',
        5316 => 'In the shipping details, the recipient is required ("name" in case of natural persons and "company" in case of legal persons).',
        5317 => 'In the shipping details, city is required.',
        5318 => 'In the shipping details, postal code is required.',
        5319 => 'In the shipping details, the first line of the address is required.',
        5320 => 'The name and version number of the requesting client program ("sdkVersion") are required.',
        5325 => 'At least one of the fields that control redirection must be sent {can be set (a) "url" - for all the cases or (b) "urls": for different events separately}.',
        5323 => 'The transaction amount to be finalised is not adequate. (The optionally provided "approveTotal" value must be a value between 0 and the original transaction amount; in a Finish operation.)',
        5110 => 'The amount to be refunded is not adequate. (The optionally provided "refundTotal" value cannot be negative and cannot exceed the current total refundable amount.)',
        5111 => 'Sending either the orderRef or the transactionId is required',
        5113 => 'The name and version number of the requesting client program ("sdkVersion") are required.',
        5327 => 'Maximum number (50) of merchant transaction identifiers ("orderRefs") to be queried is exceeded.',
        5328 => 'Maximum number (50) of SimplePay transaction identifiers ("transactionIds") to be queried is exceeded.',
        5329 => 'In the transaction initiation period to be queried the “from” must precede the “until” time.',
        5330 => 'In the transaction initiation period to be queried the “from” and the “until” must be provided together.',
        5339 => 'In connection with the transactions to be queried, either the initiation period (“from” and “until”) or the list of identifiers (“orderRefs” or “transactionIds”) must be provided.',
        5010 => 'Account cannot be found.',
        5011 => 'Transaction cannot be found',
        5012 => 'Account does not match',
        5013 => 'Transaction already exists (and is not marked as one that can be reinitiated).',
        5014 => 'The type of the transaction is not appropriate',
        5015 => 'Transaction with ongoing payment',
        5016 => 'Transaction timeout (in case of requests received from the accepting/acquirer side).',
        5017 => 'Transaction cancelled (in case of requests received from the accepting/acquirer side).',
        5018 => 'Transaction has been paid (no new operation can be initiated).',
        5020 => 'Checking the value provided in the request or the original transaction amount (“originalTotal”) failed',
        5021 => 'The transaction has been closed (therefore no new Finish operation can be initiated).',
        5022 => 'The transaction is not in the expected state required for the request.',
        5030 => 'Operation not permitted',
        5023 => 'Unknown account currency.',
        5026 => 'Transaction denied (as result of an unsuccessful fraud inspection).',
        5321 => 'Format error',
        5322 => 'Incorrect country code.',
        5337 => 'Error when complex data is transcribed into text.',
        999 => 'Other error',
    ];

    public function __construct(array $codes, ?Throwable $previous = null)
    {
        $code = 0;
        $message = '';
        foreach ($codes as $c) {
            if (array_key_exists($c, self::CODES)) {
                if (!$code) {
                    $code = $c;
                }
                $message .= self::CODES[$c] . ' | ';
            }
        }
        $message = substr($message, 0, -3);
        parent::__construct($message, $code, $previous);
    }
}
