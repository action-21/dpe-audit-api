# DPE-Audit API

> [!IMPORTANT]
> Ce projet est en cours de développement.

API du projet [DPE-Audit](https://github.com/action-21/dpe-audit).

## Installation

```
git clone https://github.com/action-21/dpe-audit-api
cd dpe-audit-api
composer install
symfony server:start
```

## Usage

**GET /audits** : Recherche de DPE-Audit disponibles dans l'opendata de l'ADEME.

**POST /audit** : Publie un audit énergétique conforme au [standard d'échange de données](https://github.com/action-21/dpe-audit-schema). Une simulation des performances est automatiquement effecuée pour chaque requête.

**GET /audit/{id}** : Retourne un audit énergétique existant depuis l'[observatoire DPE-Audit](https://observatoire-dpe-audit.ademe.fr/). Une simulation des performances est automatiquement effecuée pour chaque requête.

**PUT /audit/{id}** : 🚧 Remplace un audit énergétique existant.

**PATCH /audit/{id}/scenario** : Applique un scenario de travaux à un audit énergétique existant.

## Compatibilité

85% des DPE / audits réglementaires de l'open data de l'ADEME peuvent être reconstitués.

| Version modèle DPE-Audit |                                                                                                                                         Description                                                                                                                                          | Compatibilité |
| :----------------------: | :------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------: | :-----------: |
|            v1            | version allégée du modèle de donnée DPE sans le détail de la modélisation enveloppe et système pour le DPE logement existant et le DPE logement neuf. Ce type de DPE a été mis en place de manière transitoire au démarrage du nouvel observatoire. Les logiciels sont en cours d'évaluation |      ❌       |
|           v1.1           | version allégée du modèle de donnée DPE sans le détail de la modélisation enveloppe et système pour le DPE logement existant et le DPE logement neuf. Ce type de DPE a été mis en place de manière transitoire au démarrage du nouvel observatoire. Les logiciels sont en cours d'évaluation |      ❌       |
|            v2            |                                                             version complète du modèle DPE pour la partie logement existant. La partie de description de l'enveloppe et des systèmes est toujours optionnelle pour le DPE neuf.                                                              |      ❌       |
|           v2.1           |                          version complète du modèle DPE pour la partie logement existant. La partie de description de l'enveloppe et des systèmes est toujours optionnelle pour le DPE neuf. Les logiciels sont évalués. Les contrôles de cohérences sont effectués                          |      ✔️       |
|           v2.2           |                                                                      version du modèle DPE qui introduit de nouveaux champs obligatoires pour assurer une compatibilité de reprise des xml DPE pour réaliser un audit.                                                                       |      ✔️       |
|           v2.3           |                                                                          des corrections sont apportées pour rendre le modèle de DPE plus complet pour des usages de réimport de xml ADEME dans les logiciels DPE.                                                                           |      ✔️       |
|           v2.4           |                                                                                                                                              -                                                                                                                                               |      ✔️       |

## Tests

### Tests end-to-end

On fait tourner le moteur de calcul sur la base d'un échantillon de données issu de l'[opendata de l'ADEME](https://data.ademe.fr/datasets/dpe-v2-logements-existants) et on compare les données de sortie.

#### Échantillonage

- 1 échantillon par zone climatique (8 zones climatiques)
- 5 échantillons par période de construction (10 périodes de construction)
- Intervalle de dates d'établissement à 30 jours déterminé aléatoirement pour assurer le renouvellement de l'échantillonage

#### Données de sortie

- Déperditions des murs
- Déperditions des planchers bas
- Déperditions des planchers hauts
- Déperditions des baies
- Déperditions des portes
- Déperditions par nouvellement d'air
- Déperditions de l'enveloppe
- Surface sud équivalente
- Besoin de chauffage par scénario d'usage
- Besoin d'eau chaude sanitaire par scénario d'usage
- Besoin de refroidissement par scénario d'usage
- Consommation de chauffage par scénario d'usage
- Consommation d'eau chaude sanitaire par scénario d'usage
- Consommation de refroidissement par scénario d'usage
- Consommation d'éclairage
- Consommation des auxiliaires par scénario d'usage
- Consommation finale par scénario d'usage
- Consommation primaire par scénario d'usage
- Emission de gaz à effet de serre par scénario d'usage

## Documentation

- [Arrêté du 15 septembre 2006 relatif au diagnostic de performance énergétique pour les bâtiments ou parties de bâtiment autres que d'habitation existants proposés à la vente en France métropolitaine](https://www.legifrance.gouv.fr/loda/id/JORFTEXT000000788395/)

- [Arrêté du 31 mars 2021 relatif au diagnostic de performance énergétique pour les bâtiments ou parties de bâtiments à usage d'habitation en France métropolitaine](https://www.legifrance.gouv.fr/jorf/id/JORFTEXT000043353335)

- [Arrêté du 8 octobre 2021 modifiant la méthode de calcul et les modalités d'établissement du diagnostic de performance énergétique](https://www.legifrance.gouv.fr/jorf/id/JORFTEXT000044202205)

- [Guide à l'attention des diagnostiqueurs](https://www.planbatimentdurable.developpement-durable.gouv.fr/IMG/pdf/v2_guide_diagnostiqueurs_dpe_logement_2021.pdf)
