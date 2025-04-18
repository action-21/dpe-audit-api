<?php

namespace App\Database\Opendata\Enveloppe\Lnc;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enum\Lnc\TypeLnc;

final class XMLEtsReader extends XMLReader implements XMLLncReader
{
    public function parois_opaques(): array
    {
        return [];
    }

    /**
     * @return XMLEtsBaieReader[]
     */
    public function baies(): array
    {
        $readers = [];

        foreach ($this->findMany('.//baie_ets_collection//baie_ets') as $item) {
            $reader = XMLEtsBaieReader::from($item);
            for ($i = 1; $i <= $reader->nb_baie(); $i++) {
                $readers[] = $reader;
            }
        }
        return $readers;
    }

    public function id(): Id
    {
        return $this->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Espace tampon solarisé non décrit';
    }

    public function type(): TypeLnc
    {
        return TypeLnc::ESPACE_TAMPON_SOLARISE;
    }
}
