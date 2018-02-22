<?php
/**
 * Part of the evias/nem-php package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under MIT License.
 *
 * This source file is subject to the MIT License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    evias/nem-php
 * @version    1.0.0
 * @author     Grégory Saive <greg@evias.be>
 * @author     Robin Pedersen (https://github.com/RobertoSnap)
 * @license    MIT License
 * @copyright  (c) 2017-2018, Grégory Saive <greg@evias.be>
 * @link       http://github.com/evias/nem-php
 */
namespace NEM\Models\Transaction;

use NEM\Models\Transaction;
use NEM\Models\TransactionType;
use NEM\Models\Account;
use NEM\Models\Fee;

class NamespaceProvision
    extends Transaction
{
    /**
     * List of additional fillable attributes
     *
     * @var array
     */
    protected $appends = [
        "rentalFeeSink"     => "transaction.rentalFeeSink",
        "rentalFee"         => "transaction.rentalFee",
        "parent"            => "transaction.parent",
        "newPart"           => "transaction.newPart",
    ];

    /**
     * Return specialized fields array for Namespace Provision Transactions.
     *
     * @return array
     */
    public function extend() 
    {
        return [
            "rentalFeeSink" => $this->rentalFeeSink()->address()->toClean(),
            "rentalFee" => empty($this->parent) ? Fee::ROOT_PROVISION_NAMESPACE : Fee::SUB_PROVISION_NAMESPACE,
            "parent" => $this->parent,
            "newPart" => $this->newPart,
            // transaction type specialization
            "type" => TransactionType::PROVISION_NAMESPACE,
        ];
    }

    /**
     * The extendFee() method must be overloaded by any Transaction Type
     * which needs to extend the base FEE to a custom FEE.
     *
     * @return array
     */
    public function extendFee()
    {
        if (!empty($this->parent)) {
            return Fee::SUB_PROVISION_NAMESPACE;
        }

        return Fee::ROOT_PROVISION_NAMESPACE;
    }

    /**
     * Mutator for the `rentalFeeSink` relation.
     *
     * @return  \NEM\Models\Account
     */
    public function rentalFeeSink($address = null)
    {
        return new Account(["address" => $address ?: $this->getAttribute("rentalFeeSink")]);
    }
}
