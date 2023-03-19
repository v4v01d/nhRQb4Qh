<?php
namespace App\Interfaces;

use App\Enums\PaymentMethod;
/**
 * Initially I went with interface approach, but then a large amount of common 
 * methods appeared and an abstract parent was created. Since Both interfaces and
 * abstract methods enforce implementation of functions and provide intellisense, using
 * both felt like overkill.
 */
interface IPaymentMethod
{
    public function Execute():bool;
    public function GetTransactionID():string;
    public function GetPaymentMethod():PaymentMethod;
    public function GetAmount():float;
    public function GetErrors():array;
    public function GetStatus():string;
    public function GetStatusCode():int;
}