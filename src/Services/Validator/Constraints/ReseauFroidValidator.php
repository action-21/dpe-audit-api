<?php

namespace App\Services\Validator\Constraints;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Repository\ReseauFroidRepository;
use Symfony\Component\Validator\{Constraint, ConstraintValidator};
use Symfony\Component\Validator\Exception\{UnexpectedTypeException, UnexpectedValueException};

final class ReseauFroidValidator extends ConstraintValidator
{
    public function __construct(private ReseauFroidRepository $repository) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ReseauFroid) {
            throw new UnexpectedTypeException($constraint, ReseauFroid::class);
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
