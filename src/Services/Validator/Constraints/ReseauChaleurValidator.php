<?php

namespace App\Services\Validator\Constraints;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Chauffage\Repository\ReseauChaleurRepository;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\{UnexpectedTypeException, UnexpectedValueException};

final class ReseauChaleurValidator extends ConstraintValidator
{
    public function __construct(private ReseauChaleurRepository $repository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReseauChaleur) {
            throw new UnexpectedTypeException($constraint, ReseauChaleur::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        if (null === $this->repository->find(Id::from($value))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ id }}', $value)
                ->addViolation();
        }
    }
}
