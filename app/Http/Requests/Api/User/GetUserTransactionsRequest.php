<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Entity\DTO\Filter\GetTransactionsFilter;
use App\Models\Enum\TransactionStatus;
use App\Models\Enum\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetUserTransactionsRequest extends FormRequest
{
    private const TRANSACTION_STATUS_HIDDEN_FOR_USER = [
        TransactionStatus::Pending,
    ];

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'required', 'int', 'min:0'],
            'per_page' => ['sometimes', 'required', 'int', 'min:1', 'max:30'],

            'product_name' => ['sometimes', 'required', 'string', 'min:1'],
            'type' => ['sometimes', 'required', 'string', Rule::enum(TransactionType::class)],
            'status' => ['sometimes', 'required', 'string', Rule::enum(TransactionStatus::class)],
        ];
    }

    public function getFilter(): GetTransactionsFilter
    {
        $validatedParameters = $this->validated();

        $filter = (new GetTransactionsFilter())
            ->setUserId($this->user()->getId())
        ;

        if (isset($validatedParameters['page'])) {
            $filter->setPage((int) $validatedParameters['page']);
        }

        if (isset($validatedParameters['per_page'])) {
            $filter->setPerPage((int) $validatedParameters['per_page']);
        }

        if (isset($validatedParameters['product_name'])) {
            $filter->setName($validatedParameters['product_name']);
        }

        if (isset($validatedParameters['type'])) {
            $filter->setType(TransactionType::from($validatedParameters['type']));
        }

        if (isset($validatedParameters['status'])) {
            $filter->setStatuses([TransactionStatus::from($validatedParameters['status'])]);
        } else {
            $filter->setStatuses($this->getVisibleTransactionStatuses());
        }

        return $filter;
    }

    public function messages(): array
    {
        return array_merge(
            parent::messages(),
            [
                'type' => sprintf(
                    'The provided value is invalid, please use one of the allowed values: %s',
                    implode(', ', TransactionType::getAllowedValues()),
                ),
                'status' => sprintf(
                    'The provided value is invalid, please use one of the allowed values: %s',
                    implode(', ', TransactionStatus::getAllowedValues()),
                ),
            ]
        );
    }

    private function getVisibleTransactionStatuses(): array
    {
        return array_filter(
            TransactionStatus::cases(),
            static function (TransactionStatus $status) {
                return !in_array($status, self::TRANSACTION_STATUS_HIDDEN_FOR_USER, true);
            }
        );
    }
}
