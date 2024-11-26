# Moteur de calcul de la performance énergétique des logements (PCL) - API

> [!NOTE]
> Ce dépôt est une base de code du projet accessible [ici](https://github.com/action-21/reno-audit).

> [!IMPORTANT]
> Ce projet est en cours de développement.

## Installation

```
git clone https://github.com/action-21/reno-audit-api
cd reno-audit-api
composer install
symfony server:start
```

## Compatibilité open data

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

### Tests unitaires des calculs intermédiaires

L'ensemble des calculs intermédiaires de la méthode conventionnelle est intégré aux différents moteurs de calcul sous la forme de fonctions simples sans effet de bord afin de faciliter les tests.

Les tests sont écrits pour chaque domaine au format yaml dans le dossier /etc/calculs :

```
# /etc/calculs/ventilation.yaml
performance:
  debit:
    - type_systeme: VENTILATION_NATURELLE
      mode_extraction: null
      mode_insufflation: HYGROREGLABLE
      presence_echangeur: null
      systeme_collectif: null
      annee_installation: 1900
      qvarep_conv: 2.23
      qvasouf_conv: 0
      smea_conv: 3
```
