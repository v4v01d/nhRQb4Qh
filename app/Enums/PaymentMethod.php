<?php
namespace App\Enums;

enum PaymentMethod: string
{
    case OnDelivery = 'OnDelivery';
    case Card = 'Card';
    case Paypal = 'Paypal';
}