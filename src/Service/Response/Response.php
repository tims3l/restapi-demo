<?php
declare(strict_types=1);

namespace App\Service\Response;

use JsonSerializable;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Response implements ResponseInterface, JsonSerializable {
    
    private bool $success = true;
    private mixed $data;
    private array $errors = [];

    public function __construct(mixed $data, ConstraintViolationListInterface|array $errors = null)
    {
        $this->data = $data;
        $this->handleErrors($errors);
    }

    private function handleErrors(ConstraintViolationListInterface|array $errors = null): void
    {
        if (isset($errors)) {

            if ($errors instanceof ConstraintViolationListInterface) {
                $this->handleConstraintViolationErrors($errors);
            }

            if (is_array($errors)) {
                $this->handleGeneralErrors($errors);
            }
        }
    }

    private function handleConstraintViolationErrors(ConstraintViolationListInterface $errors): void
    {
        if ($errors->count() > 0) {
            $this->success = false;
        }

        foreach ($errors as $error) {
            $this->errors[$error->getPropertyPath()] = $error->getMessage();
        }
    }
    
    private function handleGeneralErrors(array $errors): void
    {
        if (!empty($errors)) {
            $this->success = false;
        }

        foreach ($errors as $field => $error) {
            $this->errors[$field] = $error;
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'success' => $this->success,
            'data' => $this->data,
            'errors' => $this->errors,
        ];
    }
}
