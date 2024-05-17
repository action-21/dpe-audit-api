<?php

namespace App\Database\Opendata\MasqueLointain;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\Uuid;
use App\Domain\MasqueLointain\Enum\SecteurOrientation;
use App\Domain\MasqueLointain\Enum\TypeMasqueLointain;
use App\Domain\MasqueLointain\Table\{Fe2, Fe2Repository};
use App\Domain\MasqueLointain\ValueObject\{HauteurAlpha, OrientationMasque};

final class XMLMasqueLointainHomogeneReader extends XMLReaderIterator
{
    public function __construct(private Fe2Repository $table_fe2_repository,)
    {
    }

    public function type_masque_lointain(): TypeMasqueLointain
    {
        return TypeMasqueLointain::MASQUE_LOINTAIN_HOMOGENE;
    }

    public function tv_coef_masque_lointain_homogene_id(): int
    {
        return (int) $this->get()->findOneOrError('.//tv_coef_masque_lointain_homogene_id')->getValue();
    }

    public function enum_orientation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_orientation_id')->getValue();
    }

    public function enum_orientation(): ?Orientation
    {
        return Orientation::try_from_enum_orientation_id($this->enum_orientation_id());
    }

    public function table_fe2(): Fe2
    {
        $table = $this->table_fe2_repository
            ->search_by(
                orientation: $this->enum_orientation(),
                tv_coef_masque_lointain_homogene_id: $this->tv_coef_masque_lointain_homogene_id()
            )
            ->first();

        if (null === $table) {
            throw new \RuntimeException('Table Fe2 non trouvée');
        }
        return $table;
    }

    // Données déduites

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function description(): string
    {
        return 'Masque lointain homogène non décrit';
    }

    public function hauteur_alpha(): HauteurAlpha
    {
        return HauteurAlpha::from($this->table_fe2()->hauteur_alpha_defaut);
    }

    public function orientation(): OrientationMasque
    {
        return OrientationMasque::from($this->enum_orientation()->to_azimut());
    }

    public function secteur_orientation(): ?SecteurOrientation
    {
        return null;
    }

    public function read(XMLMasqueLointainReader $reader): self
    {
        $this->array = $reader->tv_coef_masque_lointain_homogene_id() ? [$reader->get()] : [];
        return $this;
    }
}
