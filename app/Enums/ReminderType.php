<?php

namespace App\Enums;

enum ReminderType: string
{
    case Salary = 'salary';
    case CreditorPayment = 'creditor_payment';
    case BillDue = 'bill_due';
    case Subscription = 'subscription';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Salary => 'Salary / income',
            self::CreditorPayment => 'Pay creditor / lender',
            self::BillDue => 'Bill due',
            self::Subscription => 'Subscription renewal',
            self::Custom => 'Custom reminder',
        };
    }

    public function defaultTitle(): string
    {
        return match ($this) {
            self::Salary => 'Salary payment expected',
            self::CreditorPayment => 'Creditor payment due',
            self::BillDue => 'Bill payment due',
            self::Subscription => 'Subscription renewal',
            self::Custom => 'Financial reminder',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Salary => 'text-bg-success',
            self::CreditorPayment => 'text-bg-warning',
            self::BillDue => 'text-bg-danger',
            self::Subscription => 'text-bg-info',
            self::Custom => 'text-bg-secondary',
        };
    }
}
